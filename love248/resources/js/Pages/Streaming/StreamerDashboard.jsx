import { useState } from "react";
import { usePage } from "@inertiajs/inertia-react";
import __ from "@/Functions/Translate";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import axios from "axios";
import { toast } from "react-toastify";
import {
  MdSchedule,
  MdHistory,
  MdPending,
  MdGeneratingTokens,
  MdCheckCircleOutline,
  MdCancel,
  MdBlock,
  MdMessage,
  MdAccessTime,
  MdAttachMoney,
  MdPerson,
  MdVideoCall,
} from "react-icons/md";

export default function StreamerDashboard({
  auth,
  pendingRequests,
  upcomingRequests,
  pastRequests,
  totalRequests,
}) {
  const [activeTab, setActiveTab] = useState("pending");
  const [loading, setLoading] = useState(false);
  const [requests, setRequests] = useState({
    pending: pendingRequests,
    upcoming: upcomingRequests,
    past: pastRequests,
  });

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
      case "rejected":
        return "bg-red-500";
      case "cancelled":
        return "bg-gray-600";
      case "no_show":
        return "bg-gray-500";
      case "expired":
        return "bg-purple-500";
      case "disputed":
        return "bg-purple-600";
      case "resolved":
        return "bg-green-600";
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

  // Check if stream has started (current time is past start time)
  const hasStreamStarted = (request) => {
    if (!request.requested_date || !request.requested_time) return false;
    const streamDateTime = new Date(
      `${request.requested_date}T${request.requested_time}`
    );
    return new Date() >= streamDateTime;
  };

  // Accept a request
  const acceptRequest = async (id) => {
    try {
      setLoading(true);
      const response = await axios.post(route("private-stream.accept", { id }));

      if (response.data.status) {
        toast.success(
          response.data.message || __("Request accepted successfully")
        );
        await refreshData();
      } else {
        toast.error(response.data.message || __("Failed to accept request"));
      }
    } catch (error) {
      console.error("Error accepting request:", error);
      toast.error(__("An error occurred while accepting the request"));
    } finally {
      setLoading(false);
    }
  };

  // Reject a request
  const rejectRequest = async (id) => {
    try {
      setLoading(true);
      const response = await axios.post(route("private-stream.reject", { id }));

      if (response.data.status) {
        toast.success(
          response.data.message || __("Request rejected successfully")
        );
        await refreshData();
      } else {
        toast.error(response.data.message || __("Failed to reject request"));
      }
    } catch (error) {
      console.error("Error rejecting request:", error);
      toast.error(__("An error occurred while rejecting the request"));
    } finally {
      setLoading(false);
    }
  };

  // Complete a stream
  const completeStream = async (id) => {
    try {
      setLoading(true);
      const response = await axios.post(
        route("private-stream.complete", { id })
      );

      if (response.data.status) {
        toast.success(
          response.data.message || __("Stream marked as completed successfully")
        );
        await refreshData();
      } else {
        toast.error(response.data.message || __("Failed to complete stream"));
      }
    } catch (error) {
      console.error("Error completing stream:", error);
      toast.error(__("An error occurred while completing the stream"));
    } finally {
      setLoading(false);
    }
  };

  // Cancel a stream (for upcoming requests before stream starts)
  const cancelStream = async (id) => {
    try {
      setLoading(true);
      const response = await axios.post(route("private-stream.cancel", { id }));

      if (response.data.status) {
        toast.success(
          response.data.message ||
            __("Stream cancelled and refunded successfully")
        );
        await refreshData();
      } else {
        toast.error(response.data.message || __("Failed to cancel stream"));
      }
    } catch (error) {
      console.error("Error cancelling stream:", error);
      toast.error(__("An error occurred while cancelling the stream"));
    } finally {
      setLoading(false);
    }
  };

  // Mark as no-show
  const markNoShow = async (id) => {
    try {
      setLoading(true);
      const response = await axios.post(
        route("private-stream.no-show", { id })
      );

      if (response.data.status) {
        toast.success(
          response.data.message || __("User marked as no-show successfully")
        );
        await refreshData();
      } else {
        toast.error(response.data.message || __("Failed to mark as no-show"));
      }
    } catch (error) {
      console.error("Error marking no-show:", error);
      toast.error(__("An error occurred while marking as no-show"));
    } finally {
      setLoading(false);
    }
  };

  // Refresh data
  const refreshData = async () => {
    try {
      const [pendingRes, upcomingRes, pastRes] = await Promise.all([
        axios.get(route("private-stream.pending-requests-json")),
        axios.get(route("private-stream.upcoming-streams")),
        axios.get(route("private-stream.my-requests")),
      ]);

      const allRequests = pastRes.data.requests || [];
      // Show all past requests regardless of status
      const filteredPastRequests = allRequests;

      setRequests({
        pending: pendingRes.data.requests || [],
        upcoming: upcomingRes.data.requests || [],
        past: filteredPastRequests,
      });
    } catch (error) {
      console.error("Error refreshing data:", error);
    }
  };

  // Get display requests based on active tab
  const getDisplayRequests = () => {
    return requests[activeTab] || [];
  };

  // Render action buttons based on tab
  const renderActionButtons = (request) => {
    switch (activeTab) {
      case "pending":
        return (
          <div className="flex space-x-2">
            <a
              href={route("private-stream.session", request.id)}
              className="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
              title={__("View Stream Details")}
            >
              <MdVideoCall className="h-5 w-5" />
            </a>
            <button
              className="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
              onClick={() => acceptRequest(request.id)}
              title={__("Accept Request")}
              disabled={loading}
            >
              <MdCheckCircleOutline className="h-5 w-5" />
            </button>
            <button
              className="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
              onClick={() => rejectRequest(request.id)}
              title={__("Reject Request")}
              disabled={loading}
            >
              <MdCancel className="h-5 w-5" />
            </button>
          </div>
        );

      case "upcoming":
        const streamStarted = hasStreamStarted(request);
        const isLive = request.status === "in_progress";
        return (
          <div className="flex space-x-2">
            <a
              href={route("private-stream.session", request.id)}
              className={`${
                isLive
                  ? "text-red-500 hover:text-red-700 animate-pulse"
                  : "text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
              }`}
              title={
                isLive
                  ? __("MANAGE LIVE STREAM")
                  : streamStarted
                  ? __("Start Stream")
                  : __("View Stream Details")
              }
            >
              <MdVideoCall className="h-5 w-5" />
            </a>
            {isLive ? (
              // Stream is currently live - show complete option
              <button
                className="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                onClick={() => completeStream(request.id)}
                title={__("End Stream")}
                disabled={loading}
              >
                <MdCheckCircleOutline className="h-5 w-5" />
              </button>
            ) : streamStarted ? (
              // Stream has started but not live - show complete and no-show options
              <>
                <button
                  className="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                  onClick={() => completeStream(request.id)}
                  title={__("Mark as Completed")}
                  disabled={loading}
                >
                  <MdCheckCircleOutline className="h-5 w-5" />
                </button>
                <button
                  className="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300"
                  onClick={() => markNoShow(request.id)}
                  title={__("Mark as No-Show")}
                  disabled={loading}
                >
                  <MdBlock className="h-5 w-5" />
                </button>
              </>
            ) : (
              // Stream hasn't started yet - show cancel option
              <button
                className="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                onClick={() => cancelStream(request.id)}
                title={__("Cancel Stream (Refund User)")}
                disabled={loading}
              >
                <MdCancel className="h-5 w-5" />
              </button>
            )}
          </div>
        );

      case "past":
        // Check if stream can still be acted upon
        const canTakeAction = request.status === "accepted" && hasStreamTimePassed(request);
        const isDisputed = request.status === "disputed";
        
        return (
          <div className="flex space-x-2">
            <a
              href={route("private-stream.session", request.id)}
              className="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
              title={__("View Stream")}
            >
              <MdVideoCall className="h-5 w-5" />
            </a>
            {canTakeAction ? (
              // Stream is expired but can still be completed or marked as no-show
              <>
                <button
                  className="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                  onClick={() => completeStream(request.id)}
                  title={__("Mark as Completed")}
                  disabled={loading}
                >
                  <MdCheckCircleOutline className="h-5 w-5" />
                </button>
                <button
                  className="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300"
                  onClick={() => markNoShow(request.id)}
                  title={__("Mark as No-Show")}
                  disabled={loading}
                >
                  <MdBlock className="h-5 w-5" />
                </button>
              </>
            ) : isDisputed ? (
              // Show dispute indicator
              <span className="text-purple-500" title={__("Stream is disputed")}>
                <MdBlock className="h-5 w-5" />
              </span>
            ) : (
              // No actions available
              <span className="text-gray-400">—</span>
            )}
          </div>
        );

      default:
        return null;
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
        {requests.pending.length > 0 && (
          <span className="ml-2 bg-yellow-500 text-white rounded-full px-2 py-0.5 text-xs">
            {requests.pending.length}
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
        {requests.upcoming.length > 0 && (
          <span className="ml-2 bg-green-500 text-white rounded-full px-2 py-0.5 text-xs">
            {requests.upcoming.length}
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
        {__("History")}
        {requests.past.length > 0 && (
          <span className="ml-2 bg-blue-500 text-white rounded-full px-2 py-0.5 text-xs">
            {requests.past.length}
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
          {__("Private Stream Management")}
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
                  <MdGeneratingTokens className="mr-3 h-8 w-8 text-primary" />
                  <div>
                    <h1 className="text-2xl font-bold text-gray-primary">
                      {__("Private Stream Management")}
                    </h1>
                    <p className="text-sm text-gray-primary">
                      {__("Manage your private streaming requests")} •{" "}
                      {totalRequests} {__("total requests")}
                    </p>
                  </div>
                </div>
              </div>

              {/* Tab Navigation */}
              {renderTabNavigation()}

              {/* Info for past tab */}
              {activeTab === "past" && (
                <div className="mb-4 p-3 bg-blue-900/30 border border-blue-500/30 rounded-lg">
                  <p className="text-sm text-blue-300">
                    <strong>{__("Note:")}</strong> {__("Streams marked as")}{" "}
                    <span className="px-1 bg-orange-500 rounded text-xs">
                      EXPIRED
                    </span>{" "}
                    {__(
                      "were accepted but the scheduled time has passed. You can still mark them as completed or no-show."
                    )}
                  </p>
                </div>
              )}

              {/* Content */}
              {currentRequests.length > 0 ? (
                <div className="overflow-x-auto">
                  <table className="min-w-full bg-black rounded-lg overflow-hidden">
                    <thead className="bg-black">
                      <tr>
                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-primary uppercase tracking-wider">
                          {__("User")}
                        </th>
                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-primary uppercase tracking-wider">
                          {__("Date & Time")}
                        </th>
                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-primary uppercase tracking-wider">
                          {__("Duration")}
                        </th>
                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-primary uppercase tracking-wider">
                          {activeTab === "past"
                            ? __("Status")
                            : __("Your Offer")}
                        </th>
                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-primary uppercase tracking-wider">
                          {__("Room Rental")}
                        </th>
                        <th className="px-6 py-3 text-center text-xs font-medium text-gray-primary uppercase tracking-wider">
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
                                  {request.user?.name || request.user?.username}
                                </div>
                                <div className="text-xs text-gray-500 dark:text-gray-400">
                                  @{request.user?.username}
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
                            {activeTab === "past" ? (
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
                                  {request.status === "interrupted" ? __("INTERRUPTED") : 
                                   request.status === "disputed" ? __("DISPUTED") : 
                                   request.status.toUpperCase()}
                                </span>
                              )
                            ) : (
                              <div className="flex items-center text-sm text-gray-primary">
                                <MdAttachMoney className="h-4 w-4 text-gray-400 mr-1" />
                                ${request.streamer_fee}
                              </div>
                            )}
                          </td>
                          <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-primary">
                            {request.room_rental_tokens} {__("tokens")}
                          </td>
                          <td className="px-6 py-4 whitespace-nowrap text-center">
                            <div className="flex justify-center space-x-2">
                              {renderActionButtons(request)}
                              {request.message && (
                                <button
                                  className="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                  title={__("View Message")}
                                >
                                  <MdMessage className="h-5 w-5" />
                                </button>
                              )}
                            </div>
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              ) : (
                <div className="text-center py-12">
                  <MdGeneratingTokens className="mx-auto h-12 w-12 text-gray-400" />
                  <h3 className="mt-2 text-sm font-medium text-gray-primary">
                    {activeTab === "pending" && __("No pending requests")}
                    {activeTab === "upcoming" && __("No upcoming streams")}
                    {activeTab === "past" && __("No past streams")}
                  </h3>
                  <p className="mt-1 text-sm text-gray-primary">
                    {activeTab === "pending" &&
                      __("New private stream requests will appear here.")}
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
