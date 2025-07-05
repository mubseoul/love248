import React from "react";
import { Head } from "@inertiajs/inertia-react";
import __ from "@/Functions/Translate";
import Front from "@/Layouts/Front";
import { BsFillBagCheckFill } from "react-icons/bs";

export default function CardSuccess({ sale }) {
  return (
    <Front>
      <Head title={__("Payment Successful - Thank you")} />

      <div className="p-4 sm:p-8 max-w-3xl mx-auto bg-footer shadow mb-5">
        <div className="flex justify-center items-center">
          <div className="mr-2">
            <BsFillBagCheckFill />
          </div>
          <div>
            <h3 className="text-3xl font-semibold text-gray-primary text-center">
              {__("Thank you for your payment")}
            </h3>
          </div>
        </div>

        <h3 className="mt-10 text-xl text-gray-primary text-center">
          {sale.subscription_plan ? (
            <>
              {__(
                "Thank you for your payment! you successfully subscribed to :plan",
                {
                  plan: sale.subscription_plan,
                }
              )}
            </>
          ) : (
            <>
              {__(
                "Thank you for your payment! :tokens tokens have been added to your balance!",
                {
                  tokens: sale.tokens,
                }
              )}
            </>
          )}
        </h3>
      </div>
    </Front>
  );
}
