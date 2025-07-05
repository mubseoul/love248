import React from "react";
import { Head, usePage } from "@inertiajs/inertia-react";
import __ from "@/Functions/Translate";
import { MdGeneratingTokens } from "react-icons/md";
import { Inertia } from "@inertiajs/inertia";
import Front from "@/Layouts/Front";

export default function Packages({ packs }) {
  const { currency_symbol, currency_code } = usePage().props;
  return (
    <Front
      extraHeader={false}
      extraHeaderTitle={__("Subscription plan")}
      extraHeaderText={""}
      extraHeaderImage="/images/get-tokens-icon.png"
      extraImageHeight="h-12"
    >
      <Head title={__("Get Tokens")} />

      <div className=" dark:bg-zinc-900 -mt-[80px] py-5">
        <div className="iq-breadcrumb bg-footer">
          <div className="container-fluid">
            <div className="align-items-center row">
              <div className="col-sm-12">
                <nav className="text-center">
                  <h2 className="title text-capitalize text-white">
                    {__("Pricing Plan")}
                  </h2>
                  <nav aria-label="breadcrumb" className="main-bg">
                    <ol className="breadcrumb text-center justify-content-center">
                      <li className="breadcrumb-item">
                        <a href="/">{__("Home")}</a>
                      </li>
                      <li
                        className="breadcrumb-item active"
                        aria-current="page"
                      >
                        {__("Pricing Plan")}
                      </li>
                    </ol>
                  </nav>
                </nav>
              </div>
            </div>
          </div>
        </div>
        <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-3 px-4 mt-5">
          {packs.map((pack) => (
            <div key={pack.id}>
              <div className="pricing-plan-wrapper d-flex flex-column h-full">
                <div className="pricing-plan-header bg-dark">
                  <h4 className="plan-name text-capitalize mb-0 text-white">
                    {pack.subscription_name}
                  </h4>
                  <span className="main-price text-primary">
                    R{currency_symbol} {pack.subscription_price} {currency_code}
                  </span>
                </div>
                <div className="pricing-details dark-gray flex-grow-1 h-full d-flex flex-column">
                  <div className="pricing-plan-description text-white">
                    <div
                      dangerouslySetInnerHTML={{
                        __html: pack.details,
                      }}
                    />
                  </div>
                  <div className="pricing-plan-footer mt-auto">
                    <div className="iq-button">
                      <button
                        onClick={(e) =>
                          Inertia.visit(
                            route("subscription.selectGateways", {
                              tokenPack: pack.id,
                            })
                          )
                        }
                        type="button"
                        className="btn btn-sm text-uppercase position-relative btn btn-primary"
                      >
                        <span className="button-text">{__("Buy Now")}</span>
                        <i className="fa-solid fa-play"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </Front>
  );
}
