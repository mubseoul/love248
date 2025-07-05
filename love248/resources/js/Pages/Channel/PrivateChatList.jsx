import __ from "@/Functions/Translate";
import {
  MdGeneratingTokens,
  MdCancel,
  MdCheckCircleOutline,
  MdOutlineCalendarMonth,
  MdAccessTime,
  MdAttachMoney,
  MdMessage,
  MdInfoOutline,
  MdSchedule,
  MdHistory,
  MdPending,
  MdBlock,
} from "react-icons/md";
import PrimaryButton from "@/Components/PrimaryButton";
import { useState, useEffect } from "react";
import Modal from "@/Components/Modal";
import axios from "axios";
import { toast } from "react-toastify";
import { formatDate, formatTime } from "@/Functions/helpers";

export default function PrivateChatList({ streamer, sendDataToParent }) {
  const [show, setShow] = useState(false);
  const [loading, setLoading] = useState(false);
  const [requests, setRequests] = useState([]);
  const [upcomingRequests, setUpcomingRequests] = useState([]);
  const [allRequests, setAllRequests] = useState([]); // For completed/rejected
  const [activeTab, setActiveTab] = useState("pending"); // 'pending', 'upcoming', 'history'
  const [selectedRequestId, setSelectedRequestId] = useState(null);
  const [messageModal, setMessageModal] = useState(false);
  const [selectedRequest, setSelectedRequest] = useState(null);

  // Load request counts on component mount
  useEffect(() => {
    loadRequestCounts();
  }, []);

  // Load request counts without showing modal
  const loadRequestCounts = async () => {
    try {
      setLoading(true);

      // Fetch pending requests
      const pendingResponse = await axios.get(
        route("private-stream.pending-requests-json")
      );

      // Fetch upcoming streams (accepted requests)
      const upcomingResponse = await axios.get(
        route("private-stream.upcoming-streams")
      );

      if (pendingResponse.data.status) {
        setRequests(pendingResponse.data.requests || []);
      }

      if (upcomingResponse.data.status) {
        setUpcomingRequests(upcomingResponse.data.requests || []);
      }
    } catch (error) {
      console.error("Error fetching request counts:", error);
    } finally {
      setLoading(false);
    }
  };

  // Fetch all types of requests
  const fetchAllRequests = async () => {
    try {
      setLoading(true);

      // Fetch pending requests
      const pendingResponse = await axios.get(
        route("private-stream.pending-requests-json")
      );

      // Fetch upcoming streams (accepted requests)
      const upcomingResponse = await axios.get(
        route("private-stream.upcoming-streams")
      );

      // Fetch all requests for history tab
      const allResponse = await axios.get(route("private-stream.my-requests"));

      if (pendingResponse.data.status) {
        setRequests(pendingResponse.data.requests || []);
      }

      if (upcomingResponse.data.status) {
        setUpcomingRequests(upcomingResponse.data.requests || []);
      }

      if (allResponse.data.status) {
        setAllRequests(allResponse.data.requests || []);
      }

      setShow(true);

      // Show toast only if we're on pending tab and no pending requests
      if (
        activeTab === "pending" &&
        (!pendingResponse.data.requests ||
          pendingResponse.data.requests.length === 0)
      ) {
        toast.info(__("No pending private stream requests"));
      }
    } catch (error) {
      console.error("Error fetching private stream requests:", error);
      toast.error(__("Failed to load private stream requests"));
    } finally {
      setLoading(false);
    }
  };

  // Legacy method for backward compatibility
  const fetchRequests = fetchAllRequests;

  // Accept a private stream request
  const acceptRequest = async (id) => {
    try {
      setLoading(true);
      const response = await axios.post(route("private-stream.accept", { id }));

      if (response.data.status) {
        toast.success(
          response.data.message || __("Request accepted successfully")
        );
        // Refresh all requests
        await fetchAllRequests();
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

  // Reject a private stream request
  const rejectRequest = async (id) => {
    try {
      setLoading(true);
      const response = await axios.post(route("private-stream.reject", { id }));

      if (response.data.status) {
        toast.success(
          response.data.message || __("Request rejected successfully")
        );
        // Refresh all requests
        await fetchAllRequests();
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

  // Complete a stream (for upcoming requests)
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
        // Refresh all requests
        await fetchAllRequests();
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
        // Refresh all requests
        await fetchAllRequests();
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

  // Mark as no-show (for upcoming requests)
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
        // Refresh all requests
        await fetchAllRequests();
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

  // Format date for display
  const formatDisplayDate = (date) => {
    if (!date) return "N/A";
    return new Date(date).toLocaleDateString();
  };

  // Get status badge color based on request status
  const getStatusBadgeClass = (status) => {
    switch (status) {
      case "pending":
        return "bg-yellow-500";
      case "accepted":
        return "bg-green-500";
      case "completed":
        return "bg-blue-500";
      case "rejected":
        return "bg-red-500";
      case "cancelled":
        return "bg-orange-500";
      case "no_show":
        return "bg-gray-500";
      case "expired":
        return "bg-purple-500";
      default:
        return "bg-gray-500";
    }
  };

  // Open message details modal
  const viewMessageDetails = (request) => {
    setSelectedRequest(request);
    setMessageModal(true);
  };

  // Close message details modal
  const closeMessageModal = () => {
    setMessageModal(false);
    setSelectedRequest(null);
  };

  // Helper function to check if a stream time has passed
  const hasStreamTimePassed = (request) => {
    if (!request.requested_date || !request.requested_time) return false;

    const streamDateTime = new Date(
      `${request.requested_date}T${request.requested_time}`
    );
    const endDateTime = new Date(
      streamDateTime.getTime() + request.duration_minutes * 60000
    );
    const now = new Date();

    return endDateTime < now;
  };

  // Helper function to check if a stream has started (current time is past start time)
  const hasStreamStarted = (request) => {
    if (!request.requested_date || !request.requested_time) return false;

    const streamDateTime = new Date(
      `${request.requested_date}T${request.requested_time}`
    );
    const now = new Date();

    return now >= streamDateTime;
  };

  // Get count for each tab
  const pendingCount = requests.length;
  const upcomingCount = upcomingRequests.length;
  const historyCount = allRequests.filter(
    (req) =>
      ["completed", "rejected", "no_show", "cancelled", "expired"].includes(req.status) ||
      (req.status === "accepted" && hasStreamTimePassed(req))
  ).length;

  // Filter requests based on active tab
  const getDisplayRequests = () => {
    switch (activeTab) {
      case "pending":
        return requests;
      case "upcoming":
        return upcomingRequests;
      case "history":
        return allRequests.filter(
          (req) =>
            ["completed", "rejected", "no_show", "cancelled", "expired"].includes(
              req.status
            ) ||
            (req.status === "accepted" && hasStreamTimePassed(req))
        );
      default:
        return [];
    }
  };

  // Render action buttons based on request status and tab
  const renderActionButtons = (request) => {
    switch (activeTab) {
      case "pending":
        return (
          <div className="flex justify-center space-x-2">
            <button
              className="text-green-500 hover:text-green-400 transition-colors"
              onClick={() => acceptRequest(request.id)}
              title={__("Accept Request")}
            >
              <MdCheckCircleOutline className="h-6 w-6" />
            </button>
            <button
              className="text-red-500 hover:text-red-400 transition-colors"
              onClick={() => rejectRequest(request.id)}
              title={__("Reject Request")}
            >
              <MdCancel className="h-6 w-6" />
            </button>
            {request.message && (
              <button
                className="text-blue-500 hover:text-blue-400 transition-colors"
                onClick={() => viewMessageDetails(request)}
                title={__("View Message")}
              >
                <MdMessage className="h-6 w-6" />
              </button>
            )}
          </div>
        );

      case "upcoming":
        const streamStarted = hasStreamStarted(request);
        return (
          <div className="flex justify-center space-x-2">
            {streamStarted ? (
              // Stream has started - show complete and no-show options
              <>
                <button
                  className="text-green-500 hover:text-green-400 transition-colors"
                  onClick={() => completeStream(request.id)}
                  title={__("Mark as Completed")}
                >
                  <MdCheckCircleOutline className="h-6 w-6" />
                </button>
                <button
                  className="text-gray-500 hover:text-gray-400 transition-colors"
                  onClick={() => markNoShow(request.id)}
                  title={__("Mark as No-Show")}
                >
                  <MdBlock className="h-6 w-6" />
                </button>
              </>
            ) : (
              // Stream hasn't started yet - show cancel option
              <button
                className="text-red-500 hover:text-red-400 transition-colors"
                onClick={() => cancelStream(request.id)}
                title={__("Cancel Stream (Refund User)")}
              >
                <MdCancel className="h-6 w-6" />
              </button>
            )}
            {request.message && (
              <button
                className="text-blue-500 hover:text-blue-400 transition-colors"
                onClick={() => viewMessageDetails(request)}
                title={__("View Message")}
              >
                <MdMessage className="h-6 w-6" />
              </button>
            )}
          </div>
        );

      case "history":
        return (
          <div className="flex justify-center space-x-2">
            {request.status === "accepted" && hasStreamTimePassed(request) ? (
              <>
                <span className="px-2 py-1 rounded text-white text-xs bg-orange-500">
                  EXPIRED
                </span>
                <button
                  className="text-green-500 hover:text-green-400 transition-colors"
                  onClick={() => completeStream(request.id)}
                  title={__("Mark as Completed")}
                >
                  <MdCheckCircleOutline className="h-5 w-5" />
                </button>
                <button
                  className="text-gray-500 hover:text-gray-400 transition-colors"
                  onClick={() => markNoShow(request.id)}
                  title={__("Mark as No-Show")}
                >
                  <MdBlock className="h-5 w-5" />
                </button>
              </>
            ) : (
              <span
                className={`px-2 py-1 rounded text-white text-xs ${getStatusBadgeClass(
                  request.status
                )}`}
              >
                {request.status.toUpperCase()}
              </span>
            )}
            {request.message && (
              <button
                className="text-blue-500 hover:text-blue-400 transition-colors"
                onClick={() => viewMessageDetails(request)}
                title={__("View Message")}
              >
                <MdMessage className="h-6 w-6" />
              </button>
            )}
          </div>
        );

      default:
        return null;
    }
  };

  // Render tab navigation
  const renderTabNavigation = () => (
    <div className="flex space-x-4 mb-4 border-b border-gray-700">
      <button
        className={`flex items-center px-4 py-2 border-b-2 font-medium text-sm ${
          activeTab === "pending"
            ? "border-primary text-primary"
            : "border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-300"
        }`}
        onClick={() => setActiveTab("pending")}
      >
        <MdPending className="mr-2 h-4 w-4" />
        {__("Pending")}
        {pendingCount > 0 && (
          <span className="ml-2 bg-yellow-500 text-black rounded-full px-2 py-0.5 text-xs">
            {pendingCount}
          </span>
        )}
      </button>

      <button
        className={`flex items-center px-4 py-2 border-b-2 font-medium text-sm ${
          activeTab === "upcoming"
            ? "border-primary text-primary"
            : "border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-300"
        }`}
        onClick={() => setActiveTab("upcoming")}
      >
        <MdSchedule className="mr-2 h-4 w-4" />
        {__("Upcoming")}
        {upcomingCount > 0 && (
          <span className="ml-2 bg-green-500 text-black rounded-full px-2 py-0.5 text-xs">
            {upcomingCount}
          </span>
        )}
      </button>

      <button
        className={`flex items-center px-4 py-2 border-b-2 font-medium text-sm ${
          activeTab === "history"
            ? "border-primary text-primary"
            : "border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-300"
        }`}
        onClick={() => setActiveTab("history")}
      >
        <MdHistory className="mr-2 h-4 w-4" />
        {__("History")}
        {historyCount > 0 && (
          <span className="ml-2 bg-blue-500 text-black rounded-full px-2 py-0.5 text-xs">
            {historyCount}
          </span>
        )}
      </button>
    </div>
  );

  const currentRequests = getDisplayRequests();

  return (
    <>
      <Modal show={show} onClose={() => setShow(false)} maxWidth="w-full">
        <div className="p-5">
          <h3 className="text-xl mb-4 flex items-center font-semibold text-gray-primary">
            <MdGeneratingTokens className="mr-2 h-6 w-6" />
            {__("Private Stream Management")}
          </h3>

          {renderTabNavigation()}

          {activeTab === "history" && (
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

          {loading ? (
            <div className="flex justify-center my-8">
              <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary"></div>
            </div>
          ) : currentRequests.length > 0 ? (
            <div className="overflow-x-auto">
              <table className="min-w-full bg-gray-800 rounded-lg overflow-hidden">
                <thead className="bg-gray-700">
                  <tr>
                    <th className="px-4 py-3 text-left text-sm font-medium text-gray-300">
                      {__("User")}
                    </th>
                    <th className="px-4 py-3 text-left text-sm font-medium text-gray-300">
                      {__("Date")}
                    </th>
                    <th className="px-4 py-3 text-left text-sm font-medium text-gray-300">
                      {__("Time")}
                    </th>
                    <th className="px-4 py-3 text-left text-sm font-medium text-gray-300">
                      {__("Duration")}
                    </th>
                    <th className="px-4 py-3 text-left text-sm font-medium text-gray-300">
                      {activeTab === "history"
                        ? __("Status")
                        : __("Your Offer")}
                    </th>
                    <th className="px-4 py-3 text-left text-sm font-medium text-gray-300">
                      {__("Room Rental")}
                    </th>
                    <th className="px-4 py-3 text-center text-sm font-medium text-gray-300">
                      {__("Actions")}
                    </th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-gray-700">
                  {currentRequests.map((request) => (
                    <tr key={request.id} className="hover:bg-gray-700/50">
                      <td className="px-4 py-3 text-sm text-gray-300">
                        {request.user?.name || request.user?.username}
                      </td>
                      <td className="px-4 py-3 text-sm text-gray-300">
                        {formatDisplayDate(request.requested_date)}
                      </td>
                      <td className="px-4 py-3 text-sm text-gray-300">
                        {request.requested_time}
                      </td>
                      <td className="px-4 py-3 text-sm text-gray-300">
                        {request.duration_minutes} {__("min")}
                      </td>
                      <td className="px-4 py-3 text-sm text-gray-300">
                        {activeTab === "history" ? (
                          request.status === "accepted" &&
                          hasStreamTimePassed(request) ? (
                            <span className="px-2 py-1 rounded text-white text-xs bg-orange-500">
                              EXPIRED
                            </span>
                          ) : (
                            <span
                              className={`px-2 py-1 rounded text-white text-xs ${getStatusBadgeClass(
                                request.status
                              )}`}
                            >
                              {request.status.toUpperCase()}
                            </span>
                          )
                        ) : (
                          `$${request.streamer_fee}`
                        )}
                      </td>
                      <td className="px-4 py-3 text-sm text-gray-300">
                        {request.room_rental_tokens} {__("tokens")}
                      </td>
                      <td className="px-4 py-3 text-center">
                        {renderActionButtons(request)}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          ) : (
            <div className="text-center py-8 bg-gray-800 rounded-lg">
              <p className="text-gray-400">
                {activeTab === "pending" &&
                  __("No pending private stream requests")}
                {activeTab === "upcoming" &&
                  __("No upcoming streams scheduled")}
                {activeTab === "history" && __("No completed streams yet")}
              </p>
            </div>
          )}
        </div>
      </Modal>

      <div className="iq-button">
        <a href={route("private-stream.pending-requests")}>
          <PrimaryButton
            className={`btn btn-sm inline-flex items-center ${
              pendingCount + upcomingCount > 0 ? "btn-success" : "btn-primary"
            }`}
          >
            {pendingCount + upcomingCount > 0 ? (
              <span className="flex items-center">
                {__("Private Requests")}
                <span className="ml-1 bg-white text-black rounded-full px-1 text-xs min-w-[16px] h-4 flex items-center justify-center">
                  {pendingCount + upcomingCount}
                </span>
              </span>
            ) : (
              __("Private Requests")
            )}
          </PrimaryButton>
        </a>
      </div>

      {/* Message details modal */}
      <Modal show={messageModal} onClose={closeMessageModal} maxWidth="md">
        {selectedRequest && (
          <div className="p-5">
            <h3 className="text-xl mb-4 flex items-center font-semibold text-gray-primary">
              <MdMessage className="mr-2 h-6 w-6" />
              {__("Message from")}{" "}
              {selectedRequest.user?.name || selectedRequest.user?.username}
            </h3>

            <div className="bg-gray-800 rounded-lg p-4 mb-4">
              <p className="text-gray-300 whitespace-pre-wrap">
                {selectedRequest.message || __("No message provided.")}
              </p>
            </div>

            <div className="bg-gray-800/50 rounded-lg p-4 mb-4">
              <h4 className="text-sm font-medium text-gray-400 mb-2">
                {__("Request Details")}
              </h4>
              <div className="grid grid-cols-2 gap-3 text-sm">
                <div>
                  <span className="text-gray-400">{__("Date:")}</span>
                  <p className="text-gray-300">
                    {formatDisplayDate(selectedRequest.requested_date)}
                  </p>
                </div>
                <div>
                  <span className="text-gray-400">{__("Time:")}</span>
                  <p className="text-gray-300">
                    {selectedRequest.requested_time}
                  </p>
                </div>
                <div>
                  <span className="text-gray-400">{__("Duration:")}</span>
                  <p className="text-gray-300">
                    {selectedRequest.duration_minutes} {__("minutes")}
                  </p>
                </div>
                <div>
                  <span className="text-gray-400">{__("Your Offer:")}</span>
                  <p className="text-gray-300">
                    ${selectedRequest.streamer_fee}
                  </p>
                </div>
              </div>
            </div>

            <div className="flex justify-center space-x-4">
              {activeTab === "pending" && (
                <>
                  <PrimaryButton
                    onClick={() => {
                      closeMessageModal();
                      acceptRequest(selectedRequest.id);
                    }}
                    className="bg-green-600 hover:bg-green-700"
                  >
                    {__("Accept Request")}
                  </PrimaryButton>
                  <PrimaryButton
                    onClick={() => {
                      closeMessageModal();
                      rejectRequest(selectedRequest.id);
                    }}
                    className="bg-red-600 hover:bg-red-700"
                  >
                    {__("Reject Request")}
                  </PrimaryButton>
                </>
              )}
              {activeTab === "upcoming" && (
                <>
                  {selectedRequest && hasStreamStarted(selectedRequest) ? (
                    // Stream has started - show complete and no-show options
                    <>
                      <PrimaryButton
                        onClick={() => {
                          closeMessageModal();
                          completeStream(selectedRequest.id);
                        }}
                        className="bg-green-600 hover:bg-green-700"
                      >
                        {__("Mark Completed")}
                      </PrimaryButton>
                      <PrimaryButton
                        onClick={() => {
                          closeMessageModal();
                          markNoShow(selectedRequest.id);
                        }}
                        className="bg-gray-600 hover:bg-gray-700"
                      >
                        {__("Mark No-Show")}
                      </PrimaryButton>
                    </>
                  ) : (
                    // Stream hasn't started yet - show cancel option
                    <PrimaryButton
                      onClick={() => {
                        closeMessageModal();
                        cancelStream(selectedRequest.id);
                      }}
                      className="bg-red-600 hover:bg-red-700"
                    >
                      {__("Cancel Stream (Refund User)")}
                    </PrimaryButton>
                  )}
                </>
              )}
              {activeTab === "history" &&
                selectedRequest.status === "accepted" &&
                hasStreamTimePassed(selectedRequest) && (
                  <>
                    <PrimaryButton
                      onClick={() => {
                        closeMessageModal();
                        completeStream(selectedRequest.id);
                      }}
                      className="bg-green-600 hover:bg-green-700"
                    >
                      {__("Mark Completed")}
                    </PrimaryButton>
                    <PrimaryButton
                      onClick={() => {
                        closeMessageModal();
                        markNoShow(selectedRequest.id);
                      }}
                      className="bg-gray-600 hover:bg-gray-700"
                    >
                      {__("Mark No-Show")}
                    </PrimaryButton>
                  </>
                )}
              <PrimaryButton onClick={closeMessageModal}>
                {__("Close")}
              </PrimaryButton>
            </div>
          </div>
        )}
      </Modal>
    </>
  );
}
