import React, { useState, useEffect } from "react";
import __ from "@/Functions/Translate";
import { Inertia } from "@inertiajs/inertia";
import Modal from "@/Components/Modal";
import TextInput from "@/Components/TextInput";
import InputError from "@/Components/InputError";
import PrimaryButton from "@/Components/PrimaryButton";
import Spinner from "@/Components/Spinner";
import { usePage } from "@inertiajs/inertia-react";
import axios from "axios";

export default function PurchaseModal({ show, onClose, item, type }) {
  const { auth } = usePage().props;
  const [processing, setProcessing] = useState(false);
  const [email, setEmail] = useState("");
  const [errors, setErrors] = useState({});

  // Reset form when modal opens
  useEffect(() => {
    if (show) {
      setEmail("");
      setErrors({});
      setProcessing(false);
    }
  }, [show]);

  const confirmPurchase = (e) => {
    e.preventDefault();

    // Check if user is authenticated
    if (!auth.user) {
      // Redirect to login page
      Inertia.visit(route('login'));
      return;
    }

    // Clear any previous errors
    setErrors({});

    const routeName = type === 'video' 
      ? 'mercado.content.video.purchase' 
      : 'mercado.content.gallery.purchase';
    
    const routeParams = type === 'video' 
      ? { video: item.id } 
      : { gallery: item.id };

    setProcessing(true);

    // Use axios to handle JSON response properly (same as MercadoTokenForm)
    axios.post(route(routeName, routeParams), {
      email: email
    })
    .then(response => {
      console.log('Payment response:', response.data);

      // Check if content is free - no payment popup needed
      if (response.data && response.data.success && response.data.is_free) {
        // Close modal first
        handleClose();
        // Redirect to content page
        window.location.href = response.data.redirect_url;
        return;
      }

      // Check if we have a successful response with init_point (paid content)
      if (response.data && response.data.success && response.data.init_point) {
        window.location.href = response.data.init_point;
      }
      // Check if we have a payment_url (legacy support)
      else if (response.data && response.data.success && response.data.payment_url) {
        window.location.href = response.data.payment_url;
      }
      // Check if we have a redirect URL
      else if (response.data && response.data.redirect) {
        window.location.href = response.data.redirect;
      }
      // Handle direct string URL response
      else if (typeof response.data === "string" && response.data.startsWith("http")) {
        window.location.href = response.data;
      }
      // Handle error message in response
      else if (response.data && response.data.message) {
        setErrors({ general: response.data.message });
      }
      // Fallback error handling
      else {
        setErrors({ general: 'Unknown response format. Please try again.' });
      }
    })
    .catch(error => {
      console.error('Payment error:', error);
      console.error('Error response:', error.response?.data);

      // Extract error message from response if available (same as token form)
      const errorMessage = error.response?.data?.message || 
                          error.response?.data?.error || 
                          error.message || 
                          'Failed to process payment. Please try again.';

      if (error.response && error.response.data && error.response.data.errors) {
        setErrors(error.response.data.errors);
      } else {
        setErrors({ general: errorMessage });
      }
    })
    .finally(() => {
      setProcessing(false);
    });
  };

  const handleClose = () => {
    setErrors({});
    setProcessing(false);
    onClose();
  };

  if (!item) return null;

  return (
    <Modal show={show} onClose={handleClose} maxWidth="md">
      <div className="bg-footer p-10 text-center">
        <h3 className="text-lg font-bold mb-4 text-gray-primary">
          {type === 'video' 
            ? __("Purchase Video with Mercado Pago")
            : __("Purchase Gallery with Mercado Pago")
          }
        </h3>

        <p className="mb-4 text-gray-primary">
          {__(
            "Enter your email address to continue with your purchase of :itemTitle for :price",
            {
              itemTitle: item.title,
              price: `R$ ${item.price}`,
            }
          )}
        </p>

        <form onSubmit={confirmPurchase} className="">
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
            <InputError message={errors.email} className="mt-2" />
          </div>

          {errors.general && (
            <div className="mb-4">
              <InputError message={errors.general} className="mt-2" />
            </div>
          )}

          <div className="flex justify-center">
            <div className="iq-button">
              <PrimaryButton
                type="submit"
                className="btn btn-sm text-uppercase position-relative btn btn-primary"
                disabled={processing || !email}
              >
                {processing ? __("Processing...") : __("Proceed to Payment")}
              </PrimaryButton>
            </div>
          </div>

          {processing && (
            <div className="mt-3">
              <Spinner />
            </div>
          )}
        </form>
      </div>
    </Modal>
  );
} 