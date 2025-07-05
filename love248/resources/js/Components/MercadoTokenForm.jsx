import React, { useState } from "react";
import __ from "@/Functions/Translate";
import axios from "axios";
import PrimaryButton from "@/Components/PrimaryButton";
import { toast } from "react-toastify";
import TextInput from "@/Components/TextInput";
import SecondaryButton from "./SecondaryButton";

export default function MercadoTokenForm({ tokenPack, onCancel }) {
  const [isLoading, setIsLoading] = useState(false);
  const [email, setEmail] = useState("");

  // Function to parse and format error messages
  const formatErrorMessage = (error) => {
    if (!error) return "Unknown error occurred";

    // Check if it's a JSON string error
    if (typeof error === "string" && error.includes("{")) {
      try {
        const parsedError = JSON.parse(error);
        return parsedError.error || parsedError.message || "Unknown API error";
      } catch (e) {
        return error;
      }
    }

    // Handle structured error objects
    if (error.details && error.details.message) {
      return error.details.message;
    }

    if (error.message) {
      return error.message;
    }

    return "Unknown error occurred";
  };

  const handleMercadoSubmit = async (e) => {
    e.preventDefault();
    setIsLoading(true);

    try {
      console.log(
        "Submitting payment request to:",
        route("token.mercado.purchase", { tokenPack: tokenPack.id })
      );

      const response = await axios.post(
        route("token.mercado.purchase", { tokenPack: tokenPack.id }),
        {
          email: email,
          tokPack: tokenPack.id,
        }
      );

      console.log("Payment response:", response.data);

      // Check if we have a successful response with init_point
      if (response.data && response.data.success && response.data.init_point) {
        console.log("Redirecting to init_point:", response.data.init_point);
        window.location.href = response.data.init_point;
      }
      // Check if we have a redirect URL
      else if (response.data && response.data.redirect) {
        console.log("Redirecting to redirect URL:", response.data.redirect);
        window.location.href = response.data.redirect;
      }
      // Handle direct string URL response
      else if (
        typeof response.data === "string" &&
        response.data.startsWith("http")
      ) {
        console.log("Redirecting to direct URL:", response.data);
        window.location.href = response.data;
      }
      // Handle error message in response
      else if (response.data && response.data.message) {
        console.error("Error in response:", response.data.message);
        toast.error(response.data.message);
        setIsLoading(false);
      }
      // Fallback error handling
      else {
        console.error("Unknown response format:", response.data);
        toast.error("Unknown response format. Please try again.");
        setIsLoading(false);
      }
    } catch (error) {
      console.error("Payment error:", error);
      console.error("Error response:", error.response?.data);

      // Extract error message from response if available
      const errorMessage =
        error.response?.data?.message ||
        error.message ||
        "Failed to process payment. Please try again.";

      toast.error(errorMessage);
      setIsLoading(false);
    }
  };

  return (
    <div className="bg-footer p-10 text-center">
      <h3 className="text-lg font-bold mb-4 text-gray-primary">
        {__("Purchase Tokens with Mercado Pago")}
      </h3>

      <p className="mb-4 text-gray-primary">
        {__(
          "Enter your email address to continue with your purchase of :tokens tokens for :price",
          {
            tokens: tokenPack.tokensFormatted,
            price: `${tokenPack.price} BRL`,
          }
        )}
      </p>

      <form onSubmit={handleMercadoSubmit} className="">
        <div className="form-group mb-4">
          <TextInput
            id="email"
            name="email"
            type="email"
            value={email}
            placeholder={__("Enter your email")}
            handleChange={(e) => setEmail(e.target.value)}
            className="form-control"
            required
          />
        </div>

        <div className="flex justify-center">
          <div className="iq-button">
            <PrimaryButton
              type="submit"
              className="btn btn-sm text-uppercase position-relative btn btn-primary"
              disabled={isLoading || !email}
            >
              {isLoading ? __("Processing...") : __("Proceed to Payment")}
            </PrimaryButton>
          </div>
        </div>
      </form>
    </div>
  );
}
