import React, { useState } from "react";
import { Head, usePage } from "@inertiajs/inertia-react";
import __ from "@/Functions/Translate";
import { MdGeneratingTokens } from "react-icons/md";
import { Inertia } from "@inertiajs/inertia";
import Front from "@/Layouts/Front";
import MercadoTokenForm from "@/Components/MercadoTokenForm";
import Modal from "@/Components/Modal";

export default function Packages({ packs }) {
  const { currency_symbol, currency_code } = usePage().props;
  const [selectedPack, setSelectedPack] = useState(null);
  const [showForm, setShowForm] = useState(false);

  const handlePurchaseClick = (pack) => {
    setSelectedPack(pack);
    setShowForm(true);
  };

  const closeForm = () => {
    setShowForm(false);
    setSelectedPack(null);
  };

  return (
    <Front
      extraHeader={false}
      extraHeaderTitle={__("Token Packages")}
      extraHeaderText={""}
      extraHeaderImage="/images/get-tokens-icon.png"
      extraImageHeight="h-12"
    >
      <Head title={__("Get Tokens")} />

      <div className="py-5">
        <div className="flex flex-col md:flex-row items-center">
          <div className="ml-5 mt-5">
            <h3 className="text-3xl text-indigo-800 font-bold mb-3 text-gray-primary">
              {__("Token Packages")}
            </h3>
            <p className="text-xl font-medium mb-3 text-gray-600 text-gray-primary">
              {__(
                "Tokens can be used for tips and subscriptions to your favourite channels"
              )}
            </p>
          </div>
        </div>

        <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 px-4">
          {packs.map((pack) => (
            <div
              key={pack.id}
              className="rounded-lg mr-5 p-4 bg-footer mx-auto shadow dark:bg-zinc-700 w-full"
            >
              <div className="flex items-center space-x-3">
                <div>
                  <MdGeneratingTokens className="h-6 w-6 text-gray-primary" />{" "}
                </div>
                <div>
                  <h5 className="text-lg font-bold text-gray-primary">
                    {__(":tokens Tokens", {
                      tokens: pack.tokensFormatted,
                    })}
                  </h5>
                </div>
              </div>
              <div className="text-center mt-2">
                <h5 className="text-lg font-light text-gray-primary">
                  {/* {currency_symbol} */}
                  {pack.price} {"BRL"}
                </h5>

                <div className="iq-button">
                  <button
                    onClick={() => handlePurchaseClick(pack)}
                    className="btn text-uppercase position-relative btn btn-primary"
                  >
                    {__("Purchase")}
                  </button>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>

      {/* Modal for the payment form */}
      <Modal show={showForm} onClose={closeForm} maxWidth="md">
        {selectedPack && (
          <MercadoTokenForm tokenPack={selectedPack} onCancel={closeForm} />
        )}
      </Modal>
    </Front>
  );
}
