import React, { useState } from "react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, usePage } from "@inertiajs/inertia-react";
import __ from "@/Functions/Translate";
import axios from "axios";
import { Inertia } from "@inertiajs/inertia";

export default function MercadoSubscription({ auth, tokPack }) {
  const [isLoading, setIsLoading] = useState(false);
  const [email, setEmail] = useState("");
  const { currency_symbol, currency_code } = usePage().props;
  const { flash } = usePage().props;

  const handleSubmit = async (e) => {
    e.preventDefault();
    setIsLoading(true);

    try {
      const response = await axios.post(
        route("mercado.mercadoNewSubsPlanPurchase", { plan: tokPack.id }),
        {
          email: email,
          tokPack: tokPack.id,
        }
      );

      if (response.data) {
        window.location.href = response.data;
      } else {
        setIsLoading(false);
      }
    } catch (error) {
      console.error("Error processing subscription:", error);
      setIsLoading(false);
    }
  };

  return (
    <AuthenticatedLayout auth={auth}>
      <Head title={__("Subscribe with Mercado Pago")} />
      <div className="p-4 sm:p-8 max-w-3xl mx-auto mb-5">
        <h3 className="text-3xl font-semibold text-gray-primary text-center mb-10">
          {__("Subscribe with Mercado Pago")}
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
              {__("Subscription Details")}
            </h4>
            <p className="mt-2 text-gray-600">
              {__("Plan")}: {tokPack.subscription_name}
            </p>
            <p className="mt-1 text-gray-600">
              {__("Price")}: {currency_symbol}
              {tokPack.subscription_price} {currency_code} / {__("month")}
            </p>
            <p className="mt-1 text-gray-600">
              {__("Duration")}: {tokPack.days} {__("days")}
            </p>
          </div>

          <form onSubmit={handleSubmit} className="space-y-4">
            <div className="form-group">
              <label className="block text-gray-700 mb-2" htmlFor="email">
                {__("Email Address")}
              </label>
              <input
                type="email"
                id="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                className="border rounded p-2 w-full"
                placeholder={__("Enter your email")}
                required
              />
            </div>

            <div className="form-group">
              <button
                type="submit"
                className="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded w-full"
                disabled={isLoading}
              >
                {isLoading ? __("Processing...") : __("Subscribe Now")}
              </button>
            </div>
          </form>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
