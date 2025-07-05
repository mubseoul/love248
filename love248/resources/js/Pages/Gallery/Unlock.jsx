import __ from "@/Functions/Translate";
import { Link, Head, usePage } from "@inertiajs/inertia-react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import {
  MdGeneratingTokens,
  MdOutlineAccountBalanceWallet,
} from "react-icons/md";
import { useState, useEffect } from "react";
import { Inertia } from "@inertiajs/inertia";
import axios from "axios";
import Spinner from "@/Components/Spinner";
import PrimaryButton from "@/Components/PrimaryButton";
import TextInput from "@/Components/TextInput";
import InputLabel from "@/Components/InputLabel";
import InputError from "@/Components/InputError";
import Modal from "@/Components/Modal";

export default function Subscribe({ gallery }) {
  const { auth } = usePage().props;

  const [processing, setProcessing] = useState(false);
  const [email, setEmail] = useState("");
  const [errors, setErrors] = useState({});
  const [showForm, setShowForm] = useState(false);

  const handlePurchaseClick = () => {
    // Check if user is authenticated
    if (!auth.user) {
      // Redirect to login page
      Inertia.visit(route('login'));
      return;
    }

    // For free content, skip the modal and directly process
    if (parseFloat(gallery?.price) === 0) {
      setProcessing(true);
      const routeUrl = route("mercado.content.gallery.purchase", {
        gallery: gallery?.id,
      });
      
      axios.post(routeUrl, {
        email: auth.user.email // Use user's email for free content
      })
      .then(response => {
        console.log('Free content response:', response.data);
        if (response.data && response.data.success && response.data.is_free) {
          console.log('Free content - redirecting directly:', response.data.redirect_url);
          window.location.href = response.data.redirect_url;
        }
      })
      .catch(error => {
        console.error('Free content error:', error);
        setProcessing(false);
      });
      return;
    }
    
    setShowForm(true);
  };

  const closeForm = () => {
    setShowForm(false);
    setErrors({});
  };

  const confirmPurchase = (e) => {
    e.preventDefault();

    // Clear any previous errors
    setErrors({});
    setProcessing(true);

    const routeUrl = route("mercado.content.gallery.purchase", {
      gallery: gallery?.id,
    });

    // Use axios to handle JSON response properly (same as MercadoTokenForm)
    axios.post(routeUrl, {
      email: email
    })
    .then(response => {
      console.log('Payment response:', response.data);

      // Check if content is free - no payment popup needed
      if (response.data && response.data.success && response.data.is_free) {
        // Close modal first
        closeForm();
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

  return (
    <AuthenticatedLayout>
      <Head
        title={__("Unlock gallery: :galleryTitle", {
          gallery: gallery?.title,
        })}
      />

      <div className="ml-0">
        <div className="p-4 sm:p-8 bg-footer mb-5">
          <header>
            <div className="justify-center flex items-center space-x-2 flex-wrap">
              <div>
                
              </div>
              <div>
                <Link
                  href={route("channel", {
                    user: gallery?.streamer?.username,
                  })}
                >
                  <h2 className="text-center text-2xl font-medium text-gray-primary">
                    {__("Unlock :galleryTitle", {
                      galleryTitle: gallery?.title,
                    })}
                  </h2>
                </Link>
                <p className="mt-1 text-sm text-gray-primary text-center">
                  {__("Confirm your purchase")}
                </p>
              </div>
            </div>
          </header>

          <div className="border-t border-gray-700 pt-5 text-xl text-center font-light mt-8 dark:text-white">
            <p className="bg-black shadow-md text-white font-semibold inline-flex rounded-lg p-3 items-center space-x-2">
              <MdGeneratingTokens className="h-6 w-6 text-indigo-400" />
              <span>
                {__("Price: R$ :price", { price: gallery?.price })}
              </span>
            </p>

            <div className="mt-5 border-t border-gray-700 pt-5">
              <div>
                <div className="iq-button">
                  <PrimaryButton
                    onClick={handlePurchaseClick}
                    className="flex items-center"
                  >
                    <div className="flex items-center">
                      <MdGeneratingTokens className="h-6 w-6 mr-2" />
                      {__("Purchase Gallery via Mercado Pago")}
                    </div>
                  </PrimaryButton>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Modal for the payment form */}
      <Modal show={showForm} onClose={closeForm} maxWidth="md">
        <div className="bg-footer p-10 text-center">
          <h3 className="text-lg font-bold mb-4 text-gray-primary">
            {__("Purchase Gallery with Mercado Pago")}
          </h3>

          <p className="mb-4 text-gray-primary">
            {__(
              "Enter your email address to continue with your purchase of :galleryTitle for :price",
              {
                galleryTitle: gallery?.title,
                price: `R$ ${gallery?.price}`,
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
    </AuthenticatedLayout>
  );
}
