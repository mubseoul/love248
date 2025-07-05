import React, { useState } from "react";
import __ from "@/Functions/Translate";
import axios from "axios";
import PrimaryButton from "@/Components/PrimaryButton";
import { toast } from "react-toastify";
import TextInput from "@/Components/TextInput";

export default function MercadoPaymentForm({ tokenPack }) {
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
      // Set axios to not parse JSON automatically
      const response = await axios.post(
        route("mercado.createRecurringSubscription", { plan: tokenPack.id }),
        {
          email: email,
          tokPack: tokenPack.id,
          is_recurring: true, // Explicitly mark as recurring
        },
        {
          headers: {
            Accept: "*/*",
          },
          responseType: "text",
          transformResponse: [(data) => data], // Prevent axios from parsing the response
        }
      );

      // Check for Inertia location header for redirection
      const locationHeader = response.headers["x-inertia-location"];
      if (locationHeader) {
        window.location.href = locationHeader;
        return;
      }

      // Try to parse the response data as JSON if it's a string
      let jsonData = response.data;
      if (typeof response.data === "string") {
        try {
          // If the response is a URL string, use it directly
          if (response.data.trim().startsWith("http")) {
            window.location.href = response.data.trim();
            return;
          }

          jsonData = JSON.parse(response.data);
        } catch (e) {
          // Not a JSON response
        }
      }

      // Handle different response formats
      if (
        jsonData &&
        typeof jsonData === "string" &&
        jsonData.startsWith("http")
      ) {
        window.location.href = jsonData;
      } else if (jsonData && jsonData.init_point) {
        window.location.href = jsonData.init_point;
      } else if (jsonData && jsonData.error) {
        toast.error(formatErrorMessage(jsonData.error));
        setIsLoading(false);
      } else {
        toast.error("Unknown response format. Please try again.");
        setIsLoading(false);
      }
    } catch (error) {
      // Check if it's a redirect error (which might not actually be an error)
      if (error.response) {
        const locationHeader = error.response.headers["x-inertia-location"];
        if (locationHeader) {
          window.location.href = locationHeader;
          return;
        }

        // If it's HTML content, it might be a redirect page
        if (error.response.headers["content-type"]?.includes("text/html")) {
          document.open();
          document.write(error.response.data);
          document.close();
          return;
        }

        // Parse the error message to make it more user-friendly
        let errorMessage = "Server error occurred";

        try {
          // Try to extract the error message from the response
          if (error.response.data) {
            if (typeof error.response.data === "string") {
              try {
                const parsedError = JSON.parse(error.response.data);
                errorMessage = formatErrorMessage(parsedError);
              } catch (e) {
                errorMessage = error.response.data;
              }
            } else {
              errorMessage = formatErrorMessage(error.response.data);
            }
          }
        } catch (e) {
          console.error("Error parsing error message:", e);
        }

        toast.error(errorMessage);
      } else if (error.request) {
        toast.error(
          "No response received from server. Please check your internet connection."
        );
      } else {
        toast.error(`Error: ${error.message}`);
      }
      setIsLoading(false);
    }
  };

  return (
    <div className="">
      <div className="">
        <form onSubmit={handleMercadoSubmit} className="">
          <div className="form-group">
            <TextInput
              name="email"
              value={email}
              placeholder="Enter your email"
              handleChange={(e) => setEmail(e.target.value)}
              className="form-control"
              required
            />
          </div>

          <div className="form-group iq-button mt-3">
            <PrimaryButton
              type="submit"
              className="btn btn-sm text-uppercase position-relative btn btn-primary w-full"
              disabled={isLoading || !email}
            >
              {isLoading
                ? __("Processing...")
                : __("Subscribe with Mercado Pago")}
            </PrimaryButton>
          </div>
        </form>
      </div>
    </div>
  );
}
