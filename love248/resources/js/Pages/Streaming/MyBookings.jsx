import { useState } from "react";
import { usePage } from "@inertiajs/inertia-react";
import __ from "@/Functions/Translate";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import {
  MdSchedule,
  MdHistory,
  MdPending,
  MdVideoCall,
  MdAccessTime,
  MdAttachMoney,
  MdMessage,
  MdPerson,
} from "react-icons/md";
import { BiCalendarEvent } from "react-icons/bi";

export default function MyBookings({
  auth,
  pendingRequests,
  upcomingRequests,
  pastRequests,
  totalRequests,
}) {
  const [activeTab, setActiveTab] = useState("upcoming");

  // Format date for display
  const formatDisplayDate = (date) => {
    if (!date) return "N/A";
    return new Date(date).toLocaleDateString();
  };

  // Format time for display
  const formatDisplayTime = (time) => {
    if (!time) return "N/A";
    return time;
  };

  // Get status badge color
  const getStatusBadgeClass = (status) => {
    switch (status) {
      case "pending":
        return "bg-yellow-500";
      case "accepted":
        return "bg-green-500";
      case "in_progress":
        return "bg-blue-500";
      case "completed":
        return "bg-green-700";
      case "interrupted":
        return "bg-orange-600";
      case "awaiting_feedback":
        return "bg-blue-600";
      case "completed_with_issues":
        return "bg-orange-600";
      case "streamer_no_show":
        return "bg-red-600";
      case "user_no_show":
        return "bg-orange-500";
      case "disputed":
        return "bg-purple-600";
      case "resolved":
        return "bg-green-600";
      case "rejected":
        return "bg-red-500";
      case "no_show":
        return "bg-gray-500";
      case "cancelled":
        return "bg-gray-600";
      case "expired":
        return "bg-purple-500";
      default:
        return "bg-gray-500";
    }
  };

  // Check if stream time has passed
  const hasStreamTimePassed = (request) => {
    if (!request.requested_date || !request.requested_time) return false;
    const streamDateTime = new Date(
      `${request.requested_date}T${request.requested_time}`
    );
    const endDateTime = new Date(
      streamDateTime.getTime() + request.duration_minutes * 60000
    );
    return endDateTime < new Date();
  };

  // Get display requests based on active tab
  const getDisplayRequests = () => {
    switch (activeTab) {
      case "pending":
        return pendingRequests;
      case "upcoming":
        return upcomingRequests;
      case "past":
        return pastRequests;
      default:
        return [];
    }
  };

  // Render tab navigation
  const renderTabNavigation = () => (
    <div className="flex space-x-4 mb-6">
      <button
        className={`flex items-center px-4 py-2 border-b-2 font-medium text-sm ${
          activeTab === "pending"
            ? "border-primary text-primary"
            : "border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300"
        }`}
        onClick={() => setActiveTab("pending")}
      >
        <MdPending className="mr-2 h-4 w-4" />
        {__("Pending")}
        {pendingRequests.length > 0 && (
          <span className="ml-2 bg-yellow-500 text-white rounded-full px-2 py-0.5 text-xs">
            {pendingRequests.length}
          </span>
        )}
      </button>

      <button
        className={`flex items-center px-4 py-2 border-b-2 font-medium text-sm ${
          activeTab === "upcoming"
            ? "border-primary text-primary"
            : "border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300"
        }`}
        onClick={() => setActiveTab("upcoming")}
      >
        <MdSchedule className="mr-2 h-4 w-4" />
        {__("Upcoming")}
        {upcomingRequests.length > 0 && (
          <span className="ml-2 bg-green-500 text-white rounded-full px-2 py-0.5 text-xs">
            {upcomingRequests.length}
          </span>
        )}
      </button>

      <button
        className={`flex items-center px-4 py-2 border-b-2 font-medium text-sm ${
          activeTab === "past"
            ? "border-primary text-primary"
            : "border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300"
        }`}
        onClick={() => setActiveTab("past")}
      >
        <MdHistory className="mr-2 h-4 w-4" />
        {__("Past")}
        {pastRequests.length > 0 && (
          <span className="ml-2 bg-red-700 text-white rounded-full px-2 py-0.5 text-xs">
            {pastRequests.length}
          </span>
        )}
      </button>
    </div>
  );

  const currentRequests = getDisplayRequests();

  return (
    <AuthenticatedLayout
      user={auth.user}
      header={
        <h2 className="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          {__("My Private Streams")}
        </h2>
      }
    >
      <div className="py-12">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div className="bg-footer overflow-hidden">
            <div className="p-6 text-gray-primary">
              {/* Header */}
              <div className="flex items-center justify-between mb-6">
                <div className="flex items-center">
                  <BiCalendarEvent className="mr-3 h-8 w-8 text-primary" />
                  <div>
                    <h1 className="text-2xl font-bold text-gray-primary">
                      {__("My Private Streams")}
                    </h1>
                    <p className="text-sm text-gray-primary">
                      {__("Manage your private streaming sessions")} •{" "}
                      {totalRequests} {__("total bookings")}
                    </p>
                  </div>
                </div>
              </div>

              {/* Tab Navigation */}
              {renderTabNavigation()}

              {/* Content */}
              {currentRequests.length > 0 ? (
                <div className="overflow-x-auto">
                  <table className="min-w-full bg-black rounded-lg overflow-hidden">
                    <thead className="bg-black">
                      <tr>
                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-primary uppercase tracking-wider">
                          {__("Streamer")}
                        </th>
                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-primary uppercase tracking-wider">
                          {__("Date & Time")}
                        </th>
                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-primary uppercase tracking-wider">
                          {__("Duration")}
                        </th>
                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-primary uppercase tracking-wider">
                          {__("Amount Paid")}
                        </th>
                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-primary uppercase tracking-wider">
                          {__("Status")}
                        </th>
                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-primary uppercase tracking-wider">
                          {__("Actions")}
                        </th>
                      </tr>
                    </thead>
                    <tbody className="bg-black">
                      {currentRequests.map((request) => (
                        <tr key={request.id} className="">
                          <td className="px-6 py-4 whitespace-nowrap">
                            <div className="flex items-center">
                              <MdPerson className="h-5 w-5 text-gray-400 mr-2" />
                              <div>
                                <div className="text-sm font-medium text-gray-primary">
                                  {request.streamer?.name ||
                                    request.streamer?.username}
                                </div>
                                <div className="text-xs text-gray-500 dark:text-gray-400">
                                  @{request.streamer?.username}
                                </div>
                              </div>
                            </div>
                          </td>
                          <td className="px-6 py-4 whitespace-nowrap">
                            <div className="flex items-center text-sm text-gray-primary">
                              <MdAccessTime className="h-4 w-4 text-gray-400 mr-2" />
                              <div>
                                <div>
                                  {formatDisplayDate(request.requested_date)}
                                </div>
                                <div className="text-xs text-gray-500 dark:text-gray-400">
                                  {formatDisplayTime(request.requested_time)}
                                </div>
                              </div>
                            </div>
                          </td>
                          <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-primary">
                            {request.duration_minutes} {__("minutes")}
                          </td>
                          <td className="px-6 py-4 whitespace-nowrap">
                            <div className="flex items-center text-sm text-gray-primary">
                              <MdAttachMoney className="h-4 w-4 text-gray-400 mr-1" />
                              {request.streamer_fee}
                            </div>
                            <div className="text-xs text-gray-500 dark:text-gray-400">
                              {request.room_rental_tokens} {__("tokens")}
                            </div>
                          </td>
                          <td className="px-6 py-4 whitespace-nowrap">
                            {activeTab === "past" &&
                            request.status === "accepted" &&
                            hasStreamTimePassed(request) ? (
                              <span className="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800 dark:bg-orange-800 dark:text-orange-200">
                                {__("EXPIRED")}
                              </span>
                            ) : request.status === "in_progress" ? (
                              <span className="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-500 text-white animate-pulse">
                                <span className="flex items-center">
                                  <span className="w-2 h-2 bg-white rounded-full mr-1 animate-ping"></span>
                                  {__("LIVE")}
                                </span>
                              </span>
                            ) : (
                              <span
                                className={`px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full text-white ${getStatusBadgeClass(
                                  request.status
                                )}`}
                              >
                                {request.status === "interrupted" ? __("INTERRUPTED") : request.status.toUpperCase()}
                              </span>
                            )}
                          </td>
                          <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            {request.message && (
                              <button
                                className="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-2"
                                title={__("View Message")}
                              >
                                <MdMessage className="h-5 w-5" />
                              </button>
                            )}
                            <a
                              href={route("private-stream.session", request.id)}
                              className={`${
                                request.status === "in_progress"
                                  ? "text-red-500 hover:text-red-700 animate-pulse"
                                  : "text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                              }`}
                              title={
                                request.status === "in_progress"
                                  ? __("JOIN LIVE STREAM")
                                  : activeTab === "upcoming"
                                  ? __("Join Stream")
                                  : activeTab === "pending"
                                  ? __("View Stream Details")
                                  : __("View Stream")
                              }
                            >
                              <MdVideoCall className="h-5 w-5" />
                            </a>
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              ) : (
                <div className="text-center py-12">
                  <BiCalendarEvent className="mx-auto h-12 w-12 text-gray-400" />
                  <h3 className="mt-2 text-sm font-medium text-gray-primary">
                    {activeTab === "pending" && __("No pending requests")}
                    {activeTab === "upcoming" && __("No upcoming streams")}
                    {activeTab === "past" && __("No past streams")}
                  </h3>
                  <p className="mt-1 text-sm text-gray-primary">
                    {activeTab === "pending" &&
                      __("Your private stream requests will appear here.")}
                    {activeTab === "upcoming" &&
                      __("Your confirmed streams will appear here.")}
                    {activeTab === "past" &&
                      __("Your completed streams will appear here.")}
                  </p>
                </div>
              )}
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
