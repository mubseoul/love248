import React from "react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link, usePage } from "@inertiajs/inertia-react";
import __ from "@/Functions/Translate";
import { MdGeneratingTokens } from "react-icons/md";
import { Inertia } from "@inertiajs/inertia";
import PrimaryButton from "@/Components/PrimaryButton";
import MercadoPaymentForm from "@/Components/MercadoPaymentForm";

export default function SelectGateway({
  auth,
  tokenPack,
  paypalEnabled,
  stripeEnabled,
  bankEnabled,
  ccbillEnabled,
  mercadoEnabled,
  paypalImg,
  ccbillImg,
  stripeImg,
  bankImg,
}) {
  const { currency_symbol, currency_code } = usePage().props;

  return (
    <AuthenticatedLayout auth={auth}>
      <Head title={__("Select Gateway - Purchase Tokens")} />

      <div className="p-4 sm:p-8 max-w-3xl mx-auto bg-footer shadow mb-5">
        <h3 className="text-3xl font-semibold text-gray-primary text-center">
          {__("Select Payment Gateway")}
        </h3>

        <h3 className="mt-5 text-2xl font-semibold text-gray-primary text-center">
          {__("You are purchasing :tokensAmount for :moneyAmount", {
            status: tokenPack.status,
            tokensAmount: tokenPack.subscription_name,
            moneyAmount: `${currency_symbol}${tokenPack.subscription_price}`,
          })}
        </h3>
        <div className="mt-10 flex items-center justify-center flex-col space-y-5">
          {paypalEnabled == "Yes" && (
            <div>
              <span className="block text-center text-gray-700 font-bold text-gray-primary text-lg">
                {__("Pay via PayPal")}
              </span>
              {paypalEnabled == "Yes" && (
                <Link
                  href={route("paypal.purchaseTokenss", {
                    tokenPack: tokenPack.id,
                  })}
                >
                  <img src={paypalImg} alt="paypal" className="h-24 mx-auto" />
                </Link>
              )}
            </div>
          )}
          {mercadoEnabled == "Yes" && (
            <MercadoPaymentForm tokenPack={tokenPack} />
          )}

          {/* {stripeEnabled == "Yes" && (
            <div>
              <Link
                href={route("stripe.purchaseTokenss", {
                  tokPack: tokenPack.id,
                })}
              >
                <div
                  type="submit"
                  className="btn text-uppercase position-relative d-flex w-100 my-2 btn btn-primary btn-sm justify-content-center align-items-center"
                >
                  <span className="button-text">{__("Credit Card (Stripe)")}</span>
                  <i className="fa-solid fa-play ms-2"></i>
                </div>
              </Link>
            </div>
          )} */}
          {/* {ccbillEnabled == "Yes" && (
            <div className="pt-5">
              <span className="block text-center text-gray-700 font-bold text-gray-primary text-lg">
                {__("CCBill (Credit Card)")}
              </span>
              <a
                href={route("ccbill.purchaseTokenss", {
                  tokenPack: tokenPack.id,
                })}
              >
                <img src={ccbillImg} alt="stripe" className="h-14 mx-auto" />
              </a>
            </div>
          )} */}
          {/* {bankEnabled == "Yes" && (
            <div className="mt-10">
              <span className="block text-center text-gray-700 font-bold text-gray-primary text-lg">
                {__("Pay via Bank Transfer")}
              </span>
              <Link
                href={route("bank.purchaseTokenss", {
                  tokenPack: tokenPack.id,
                })}
              >
                <img src={bankImg} alt="stripe" className="h-14 mx-auto" />
              </Link>
            </div>
          )} */}
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
