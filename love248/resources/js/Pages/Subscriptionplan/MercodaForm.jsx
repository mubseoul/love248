import React, { useState, useEffect } from "react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, usePage } from "@inertiajs/inertia-react";
import __ from "@/Functions/Translate";
import axios from "axios";
import { Inertia } from "@inertiajs/inertia";

export default function MercodaForm({
  auth,
  price,
  preferenceid,
  publicKey,
  tokenPack,
}) {
  const [isLoading, setIsLoading] = useState(false);
  const { flash } = usePage().props;

  useEffect(() => {
    const loadMercadoPago = async () => {
      const script = document.createElement("script");
      script.src = "https://sdk.mercadopago.com/js/v2";
      script.type = "text/javascript";
      script.async = true;
      document.body.appendChild(script);

      script.onload = () => {
        initMercadoPago();
      };
    };

    loadMercadoPago();
  }, []);

  const initMercadoPago = () => {
    const mp = new window.MercadoPago(publicKey, {
      locale: "en-US",
    });

    const cardForm = mp.cardForm({
      amount: String(price),
      iframe: true,
      form: {
        id: "form-checkout",
        cardNumber: {
          id: "form-checkout__cardNumber",
          placeholder: "Card Number",
        },
        expirationDate: {
          id: "form-checkout__expirationDate",
          placeholder: "MM/YY",
        },
        securityCode: {
          id: "form-checkout__securityCode",
          placeholder: "CVV",
        },
        cardholderName: {
          id: "form-checkout__cardholderName",
          placeholder: "Cardholder Name",
        },
        issuer: {
          id: "form-checkout__issuer",
          placeholder: "Issuing Bank",
        },
        installments: {
          id: "form-checkout__installments",
          placeholder: "Installments",
        },
        identificationType: {
          id: "form-checkout__identificationType",
          placeholder: "Document Type",
        },
        identificationNumber: {
          id: "form-checkout__identificationNumber",
          placeholder: "Document Number",
        },
        cardholderEmail: {
          id: "form-checkout__cardholderEmail",
          placeholder: "Email",
        },
      },
      callbacks: {
        onFormMounted: (error) => {
          if (error) {
            console.log("Form Mounted error: ", error);
            return;
          }
        },
        onSubmit: (event) => {
          event.preventDefault();
          setIsLoading(true);

          const {
            paymentMethodId: payment_method_id,
            issuerId: issuer_id,
            cardholderEmail: email,
            token,
            installments,
            identificationNumber,
            identificationType,
          } = cardForm.getCardFormData();

          const data = {
            id: tokenPack,
            token,
            issuer_id,
            payment_method_id,
            transaction_amount: Number(price),
            installments: Number(installments),
            description: "Subscription Plan",
            payer: {
              email,
              identification: {
                type: identificationType,
                number: identificationNumber,
              },
            },
            preferenceid: preferenceid,
          };

          axios
            .post(route("mercoda.mercodaSubsPlanPayment"), data)
            .then((res) => {
              setIsLoading(false);
              if (res.data.redirect) {
                Inertia.visit(res.data.redirect);
              }
            })
            .catch((err) => {
              setIsLoading(false);
              console.log(err);
            });
        },
        onFetching: (resource) => {
          if (resource === "paymentMethods") {
            return;
          }
        },
      },
    });
  };

  return (
    <AuthenticatedLayout auth={auth}>
      <Head title={__("Subscription Plan Payment")} />
      <div className="p-4 sm:p-8 max-w-3xl mx-auto mb-5">
        <h3 className="text-3xl font-semibold text-gray-primary text-center mb-10">
          {__("Complete your subscription payment")}
        </h3>

        <div className="my-5">
          {flash.message && (
            <div className="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mt-3">
              {flash.message}
            </div>
          )}
        </div>

        <div className="bg-white p-6 rounded-lg shadow-md">
          <div className="mb-5">
            <h4 className="text-xl font-semibold text-gray-800">
              {__("Payment Amount")}: ${price}
            </h4>
          </div>

          <form id="form-checkout" className="space-y-4">
            <div className="form-group">
              <div
                id="form-checkout__cardNumber"
                className="border rounded p-2 w-full"
              ></div>
            </div>
            <div className="grid grid-cols-2 gap-4">
              <div className="form-group">
                <div
                  id="form-checkout__expirationDate"
                  className="border rounded p-2 w-full"
                ></div>
              </div>
              <div className="form-group">
                <div
                  id="form-checkout__securityCode"
                  className="border rounded p-2 w-full"
                ></div>
              </div>
            </div>
            <div className="form-group">
              <div
                id="form-checkout__cardholderName"
                className="border rounded p-2 w-full"
              ></div>
            </div>
            <div className="form-group">
              <div
                id="form-checkout__cardholderEmail"
                className="border rounded p-2 w-full"
              ></div>
            </div>
            <div className="grid grid-cols-2 gap-4">
              <div className="form-group">
                <div
                  id="form-checkout__issuer"
                  className="border rounded p-2 w-full"
                ></div>
              </div>
              <div className="form-group">
                <div
                  id="form-checkout__installments"
                  className="border rounded p-2 w-full"
                ></div>
              </div>
            </div>
            <div className="grid grid-cols-2 gap-4">
              <div className="form-group">
                <div
                  id="form-checkout__identificationType"
                  className="border rounded p-2 w-full"
                ></div>
              </div>
              <div className="form-group">
                <div
                  id="form-checkout__identificationNumber"
                  className="border rounded p-2 w-full"
                ></div>
              </div>
            </div>
            <div className="form-group">
              <button
                type="submit"
                id="form-checkout__submit"
                className="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded w-full"
                disabled={isLoading}
              >
                {isLoading ? __("Processing...") : __("Pay")}
              </button>
            </div>
          </form>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
