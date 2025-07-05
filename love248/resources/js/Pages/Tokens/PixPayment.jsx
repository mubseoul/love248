import React, { useState } from "react";
import { Head } from "@inertiajs/inertia-react";
import __ from "@/Functions/Translate";
import Front from "@/Layouts/Front";

export default function PixPayment({ pixData, message }) {
  const [copied, setCopied] = useState(false);

  // Function to copy PIX code to clipboard
  const copyToClipboard = () => {
    if (pixData && pixData.qr_code) {
      navigator.clipboard
        .writeText(pixData.qr_code)
        .then(() => {
          setCopied(true);
          setTimeout(() => setCopied(false), 3000);
        })
        .catch((err) => {
          console.error("Failed to copy: ", err);
        });
    }
  };

  return (
    <Front>
      <Head title={__("PIX Payment")} />

      <div className="p-4 sm:p-8 bg-white max-w-3xl mx-auto dark:bg-zinc-900 shadow sm:rounded-lg">
        <div className="flex justify-center items-center mb-8">
          <div>
            <h3 className="text-3xl font-semibold dark:text-white text-center">
              {__("PIX Payment")}
            </h3>
          </div>
        </div>

        <div className="text-center mb-6">
          <p className="text-lg dark:text-white">{message}</p>
        </div>

        {pixData && (
          <div className="flex flex-col items-center">
            {pixData.qr_code_base64 && (
              <div className="mb-6">
                <h4 className="font-semibold text-lg mb-3 dark:text-white">
                  {__("Scan QR Code")}
                </h4>
                <img
                  src={`data:image/png;base64,${pixData.qr_code_base64}`}
                  alt="PIX QR Code"
                  className="w-64 h-64 mx-auto p-2 bg-white rounded"
                />
              </div>
            )}

            {pixData.qr_code && (
              <div className="mb-6 w-full max-w-md">
                <h4 className="font-semibold text-lg mb-3 dark:text-white">
                  {__("Or Copy PIX Code")}
                </h4>
                <div className="flex items-center">
                  <input
                    type="text"
                    value={pixData.qr_code}
                    readOnly
                    className="flex-grow px-3 py-2 border rounded-l-md dark:bg-zinc-800 dark:text-white dark:border-zinc-700"
                  />
                  <button
                    onClick={copyToClipboard}
                    className="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-r-md"
                  >
                    {copied ? __("Copied!") : __("Copy")}
                  </button>
                </div>
              </div>
            )}

            {pixData.ticket_url && (
              <div className="mt-4">
                <a
                  href={pixData.ticket_url}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-md inline-block"
                >
                  {__("Open MercadoPago Payment Page")}
                </a>
              </div>
            )}
          </div>
        )}

        <div className="mt-8 text-center">
          <p className="text-sm text-gray-500 dark:text-gray-400">
            {__(
              "After completing your payment, you can close this page. Your tokens will be automatically added to your account once the payment is confirmed."
            )}
          </p>
        </div>
      </div>
    </Front>
  );
}
