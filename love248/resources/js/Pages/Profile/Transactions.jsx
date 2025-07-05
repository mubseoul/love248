import __ from "@/Functions/Translate";
import { Link, Head, usePage } from "@inertiajs/inertia-react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { FiUserMinus } from "react-icons/fi";
import { BiReceipt } from "react-icons/bi";
import { Inertia } from "@inertiajs/inertia";
import AccountNavi from "../Channel/Partials/AccountNavi";
import PrimaryButton from "@/Components/PrimaryButton";

export default function Transactions({
  transactions,
  filters,
  types,
  statuses,
}) {
  const { auth, currency_symbol } = usePage().props;

  const formatTransactionType = (type) => {
    const typeMap = {
      'video_purchase': 'Video Purchase',
      'gallery_purchase': 'Gallery Purchase',
      'video_sale': 'Video Sale',
      'gallery_sale': 'Gallery Sale',
      'commission_received': 'Commission Received',
      'commission_deducted': 'Commission Deducted',
      'content_payout': 'Payout Received',
      'streamer_payout': 'Payout Sent',
      'tip_sent': 'Tip Sent',
      'tip_received': 'Tip Received',
      'subscription_purchase': 'Subscription Purchase',
      'subscription_cancellation': 'Subscription Cancelled',
      'token_purchase': 'Token Purchase',
      'private_stream_fee': 'Private Stream Fee',
      'room_rental': 'Room Rental'
    };
    
    return typeMap[type] || type
      .split("_")
      .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
      .join(" ");
  };

  const getStatusBadge = (status) => {
    const baseClasses = "px-2 py-1 text-xs font-medium rounded-full";
    switch (status) {
      case "completed":
        return `${baseClasses} bg-green-100 text-green-800`;
      case "pending":
        return `${baseClasses} bg-yellow-100 text-yellow-800`;
      case "failed":
        return `${baseClasses} bg-red-100 text-red-800`;
      default:
        return `${baseClasses} bg-gray-100 text-gray-800`;
    }
  };

  const getAmountColor = (amount) => {
    const numericAmount = parseFloat(amount);
    if (numericAmount > 0) {
      return 'text-green-600'; // Positive amounts in green
    } else if (numericAmount < 0) {
      return 'text-red-600'; // Negative amounts in red
    }
    return 'text-gray-primary'; // Zero or non-numeric in default color
  };

  const formatAmount = (amount, currency) => {
    const numericAmount = parseFloat(amount);
    const symbol = numericAmount >= 0 ? '+' : '';
    return `${symbol}${currency} ${amount}`;
  };

  const handleFilterChange = (type, status) => {
    const params = new URLSearchParams();
    if (type) params.append("type", type);
    if (status) params.append("status", status);

    Inertia.visit(route("profile.transactions") + "?" + params.toString());
  };

  return (
    <AuthenticatedLayout>
      <Head title={__("Transaction History")} />

      <div className="lg:flex lg:space-x-10">
        <AccountNavi active={"transactions"} />

        <div className="ml-0 w-full">
          <div className="mb-5 p-4 sm:p-8 bg-footer shadow">
            <header className="mb-3">
              <div className="flex items-start space-x-3">
                <div>
                  <BiReceipt className="w-8 h-8 text-gray-primary" />
                </div>
                <div>
                  <h2 className="text-lg md:text-xl font-medium text-gray-primary">
                    {__("Transaction History")}
                  </h2>
                  <p className="mt-1 mb-2 text-sm text-gray-primary">
                    {__(
                      "View all your transaction history and payment details"
                    )}
                  </p>
                </div>
              </div>
            </header>

            {/* Filters */}
            <div className="mb-6 flex flex-wrap items-end gap-4">
              <div>
                <label className="block text-sm font-medium text-gray-primary mb-1">
                  {__("Transaction Type")}
                </label>
                <select
                  value={filters?.type || ""}
                  onChange={(e) =>
                    handleFilterChange(e.target.value, filters?.status)
                  }
                  className="mt-1 block w-full pl-3 pr-10 py-2 text-base border border-gray-600 bg-gray-800 text-gray-primary focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md"
                >
                  <option value="">{__("All Types")}</option>
                  {types?.map((type) => (
                    <option key={type} value={type}>
                      {formatTransactionType(type)}
                    </option>
                  ))}
                </select>
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-primary mb-1">
                  {__("Status")}
                </label>
                <select
                  value={filters?.status || ""}
                  onChange={(e) =>
                    handleFilterChange(filters?.type, e.target.value)
                  }
                  className="mt-1 block w-full pl-3 pr-10 py-2 text-base border border-gray-600 bg-gray-800 text-gray-primary focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md"
                >
                  <option value="">{__("All Statuses")}</option>
                  {statuses?.map((status) => (
                    <option key={status} value={status}>
                      {status.charAt(0).toUpperCase() + status.slice(1)}
                    </option>
                  ))}
                </select>
              </div>
            </div>

            {transactions?.total === 0 && (
              <div className="text-xl text-gray-primary flex items-center space-x-4 mb-5">
                <FiUserMinus className="w-10 h-10" />
                <div>{__("You haven't made any transactions yet.")}</div>
              </div>
            )}

            {transactions?.total > 0 && (
              <div className="overflow-x-auto table-view table-responsive table-space">
                <table
                  className="table border-collapse w-full bg-white text-stone-600 dataTable no-footer"
                  data-toggle="data-table"
                >
                  <thead>
                    <tr>
                      <th className="p-3 uppercase text-center hidden lg:table-cell bg-footer-2 text-white">
                        {__("ID")}
                      </th>
                      <th className="p-3 uppercase text-center hidden lg:table-cell bg-footer-2 text-white">
                        {__("Type")}
                      </th>
                      <th className="p-3 uppercase text-center hidden lg:table-cell bg-footer-2 text-white">
                        {__("Description")}
                      </th>
                      <th className="p-3 uppercase text-center hidden lg:table-cell bg-footer-2 text-white">
                        {__("Amount")}
                      </th>
                      <th className="p-3 uppercase text-center hidden lg:table-cell bg-footer-2 text-white">
                        {__("Status")}
                      </th>
                      <th className="p-3 uppercase text-center hidden lg:table-cell bg-footer-2 text-white">
                        {__("Date")}
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    {transactions?.data?.map((transaction, index) => (
                      <tr key={index}>
                        <td className="w-full lg:w-auto p-3 text-center block lg:table-cell relative lg:static text-gray-primary bg-black">
                          #{transaction.id}
                        </td>
                        <td className="w-full lg:w-auto p-3 text-center block lg:table-cell relative lg:static text-gray-primary bg-black">
                          {formatTransactionType(transaction.transaction_type)}
                        </td>
                        <td className="w-full lg:w-auto p-3 text-center block lg:table-cell relative lg:static text-gray-primary bg-black">
                          {transaction.description || __("No description")}
                        </td>
                        <td className="w-full lg:w-auto p-3 text-center block lg:table-cell relative lg:static text-gray-primary bg-black">
                          <span className={getAmountColor(transaction.amount)}>
                            {formatAmount(transaction.amount, currency_symbol)}
                          </span>
                        </td>
                        <td className="w-full lg:w-auto p-3 text-center block lg:table-cell relative lg:static text-gray-primary bg-black">
                          <span className={getStatusBadge(transaction.status)}>
                            {transaction.status.charAt(0).toUpperCase() +
                              transaction.status.slice(1)}
                          </span>
                        </td>
                        <td className="w-full lg:w-auto p-3 text-center block lg:table-cell relative lg:static text-gray-primary bg-black">
                          {new Date(
                            transaction.created_at
                          ).toLocaleDateString()}
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            )}

            {transactions?.last_page > 1 && (
              <>
                <div className="mt-10 flex text-gray-primary my-3 text-sm">
                  {__("Page: :pageNumber of :lastPage", {
                    pageNumber: transactions.current_page,
                    lastPage: transactions.last_page,
                  })}
                </div>

                <div className="flex items-center">
                  <div className="iq-button">
                    <PrimaryButton
                      processing={transactions.prev_page_url ? false : true}
                      className="mr-3"
                      onClick={(e) => Inertia.visit(transactions.prev_page_url)}
                    >
                      {__("Previous")}
                    </PrimaryButton>
                  </div>

                  <div className="iq-button">
                    <PrimaryButton
                      processing={transactions.next_page_url ? false : true}
                      onClick={(e) => Inertia.visit(transactions.next_page_url)}
                    >
                      {__("Next")}
                    </PrimaryButton>
                  </div>
                </div>
              </>
            )}
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
