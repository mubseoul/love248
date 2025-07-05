import React, { useState } from "react";
import PrimaryButton from "@/Components/PrimaryButton";
import SecondaryButton from "@/Components/SecondaryButton";
import { useForm } from "@inertiajs/inertia-react";
import __ from "@/Functions/Translate";
import { usePage } from "@inertiajs/inertia-react";

export default function ConnectMercadoAccountForm({ className }) {
  const { mercadoaccount } = usePage().props;
  const { 
    post, 
    processing, 
    reset 
  } = useForm();

  const connectMercadoAccount = (e) => {
    e.preventDefault();
    window.location.href = route("mercado.account.connect");
  };

  const disconnectMercadoAccount = (e) => {
    e.preventDefault();

    post(route("mercado.account.disconnect"), {
      preserveScroll: true,
      onSuccess: () => {
        reset();
        // The page will be automatically updated by Inertia
      },
      onError: (errors) => {
        console.error("Failed to disconnect Mercado account:", errors);
      },
    });
  };

  return (
    <section className={`space-y-6 ${className}`}>
      <header>
        <h2 className="text-lg font-medium text-gray-primary">
          {__("Mercado Account")}
        </h2>

        <p className="mt-1 text-sm text-gray-primary">
          {mercadoaccount
            ? __(
                "Your Mercado account is successfully connected. You can now receive commission in your Mercado account."
              )
            : __(
                "Once your account is connected, you will able to get commission in your mercado account."
              )}
        </p>
      </header>

      {!mercadoaccount && (
        <div className="iq-button">
          <PrimaryButton onClick={connectMercadoAccount} disabled={processing}>
            {__("Connect Mercado Account")}
          </PrimaryButton>
        </div>
      )}

      {mercadoaccount && (
        <>
          <div className="overflow-x-auto">
            <table className="min-w-full bg-black mb-3 overflow-hidden">
              <tbody className="">
                <tr>
                  <td className="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-primary">
                    {__("User ID")}
                  </td>
                  <td className="px-4 py-2 whitespace-nowrap text-sm text-red-600">
                    {mercadoaccount.user_id}
                  </td>
                </tr>
                <tr>
                  <td className="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-primary">
                    {__("Public Key")}
                  </td>
                  <td className="px-4 py-2 whitespace-nowrap text-sm text-red-600">
                    {mercadoaccount.public_key}
                  </td>
                </tr>
                <tr>
                  <td className="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-primary">
                    {__("Status")}
                  </td>
                  <td className="px-4 py-2 whitespace-nowrap text-sm text-red-600">
                    <span className="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                      {__("Active")}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div className="flex items-center mt-4">
            <div className="text-sm text-green-500 mr-4">
              {__("Account connected")} ✓
            </div>
            <SecondaryButton
              onClick={disconnectMercadoAccount}
              disabled={processing}
            >
              {processing
                ? __("Disconnecting...")
                : __("Disconnect Account")}
            </SecondaryButton>
          </div>
        </>
      )}
    </section>
  );
}
