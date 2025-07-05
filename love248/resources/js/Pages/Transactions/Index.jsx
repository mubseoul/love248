import React, { useState } from "react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link } from "@inertiajs/inertia-react";
import __ from "@/Functions/Translate";
import PrimaryButton from "@/Components/PrimaryButton";
import { format } from "date-fns";

export default function TransactionHistory({
  auth,
  transactions,
  filters,
  types,
  statuses,
}) {
  const [selectedType, setSelectedType] = useState(filters.type || "");
  const [selectedStatus, setSelectedStatus] = useState(filters.status || "");

  const handleFilter = () => {
    window.location.href = route("transactions.index", {
      type: selectedType,
      status: selectedStatus,
    });
  };

  const formatDate = (dateString) => {
    const date = new Date(dateString);
    return format(date, "PPP p"); // e.g., "Apr 29, 2021, 5:14 PM"
  };

  const getStatusBadgeClass = (status) => {
    switch (status) {
      case "completed":
        return "bg-green-100 text-green-800";
      case "pending":
        return "bg-yellow-100 text-yellow-800";
      case "failed":
        return "bg-red-100 text-red-800";
      default:
        return "bg-gray-100 text-gray-800";
    }
  };

  const formatTransactionType = (type) => {
    return type
      .split("_")
      .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
      .join(" ");
  };

  return (
    <AuthenticatedLayout auth={auth}>
      <Head title={__("Transaction History")} />

      <div className="p-4 sm:p-8 max-w-6xl mx-auto bg-footer shadow mb-5">
        <div className="flex justify-between items-center mb-6">
          <h3 className="text-3xl font-semibold text-gray-primary">
            {__("Transaction History")}
          </h3>
        </div>

        {/* Filters */}
        <div className="mb-6 flex flex-wrap items-end gap-4">
          <div>
            <label className="block text-sm font-medium text-gray-400 mb-1">
              {__("Transaction Type")}
            </label>
            <select
              value={selectedType}
              onChange={(e) => setSelectedType(e.target.value)}
              className="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-700 bg-gray-800 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md"
            >
              <option value="">{__("All Types")}</option>
              {types.map((type) => (
                <option key={type} value={type}>
                  {formatTransactionType(type)}
                </option>
              ))}
            </select>
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-400 mb-1">
              {__("Status")}
            </label>
            <select
              value={selectedStatus}
              onChange={(e) => setSelectedStatus(e.target.value)}
              className="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-700 bg-gray-800 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md"
            >
              <option value="">{__("All Statuses")}</option>
              {statuses.map((status) => (
                <option key={status} value={status}>
                  {status.charAt(0).toUpperCase() + status.slice(1)}
                </option>
              ))}
            </select>
          </div>

          <div>
            <PrimaryButton onClick={handleFilter}>{__("Filter")}</PrimaryButton>
          </div>
        </div>

        {/* Transactions Table */}
        <div className="overflow-x-auto">
          <table className="min-w-full divide-y divide-gray-700">
            <thead className="bg-gray-800">
              <tr>
                <th
                  scope="col"
                  className="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider"
                >
                  {__("Date")}
                </th>
                <th
                  scope="col"
                  className="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider"
                >
                  {__("Type")}
                </th>
                <th
                  scope="col"
                  className="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider"
                >
                  {__("Description")}
                </th>
                <th
                  scope="col"
                  className="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider"
                >
                  {__("Amount")}
                </th>
                <th
                  scope="col"
                  className="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider"
                >
                  {__("Status")}
                </th>
                <th
                  scope="col"
                  className="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider"
                >
                  {__("Actions")}
                </th>
              </tr>
            </thead>
            <tbody className="bg-gray-900 divide-y divide-gray-800">
              {transactions.data.length > 0 ? (
                transactions.data.map((transaction) => (
                  <tr key={transaction.id}>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                      {formatDate(transaction.created_at)}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                      {formatTransactionType(transaction.transaction_type)}
                    </td>
                    <td className="px-6 py-4 text-sm text-gray-300">
                      {transaction.description}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                      {transaction.currency} {transaction.amount}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      <span
                        className={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${getStatusBadgeClass(
                          transaction.status
                        )}`}
                      >
                        {transaction.status.charAt(0).toUpperCase() +
                          transaction.status.slice(1)}
                      </span>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      <Link
                        href={route("transactions.show", transaction.id)}
                        className="text-primary-500 hover:text-primary-400"
                      >
                        {__("View")}
                      </Link>
                    </td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td
                    colSpan="6"
                    className="px-6 py-4 text-center text-sm text-gray-400"
                  >
                    {__("No transactions found")}
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>

        {/* Pagination */}
        {transactions.data.length > 0 && (
          <div className="mt-4 flex justify-between items-center">
            <div className="text-sm text-gray-400">
              {__("Showing")} {transactions.from} {__("to")} {transactions.to}{" "}
              {__("of")} {transactions.total} {__("results")}
            </div>
            <div className="flex gap-2">
              {transactions.links.map((link, i) => (
                <Link
                  key={i}
                  href={link.url}
                  className={`px-4 py-2 text-sm rounded ${
                    link.active
                      ? "bg-primary-600 text-white"
                      : link.url
                      ? "bg-gray-800 text-gray-300 hover:bg-gray-700"
                      : "bg-gray-900 text-gray-500 cursor-not-allowed"
                  }`}
                  dangerouslySetInnerHTML={{ __html: link.label }}
                />
              ))}
            </div>
          </div>
        )}
      </div>
    </AuthenticatedLayout>
  );
}
