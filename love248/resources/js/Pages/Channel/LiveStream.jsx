import React, { Fragment } from "react";
import Front from "@/Layouts/Front";
import __ from "@/Functions/Translate";
import { useState, useEffect, useRef } from "react";
import PrimaryButton from "@/Components/PrimaryButton";
import ChatRoom from "./ChatRoom";
import VideoJS from "./Partials/VideoJs";
import StreamInstructions from "./StreamInstructions";
import { usePage } from "@inertiajs/inertia-react";
import { Inertia } from "@inertiajs/inertia";
import Modal from "@/Components/Modal";
import axios from "axios";
import { toast } from "react-toastify";

export default function LiveStream({
  isChannelOwner,
  streamUser,
  roomName,
  stripePublicKey,
}) {
  const [isRoomOffline, setIsRoomOffline] = useState(
    streamUser.live_status === "online" ? false : true
  );

  const [showReportModal, setShowReportModal] = useState(false);
  const [reportForm, setReportForm] = useState({
    reason: "",
    email: "",
  });
  const [reportSubmitting, setReportSubmitting] = useState(false);

  const { auth } = usePage().props;

  const playerRef = useRef(null);

  // Get environment variables for streaming
  const { hls_url } = usePage().props;

  // Create dynamic stream URL using environment variable and user's streaming key
  const streamUrl = `${hls_url}/${roomName}.m3u8`;

  // Check if current user is the streamer
  const isStreamer = auth?.user?.username === streamUser.username;

  const videoJsOptions = {
    autoplay: true,
    controls: true,
    responsive: true,
    fill: true,
    preload: "auto",
    fluid: true,
    liveui: true,
    html5: {
      vhs: {
        overrideNative: true,
      },
      nativeAudioTracks: false,
      nativeVideoTracks: false,
    },
    sources: [
      {
        src: streamUrl,
        type: "application/x-mpegURL",
      },
    ],
  };

  const handlePlayerReady = (player) => {
    playerRef.current = player;

    player.on("waiting", () => {
      console.log("player is waiting");
    });

    player.on("dispose", () => {
      console.log("player will dispose");
    });

    player.on("error", (error) => {
      console.error("VideoJS Error:", error);
      console.log("Error details:", {
        code: player.error()?.code,
        message: player.error()?.message,
        type: player.error()?.type,
        streamUrl: player.currentSource().src,
      });
    });

    player.on("loadedmetadata", () => {
      console.log("Stream metadata loaded successfully");
    });

    player.on("playing", () => {
      console.log("Stream is now playing");
    });

    // Initial play attempt
    player.ready(() => {
      console.log("Player is ready, attempting to play...");
      player.play().catch((error) => {
        console.log("Initial play failed:", error);
      });
    });
  };

  // Handle report form submission
  const handleReportSubmit = async (e) => {
    e.preventDefault();
    
    if (!reportForm.reason || !reportForm.email) {
      toast.error(__("Please fill in all fields."));
      return;
    }

    setReportSubmitting(true);

    try {
      const response = await axios.post(route("stream.report"), {
        reason: reportForm.reason,
        email: reportForm.email,
        id: streamUser.id,
        item_id: window.location.href,
      });

      if (response.data.success) {
        toast.success(__("Report submitted successfully!"));
        setShowReportModal(false);
        setReportForm({ reason: "", email: "" });
      } else {
        toast.error(__("Failed to submit report. Please try again."));
      }
    } catch (error) {
      console.error("Error submitting report:", error);
      const errorMessage = error.response?.data?.message || __("An error occurred. Please try again.");
      toast.error(errorMessage);
    } finally {
      setReportSubmitting(false);
    }
  };

  // stream setup
  useEffect(() => {
    window.Echo.channel("LiveStreamRefresh").listen(
      ".livestreams.refresh",
      (data) => {
        console.log(`refresh livestreams`);
        Inertia.reload();
      }
    );

    // listen for public live streaming events only
    window.Echo.channel(`room-${streamUser?.username}`)
      .listen(".livestream.started", (data) => {
        setIsRoomOffline(false);
      })
      .listen(".livestream.ban", (data) => {
        window.location.href = route("channel.bannedFromRoom", {
          user: streamUser?.username,
        });
      })
      .listen(".livestream.stopped", (data) => {
        setIsRoomOffline(true);
      });
  }, []);

  return (
    <Front
      extraHeader={false}
      extraHeaderTitle={__("@:username's Live Stream", {
        username: streamUser.username,
      })}
      extraHeaderImage="/images/live-stream-icon.png"
      extraImageHeight="h-10"
    >
      <div className="mt-[110px] flex max-w-7xl flex-col lg:flex-row lg:justify-between items-start h-fit mb-5">
        <div className="w-full h-full">
          {/* Show appropriate content based on stream status and user type */}
          {isRoomOffline ? (
            <>
              {/* For streamers when offline - show instructions only */}
              {isStreamer && (
                <StreamInstructions
                  streamKey={roomName}
                  streamUser={streamUser.username}
                />
              )}

              {/* For viewers when offline */}
              {!isStreamer && (
                <>
                  {streamUser.message_video && (
                    <video
                      src={`/storage/${streamUser.message_video}`}
                      className="w-full pe-4"
                      autoPlay
                      muted
                      controls
                    />
                  )}
                  <div className="bg-footer mb-5 dark:bg-zinc-900 mr-10 p-5 text-center">
                    <h2 className="text-xl font-semibold text-gray-primary mb-3">
                      {__("Stream is Offline")}
                    </h2>
                    <p className="text-gray-primary">
                      {__(
                        "@:username is currently offline. Please check back later!",
                        {
                          username: streamUser.username,
                        }
                      )}
                    </p>
                  </div>
                </>
              )}
            </>
          ) : (
            <>
              {/* When live - show video player first (for everyone) */}
              <div className="mb-5 mr-10">
                <VideoJS options={videoJsOptions} onReady={handlePlayerReady} />
                
                {/* Report button for non-streamers */}
                {!isStreamer && auth?.user && (
                  <div className="mt-3 flex justify-end">
                    <button
                      onClick={() => setShowReportModal(true)}
                      className="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm flex items-center gap-2 transition-colors"
                    >
                      <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 14.5c-.77.833.192 2.5 1.732 2.5z" />
                      </svg>
                      {__("Report Stream")}
                    </button>
                  </div>
                )}
              </div>

              {/* For streamers when live - show instructions below video */}
              {isStreamer && (
                <StreamInstructions
                  streamKey={roomName}
                  streamUser={streamUser.username}
                />
              )}
            </>
          )}
        </div>
        <ChatRoom stripePublicKey={stripePublicKey} streamer={streamUser} />
      </div>

      {/* Report Stream Modal */}
      <Modal show={showReportModal} onClose={() => setShowReportModal(false)} maxWidth="md">
        <div className="p-6">
          <h3 className="text-xl font-semibold text-gray-primary mb-4 flex items-center">
            <svg className="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 14.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
            {__("Report Stream")}
          </h3>
          
          <form onSubmit={handleReportSubmit}>
            <div className="mb-4">
              <label className="block text-sm font-medium text-gray-primary mb-2">
                {__("Your Email")}
              </label>
              <input
                type="email"
                value={reportForm.email}
                onChange={(e) => setReportForm({ ...reportForm, email: e.target.value })}
                className="w-full form-control"
                placeholder={__("Enter your email address")}
                required
              />
            </div>

            <div className="mb-6">
              <label className="block text-sm font-medium text-gray-primary mb-2">
                {__("Reason for Report")}
              </label>
              <select
                value={reportForm.reason}
                onChange={(e) => setReportForm({ ...reportForm, reason: e.target.value })}
                className="w-full form-control"
                required
              >
                <option value="">{__("Select a reason")}</option>
                <option value="inappropriate_content">{__("Inappropriate Content")}</option>
                <option value="harassment">{__("Harassment")}</option>
                <option value="spam">{__("Spam")}</option>
                <option value="violence">{__("Violence")}</option>
                <option value="nudity">{__("Nudity/Sexual Content")}</option>
                <option value="copyright">{__("Copyright Violation")}</option>
                <option value="other">{__("Other")}</option>
              </select>
            </div>

            <div className="flex justify-end gap-3">
              <button
                type="button"
                onClick={() => setShowReportModal(false)}
                className="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors"
                disabled={reportSubmitting}
              >
                {__("Cancel")}
              </button>
              <button
                type="submit"
                disabled={reportSubmitting}
                className="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-md transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {reportSubmitting ? __("Submitting...") : __("Submit Report")}
              </button>
            </div>
          </form>
        </div>
      </Modal>
    </Front>
  );
}
