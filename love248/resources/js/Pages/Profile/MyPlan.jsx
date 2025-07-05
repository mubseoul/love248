import __ from "@/Functions/Translate";
import { Link, Head, usePage } from "@inertiajs/inertia-react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { BiMoney } from "react-icons/bi";
import { FiAlertCircle } from "react-icons/fi";
import { Inertia } from "@inertiajs/inertia";
import AccountNavi from "../Channel/Partials/AccountNavi";
import PrimaryButton from "@/Components/PrimaryButton";
import { useState, useEffect } from "react";
import Modal from "@/Components/Modal";
import { ToastContainer, toast } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";
import axios from "axios";

export default function MyPlan({ plan }) {
  const { auth, flash } = usePage().props;
  const [confirmingCancel, setConfirmingCancel] = useState(false);
  const [verifyingPayments, setVerifyingPayments] = useState({});

  // Show toast notifications based on flash messages when page loads or updates
  useEffect(() => {
    // Handle basic flash messages
    if (flash.message) {
      toast.success(flash.message);
    }
    if (flash.error) {
      toast.error(flash.error);
    }

    // Handle verification status toast (either from flash or URL parameters)
    if (
      flash.verified === true ||
      new URLSearchParams(window.location.search).get("verified") === "true"
    ) {
      toast.success(
        __("Payment verified successfully! Your subscription is now active.")
      );
    } else if (
      flash.verified === false ||
      new URLSearchParams(window.location.search).get("verified") === "false"
    ) {
      toast.error(
        __("Payment verification failed. Please try again or contact support.")
      );
    }
  }, [flash.message, flash.error, flash.verified]);

  const handleUpgrade = () => {
    Inertia.visit(route("subscription.plan"));
  };

  const handleCancel = () => {
    setConfirmingCancel(true);
  };

  console.log(auth);

  const confirmCancel = () => {
    Inertia.visit(route("subscription.cancel"));
    setConfirmingCancel(false);
  };

  // Verify payment via API call
  const handleVerifyPayment = (invoice) => {
    // Check if this payment is already being processed
    if (verifyingPayments[invoice.id]) return;

    // Mark this payment as being verified
    setVerifyingPayments((prev) => ({
      ...prev,
      [invoice.id]: true,
    }));

    // Show loading toast
    const toastId = toast.loading(__("Verifying payment..."));

    // Determine if it's a preapproval or regular payment
    const isPreapproval = /^[a-zA-Z]/.test(invoice.payment_id);

    // Call the verification API
    axios
      .post("/api/verify-payment", {
        payment_id: invoice.payment_id,
        is_preapproval: isPreapproval,
      })
      .then((response) => {
        // Check if the response has data
        const data = response.data;

        if (data.success) {
          toast.update(toastId, {
            render: data.message || __("Payment verified successfully!"),
            type: "success",
            isLoading: false,
            autoClose: 3000,
          });

          // Reload the page after a delay to show updated status
          setTimeout(() => {
            window.location.reload();
          }, 3000);
        } else {
          toast.update(toastId, {
            render: data.message || __("Payment verification failed"),
            type: "error",
            isLoading: false,
            autoClose: 5000,
          });
        }
      })
      .catch((error) => {
        console.error("Verification error:", error);

        // Get error message from response if available
        const errorMessage =
          error.response?.data?.message ||
          error.message ||
          __("Unable to verify payment. Please try again or contact support.");

        toast.update(toastId, {
          render: errorMessage,
          type: "error",
          isLoading: false,
          autoClose: 5000,
        });
      })
      .finally(() => {
        // Mark this payment as no longer being verified
        setVerifyingPayments((prev) => ({
          ...prev,
          [invoice.id]: false,
        }));
      });
  };

  return (
    <AuthenticatedLayout>
      <Head title={__("My Plan")} />
      <ToastContainer position="top-right" theme="colored" />

      {flash.message && (
        <div className="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
          {flash.message}
        </div>
      )}

      {flash.error && (
        <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
          {flash.error}
        </div>
      )}

      <div className="lg:flex lg:space-x-10">
        <AccountNavi active={"withdraw"} />

        <div className="ml-0 w-full">
          <div className="mb-5 p-4 sm:p-8 bg-footer shadow">
            <header className="mb-3">
              <div className="flex items-start space-x-3">
                <div>
                  <BiMoney className="w-8 text-gray-primary h-8" />
                </div>
                <div>
                  <h2 className="text-lg md:text-xl font-medium text-gray-primary">
                    {__("My Plan")}
                  </h2>

                  <p className="mt-1 mb-2 text-sm text-gray-primary">
                    {__("Manage your subscription plan and payment details")}
                  </p>
                </div>
              </div>
            </header>

            {(!plan ||
              (plan.status !== "active" && plan.status !== "cancelled")) && (
              <div className="text-xl bg-footer text-gray-primary flex items-center space-x-4 mb-5">
                <FiAlertCircle className="w-10 h-10" />
                <div>{__("You don't have an active plan yet.")}</div>
              </div>
            )}

            {plan &&
              (plan.status === "active" || plan.status === "cancelled") && (
                <div className="flex flex-col space-y-5 mb-5">
                  <div className="bg-footer">
                    <div className="flex items-center justify-between mb-3">
                      <h3 className="text-xl font-semibold text-gray-primary">
                        {plan.name}
                      </h3>

                      {plan.is_cancelled && (
                        <span className="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm">
                          {__("Cancelled")}
                        </span>
                      )}
                    </div>

                    <div className="flex flex-wrap gap-4">
                      <div className="bg-black dark:bg-zinc-700 rounded-md p-4 flex-1">
                        <span className="block text-sm text-gray-500 dark:text-gray-400">
                          {__("Status")}
                        </span>
                        <span
                          className={`font-medium ${
                            plan.is_cancelled
                              ? "text-red-500"
                              : "text-green-600 dark:text-green-400"
                          }`}
                        >
                          {plan.is_cancelled
                            ? __("Cancelled (Active until expiry)")
                            : __("Active")}
                        </span>
                      </div>

                      <div className="bg-black dark:bg-zinc-700 rounded-md p-4 flex-1">
                        <span className="block text-sm text-gray-500 dark:text-gray-400">
                          {__("Price")}
                        </span>
                        <span className="font-medium text-gray-primary">
                          {plan.price_formatted}
                        </span>
                      </div>

                      <div className="bg-black dark:bg-zinc-700 rounded-md p-4 flex-1">
                        <span className="block text-sm text-gray-500 dark:text-gray-400">
                          {__("Expiry Date")}
                        </span>
                        <span className="font-medium text-gray-primary">
                          {plan.expire_date}
                        </span>
                      </div>

                      {plan.gateway && (
                        <div className="bg-black dark:bg-zinc-700 rounded-md p-4 flex-1">
                          <span className="block text-sm text-gray-500 dark:text-gray-400">
                            {__("Payment Method")}
                          </span>
                          <span className="font-medium text-gray-primary">
                            {plan.gateway}
                          </span>
                        </div>
                      )}
                    </div>

                    {!plan.is_cancelled && (
                      <div className="mt-6 flex space-x-4">
                        <div className="iq-button">
                          <PrimaryButton onClick={handleUpgrade}>
                            {__("Upgrade/Change Plan")}
                          </PrimaryButton>
                        </div>

                        <div className="iq-button">
                          <PrimaryButton
                            onClick={handleCancel}
                            className="border-red-500 hover:bg-red-500 hover:text-white"
                          >
                            {__("Cancel Plan")}
                          </PrimaryButton>
                        </div>
                      </div>
                    )}

                    {plan.is_cancelled && (
                      <div className="mt-6">
                        <div className="iq-button">
                          <PrimaryButton onClick={handleUpgrade}>
                            {__("Subscribe Again")}
                          </PrimaryButton>
                        </div>
                      </div>
                    )}
                  </div>

                  <div className="bg-footer">
                    <h3 className="text-xl font-semibold text-gray-primary mb-3">
                      {__("Billing History")}
                    </h3>

                    {plan.invoices && plan.invoices.length > 0 ? (
                      <div className="overflow-x-auto table-view table-responsive table-space">
                        <table
                          className="table border-collapse w-full bg-white text-stone-600 dataTable no-footer"
                          data-toggle="data-table"
                        >
                          <thead>
                            <tr>
                              <th className="p-3 uppercase text-center hidden lg:table-cell bg-footer-2 text-white">
                                {__("Date")}
                              </th>
                              <th className="p-3 uppercase text-center hidden lg:table-cell bg-footer-2 text-white">
                                {__("Amount")}
                              </th>
                              <th className="p-3 uppercase text-center hidden lg:table-cell bg-footer-2 text-white">
                                {__("Status")}
                              </th>
                              <th className="p-3 uppercase text-center hidden lg:table-cell bg-footer-2 text-white">
                                {__("Actions")}
                              </th>
                            </tr>
                          </thead>
                          <tbody>
                            {plan.invoices.map((invoice, index) => (
                              <tr key={index}>
                                <td className="w-full lg:w-auto p-3 text-center block lg:table-cell relative lg:static text-gray-primary bg-black">
                                  {invoice.date}
                                </td>
                                <td className="w-full lg:w-auto p-3 text-center block lg:table-cell relative lg:static text-gray-primary bg-black">
                                  {invoice.amount_formatted}
                                </td>
                                <td className="w-full lg:w-auto p-3 text-center block lg:table-cell relative lg:static text-gray-primary bg-black">
                                  <span
                                    className={`px-2 py-1 rounded-full text-xs ${
                                      invoice.status === "completed"
                                        ? "bg-green-100 text-green-800"
                                        : invoice.status === "pending"
                                        ? "bg-yellow-100 text-yellow-800"
                                        : invoice.status === "upgraded"
                                        ? "bg-blue-100 text-blue-800"
                                        : "bg-gray-100 text-gray-800"
                                    }`}
                                  >
                                    {invoice.status.charAt(0).toUpperCase() +
                                      invoice.status.slice(1)}
                                  </span>
                                </td>
                                <td className="text-center w-full lg:w-auto p-3 block lg:table-cell relative lg:static text-gray-primary bg-black">
                                  <div className="d-flex align-items-center list-user-action mx-auto">
                                    {invoice.status === "pending" ? (
                                      invoice.payment_id ? (
                                        <button
                                          onClick={() =>
                                            handleVerifyPayment(invoice)
                                          }
                                          disabled={
                                            verifyingPayments[invoice.id]
                                          }
                                          className="btn btn-sm btn-warning rounded me-2 px-2 py-1"
                                          title={__("Verify Payment")}
                                        >
                                          <i
                                            className={`fa-solid ${
                                              verifyingPayments[invoice.id]
                                                ? "fa-spinner fa-spin"
                                                : "fa-sync"
                                            } me-1`}
                                          ></i>
                                          {__("Verify Payment")}
                                        </button>
                                      ) : (
                                        <span className="text-xs text-gray-400 me-2">
                                          {__("No payment ID")}
                                        </span>
                                      )
                                    ) : null}

                                    {invoice.invoice_url ? (
                                      <a
                                        href={invoice.invoice_url}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        className="btn btn-sm btn-icon btn-success rounded"
                                        title={__("Download Invoice")}
                                      >
                                        <i className="fa-solid fa-download"></i>
                                      </a>
                                    ) : (
                                      <span className="text-xs text-gray-400">
                                        {__("No invoice")}
                                      </span>
                                    )}
                                  </div>
                                </td>
                              </tr>
                            ))}
                          </tbody>
                        </table>
                      </div>
                    ) : (
                      <div className="text-gray-500 dark:text-gray-400">
                        {__("No billing history available")}
                      </div>
                    )}
                  </div>
                </div>
              )}
          </div>
        </div>
      </div>

      {/* Cancel Confirmation Modal */}
      <Modal show={confirmingCancel} onClose={() => setConfirmingCancel(false)}>
        <div className="p-6">
          <h2 className="text-lg font-medium text-gray-900 text-gray-primary mb-4">
            {__("Cancel Subscription")}
          </h2>

          <p className="text-sm text-gray-primary mb-4">
            {__(
              "Are you sure you want to cancel your subscription? Your subscription will remain active until the end of the current billing period on "
            )}
            <strong>{plan?.expire_date}</strong>.
          </p>

          <p className="text-sm text-gray-primary mb-6">
            {__(
              "After cancellation, you'll still have access to your subscription until it expires."
            )}
          </p>

          <div className="mt-6 flex justify-end">
            <div className="iq-button">
              <PrimaryButton
                type="button"
                className=""
                onClick={() => setConfirmingCancel(false)}
              >
                {__("No, Keep My Subscription")}
              </PrimaryButton>
            </div>
            <div className="iq-button">
              <PrimaryButton
                type="button"
                className="ms-2"
                onClick={confirmCancel}
              >
                {__("Yes, Cancel Subscription")}
              </PrimaryButton>
            </div>
          </div>
        </div>
      </Modal>
    </AuthenticatedLayout>
  );
}
