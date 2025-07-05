import __ from "@/Functions/Translate";
import { Link, Head, usePage } from "@inertiajs/inertia-react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { FiUserMinus } from "react-icons/fi";
import { MdGeneratingTokens } from "react-icons/md";
import { GoCalendar } from "react-icons/go";
import SecondaryButton from "@/Components/SecondaryButton";
import { Inertia } from "@inertiajs/inertia";

export default function TokenOrders({ orders }) {
  const { auth, currency_symbol } = usePage().props;

  return (
    <AuthenticatedLayout>
      <Head title={__("Token Order History")} />

      <div className="ml-0">
        <div className="mt-5 p-4 sm:p-8 bg-footer mb-5">
          <header>
            <div className="flex items-start space-x-3">
              <div>
                <MdGeneratingTokens className="w-8 h-8 text-gray-primary" />
              </div>
              <div className="flex justify-between items-center w-full flex-wrap">
                <h2 className="text-lg md:text-xl font-medium text-gray-primary">
                  {__("Token Order History")}
                </h2>
                <div className="text-sm text-gray-primary">
                  {__("Balance: :balance", {
                    balance: auth.user.tokens,
                  })}
                </div>
              </div>
            </div>
          </header>

          <hr className="my-5" />

          {orders.total === 0 && (
            <div className="text-xl text-gray-primary flex items-center space-x-4">
              <FiUserMinus className="w-10 h-10" />
              <div>{__("You haven't ordered any tokens yet.")}</div>
            </div>
          )}

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
                    {__("Tokens")}
                  </th>
                  <th className="p-3 uppercase text-center hidden lg:table-cell bg-footer-2 text-white">
                    {__("Price")}
                  </th>
                  <th className="p-3 uppercase text-center hidden lg:table-cell bg-footer-2 text-white">
                    {__("Gateway")}
                  </th>
                  <th className="p-3 uppercase text-center hidden lg:table-cell bg-footer-2 text-white">
                    {__("Date")}
                  </th>
                </tr>
              </thead>
              <tbody>
                {orders.data?.map((t, index) => (
                  <tr key={index}>
                    <td className="w-full lg:w-auto p-3 text-center block lg:table-cell relative lg:static text-gray-primary bg-black">
                      {t.id}
                    </td>
                    <td className="w-full lg:w-auto p-3 text-center block lg:table-cell relative lg:static text-gray-primary bg-black">
                      {t.tokens}
                    </td>
                    <td className="w-full lg:w-auto p-3 text-center block lg:table-cell relative lg:static text-gray-primary bg-black">
                      {`${currency_symbol}${t.amount}`}
                    </td>
                    <td className="w-full lg:w-auto p-3 text-center block lg:table-cell relative lg:static text-gray-primary bg-black">
                      {t.gateway}
                    </td>
                    <td className="w-full lg:w-auto p-3 text-center block lg:table-cell relative lg:static text-gray-primary bg-black">
                      {t.created_at_human}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>

        {orders.last_page > 1 && (
          <>
            <div className="mt-10 flex text-gray-primary my-3 text-sm">
              {__("Page: :pageNumber of :lastPage", {
                pageNumber: orders.current_page,
                lastPage: orders.last_page,
              })}
            </div>

            <SecondaryButton
              processing={orders.prev_page_url ? false : true}
              className="mr-3"
              onClick={(e) => Inertia.visit(orders.prev_page_url)}
            >
              {__("Previous")}
            </SecondaryButton>

            <SecondaryButton
              processing={orders.next_page_url ? false : true}
              onClick={(e) => Inertia.visit(orders.next_page_url)}
            >
              {__("Next")}
            </SecondaryButton>
          </>
        )}
      </div>
    </AuthenticatedLayout>
  );
}
