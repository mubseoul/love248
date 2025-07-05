import { useState, useEffect, useMemo } from "react";
import { usePage } from "@inertiajs/inertia-react";
import __ from "@/Functions/Translate";
import { toast } from 'react-toastify';

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import PrivateStreamChat from "@/Components/PrivateStreamChat";
import PrivateStreamControls from "@/Components/PrivateStreamControls";
import PrivateStreamRealtimeListener from "@/Components/PrivateStreamRealtimeListener";
import PrivateStreamInstructions from "@/Components/PrivateStreamInstructions";
import PrivateStreamFeedback from "@/Components/PrivateStreamFeedback";
import VideoJS from "@/Pages/Channel/Partials/VideoJs";

import CalendarIntegration from "@/Components/CalendarIntegration";
import {
  MdVideoCall,
  MdPerson,
  MdAccessTime,
  MdAttachMoney,
  MdChat,
  MdClose,
  MdEdit,
  MdCheckCircle,
} from "react-icons/md";

// Component to display submitted feedback
function SubmittedFeedbackDisplay({ feedback, isStreamer, onEditFeedback }) {
  const formatRating = (rating) => {
    const stars = '★'.repeat(rating) + '☆'.repeat(5 - rating);
    return `${stars} (${rating}/5)`;
  };

  const formatExperience = (experience) => {
    const experiences = {
      'excellent': __('Excellent'),
      'good': __('Good'),
      'average': __('Average'),
      'poor': __('Poor'),
      'terrible': __('Terrible')
    };
    return experiences[experience] || experience;
  };

  return (
    <div className="bg-footer border border-gray-600 rounded-xl shadow-xl p-6">
      <div className="text-center mb-6">
        <div className="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
          <MdCheckCircle className="text-3xl text-white" />
        </div>
        <h3 className="text-2xl font-bold text-gray-primary mb-2">
          {__('Feedback Submitted')}
        </h3>
        <p className="text-gray-400">
          {__('Thank you for your feedback!')}
        </p>
      </div>

      <div className="space-y-4">
        {/* Rating Display */}
        <div className="bg-gray-800 p-4 rounded-lg">
          <h4 className="text-lg font-medium text-gray-primary mb-2">
            {__('Your Rating')}
          </h4>
          <div className="text-center">
            <div className="text-3xl text-yellow-400 mb-2">
              {formatRating(feedback.rating)}
            </div>
          </div>
        </div>

        {/* Comment */}
        {feedback.comment && (
          <div className="bg-gray-800 p-4 rounded-lg">
            <h4 className="text-lg font-medium text-gray-primary mb-2">
              {__('Your Comment')}
            </h4>
            <p className="text-gray-300 italic">"{feedback.comment}"</p>
          </div>
        )}

        {/* Experience Details */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div className="bg-gray-800 p-4 rounded-lg">
            <h4 className="text-sm font-medium text-gray-primary mb-2">
              {__('Overall Experience')}
            </h4>
            <p className="text-gray-300">{formatExperience(feedback.overall_experience)}</p>
          </div>

          <div className="bg-gray-800 p-4 rounded-lg">
            <h4 className="text-sm font-medium text-gray-primary mb-2">
              {__('Would Recommend')}
            </h4>
            <p className="text-gray-300">
              {feedback.would_recommend ? __('Yes') : __('No')}
            </p>
          </div>
        </div>

        {/* Issues Reported */}
        {(feedback.technical_issues || feedback.inappropriate_behavior) && (
          <div className="bg-red-900/20 border border-red-600 p-4 rounded-lg">
            <h4 className="text-lg font-medium text-red-400 mb-2">
              {__('Issues Reported')}
            </h4>
            {feedback.technical_issues && (
              <div className="mb-2">
                <p className="text-red-300">• {__('Technical Issues')}</p>
                {feedback.technical_issues_description && (
                  <p className="text-gray-400 ml-4 text-sm">"{feedback.technical_issues_description}"</p>
                )}
              </div>
            )}
            {feedback.inappropriate_behavior && (
              <div>
                <p className="text-red-300">• {__('Inappropriate Behavior')}</p>
                {feedback.inappropriate_behavior_description && (
                  <p className="text-gray-400 ml-4 text-sm">"{feedback.inappropriate_behavior_description}"</p>
                )}
              </div>
            )}
          </div>
        )}

        {/* Attendance */}
        <div className="bg-gray-800 p-4 rounded-lg">
          <h4 className="text-lg font-medium text-gray-primary mb-2">
            {__('Attendance Confirmation')}
          </h4>
          <div className="grid grid-cols-2 gap-4 text-sm">
            <div>
              <span className="text-gray-400">{isStreamer ? __('User showed up') : __('Streamer showed up')}:</span>
              <span className={`ml-2 ${(isStreamer ? feedback.user_showed_up : feedback.streamer_showed_up) ? 'text-green-400' : 'text-red-400'}`}>
                {(isStreamer ? feedback.user_showed_up : feedback.streamer_showed_up) ? __('Yes') : __('No')}
              </span>
            </div>
          </div>
        </div>
      </div>

      {/* Edit Button */}
      <div className="pt-4 border-t border-gray-600 mt-6">
        <button
          onClick={onEditFeedback}
          className="w-full px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg font-medium transition-all duration-200 flex items-center justify-center"
        >
          <MdEdit className="mr-2" />
          {__('Edit Feedback')}
        </button>
      </div>
    </div>
  );
}

export default function StreamingSession({
  auth,
  streamRequest,
  isStreamer,
  isOwner,
  isAdmin,
  streamTiming,
  hls_url,
}) {
  const [isMobileChatOpen, setIsMobileChatOpen] = useState(false);
  
  // Validate streamTiming prop first
  const safeStreamTiming = streamTiming || {
    canStartSoon: false,
    canStartNow: false,
    canStreamerStart: false,
    canUserJoin: false,
    isInPreparationPeriod: false,
    timeUntilUserCanJoin: 0,
    isExpired: false,
    timeUntilStart: 0,
    streamDateTime: null,
    streamEndTime: null,
  };


  const [currentStreamRequest, setCurrentStreamRequest] = useState(streamRequest);
  const [realtimeTiming, setRealtimeTiming] = useState(safeStreamTiming);
  const [streamTimeRemaining, setStreamTimeRemaining] = useState(null);
  const [submittedFeedback, setSubmittedFeedback] = useState(null);
  const [showFeedbackForm, setShowFeedbackForm] = useState(true);
  const [isLoadingFeedback, setIsLoadingFeedback] = useState(false);
  const [userHasJoinedStream, setUserHasJoinedStream] = useState(streamRequest.user_joined || false);
  
  // Player states
  const [playerInstance, setPlayerInstance] = useState(null);

  // Check for existing feedback on component mount
  useEffect(() => {
    if (currentStreamRequest.status === 'awaiting_feedback') {
      checkExistingFeedback();
    }
  }, [currentStreamRequest.status]);



  // Monitor critical stream state changes only
  useEffect(() => {
    console.log("🔄 Critical stream state change - Actual start time:", currentStreamRequest.actual_start_time);
  }, [currentStreamRequest.actual_start_time]);

  const checkExistingFeedback = async () => {
    try {
      setIsLoadingFeedback(true);
      const response = await axios.get(
        route('private-stream.feedback.check', currentStreamRequest.id)
      );
      
      if (response.data.status && response.data.feedback) {
        setSubmittedFeedback(response.data.feedback);
        setShowFeedbackForm(false);
      }
    } catch (error) {
      console.error('Error checking existing feedback:', error);
      // If there's an error, just show the form
    } finally {
      setIsLoadingFeedback(false);
    }
  };

  // Handle user joining the stream
  const handleUserJoinStream = async () => {
    try {
      const response = await axios.post(
        route('private-stream.mark-user-joined', currentStreamRequest.id)
      );
      
      if (response.data.status) {
        setUserHasJoinedStream(true);
        setCurrentStreamRequest(response.data.stream_request);
        toast.success(__('Joined stream successfully'));
      } else {
        toast.error(response.data.message || __('Failed to join stream'));
      }
    } catch (error) {
      console.error('Error joining stream:', error);
      toast.error(__('Error joining stream'));
    }
  };

  // Update countdown timer and timing conditions more frequently
  useEffect(() => {
    if (!['accepted', 'in_progress'].includes(currentStreamRequest.status)) {
      return;
    }

    const updateTimingConditions = () => {
      try {
        const now = new Date();
        let scheduledTime;
        
        // Parse scheduled time from different possible formats
        if (streamTiming && streamTiming.streamDateTime) {
          scheduledTime = new Date(streamTiming.streamDateTime);
        } else {
          // Fallback parsing
          const { requested_date, requested_time } = currentStreamRequest;
          let dateStr = requested_date;
          if (typeof requested_date === 'object' && requested_date.date) {
            dateStr = requested_date.date.split(' ')[0];
          } else if (requested_date.includes("T")) {
            dateStr = requested_date.split("T")[0];
          }

          let timeStr = requested_time;
          if (timeStr && timeStr.split(":").length === 2) {
            timeStr += ":00";
          }

          scheduledTime = new Date(`${dateStr} ${timeStr}`);
        }

        if (isNaN(scheduledTime.getTime())) {
          return; // Invalid date, skip update
        }

        const timeUntilStart = Math.max(0, Math.floor((scheduledTime.getTime() - now.getTime()) / 60000)); // Minutes
        const timeUntilStartSeconds = Math.max(0, Math.floor((scheduledTime.getTime() - now.getTime()) / 1000)); // Seconds
        
        // Calculate timing conditions - MANUAL CONTROL ONLY
        const canStreamerStart = true; // Streamers can start anytime for preparation
        const canStartActualStream = timeUntilStartSeconds <= 0; // At scheduled time, but requires manual action
        const canUserJoin = timeUntilStartSeconds <= 0 && currentStreamRequest.actual_start_time; // Only after streamer manually starts
        const isInPreparationPeriod = timeUntilStartSeconds > 0; // Anytime before scheduled time
        
        // Calculate end time conditions
        const scheduledEndTime = new Date(scheduledTime.getTime() + (currentStreamRequest.duration_minutes * 60 * 1000));
        const isExpired = now >= scheduledEndTime;

        setRealtimeTiming(prev => ({
          ...prev,
          timeUntilStart,
          timeUntilStartSeconds,
          canStreamerStart,
          canStartActualStream, 
          canUserJoin,
          isInPreparationPeriod,
          isExpired,
          scheduledTime: scheduledTime.toISOString(),
          scheduledEndTime: scheduledEndTime.toISOString()
        }));
      } catch (error) {
        console.error('Error updating timing conditions:', error);
      }
    };

    // Update immediately
    updateTimingConditions();

    // Update every 1 second for smooth countdown display
    const interval = setInterval(updateTimingConditions, 1000);
    return () => clearInterval(interval);
  }, [currentStreamRequest.status, currentStreamRequest.requested_date, currentStreamRequest.requested_time, currentStreamRequest.duration_minutes, streamTiming, currentStreamRequest.countdown_started_at, currentStreamRequest.actual_start_time]);

  // Stream countdown timer - only starts when streamer manually starts the stream
  useEffect(() => {
    // Only start countdown if stream has been manually started by streamer
    if (!currentStreamRequest.actual_start_time || currentStreamRequest.stream_ended_at) {
      return;
    }

    const calculateTimeRemaining = () => {
      const startTime = new Date(currentStreamRequest.actual_start_time);
      const durationMs = currentStreamRequest.duration_minutes * 60 * 1000;
      const endTime = new Date(startTime.getTime() + durationMs);
      const now = new Date();
      
      return Math.max(0, Math.floor((endTime.getTime() - now.getTime()) / 1000));
    };

    // Initial calculation
    let timeRemaining = calculateTimeRemaining();
    setStreamTimeRemaining(timeRemaining);

    // Auto-end if already expired and stream was started
    if (timeRemaining <= 0) {
      handleAutoEndStream();
      return;
    }

    // Update every second
    const interval = setInterval(() => {
      timeRemaining = calculateTimeRemaining();
      setStreamTimeRemaining(timeRemaining);

      // Auto-end when time reaches zero
      if (timeRemaining <= 0) {
        clearInterval(interval);
        handleAutoEndStream();
      }
    }, 1000);

    return () => clearInterval(interval);
  }, [currentStreamRequest.actual_start_time, currentStreamRequest.stream_ended_at]);

  // Auto-end stream function
  const handleAutoEndStream = async () => {
    try {
      const response = await axios.post(
        route('private-stream.end-stream', currentStreamRequest.id)
      );
      
      if (response.data.status) {
        // Update local state immediately
        setCurrentStreamRequest(response.data.stream_request);
        
        // Show notification
        toast.success(__('Stream ended automatically after duration expired'), {
          position: "top-right",
          autoClose: 3000,
        });

        // Redirect to feedback after a short delay
        setTimeout(() => {
          window.location.reload();
        }, 2000);
      }
    } catch (error) {
      console.error('Error auto-ending stream:', error);
    }
  };

  // Format time duration for user-friendly display
  const formatTimeDuration = (minutes) => {
    if (minutes <= 0) return __("now");

    const days = Math.floor(minutes / (24 * 60));
    const hours = Math.floor((minutes % (24 * 60)) / 60);
    const remainingMinutes = minutes % 60;

    const parts = [];

    if (days > 0) {
      parts.push(`${days} ${days === 1 ? __("day") : __("days")}`);
    }

    if (hours > 0) {
      parts.push(`${hours} ${hours === 1 ? __("hour") : __("hours")}`);
    }

    if (remainingMinutes > 0 || parts.length === 0) {
      parts.push(
        `${remainingMinutes} ${
          remainingMinutes === 1 ? __("minute") : __("minutes")
        }`
      );
    }

    // Join with "and" for the last part, commas for the rest
    if (parts.length === 1) {
      return parts[0];
    } else if (parts.length === 2) {
      return parts.join(` ${__("and")} `);
    } else {
      const lastPart = parts.pop();
      return parts.join(", ") + `, ${__("and")} ` + lastPart;
    }
  };

  // Format seconds remaining for stream countdown
  const formatTimeRemaining = (seconds) => {
    if (seconds <= 0) return "00:00";

    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    const secs = seconds % 60;

    if (hours > 0) {
      return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    } else {
      return `${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    }
  };

  // Format countdown to stream start
  const formatCountdownToStart = (seconds) => {
    if (seconds <= 0) return null;

    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    const secs = seconds % 60;

    if (hours > 0) {
      return `${hours}h ${minutes}m ${secs}s`;
    } else if (minutes > 0) {
      return `${minutes}m ${secs}s`;
    } else {
      return `${secs}s`;
    }
  };

  // Dynamic countdown display component
  const CountdownDisplay = () => {
    const timeLeft = currentTiming.timeUntilStartSeconds;
    
    if (!timeLeft || timeLeft <= 0) return null;

    const countdownText = formatCountdownToStart(timeLeft);
    if (!countdownText) return null;

    return (
      <div className="bg-footer text-white p-4 shadow-lg mb-4">
        <div className="flex items-center justify-center space-x-3">
          <MdAccessTime className="text-2xl text-primary animate-pulse" />
          <div className="text-center">
            <div className="text-lg font-bold text-gray-primary">
              {isStreamer ? __("Stream starts in") : __("Stream starts in")}
            </div>
            <div className="text-3xl font-mono font-bold tracking-wider text-white">
              {countdownText}
            </div>
            <div className="text-sm text-gray-400">
              {isStreamer 
                ? __("You can start preparing anytime") 
                : __("Streamer will start manually at scheduled time")
              }
            </div>
          </div>
        </div>
      </div>
    );
  };

  // Use real-time timing data if available
  const currentTiming = realtimeTiming || safeStreamTiming;

  // Memoize video player options to prevent unnecessary re-renders
  const streamerVideoOptions = useMemo(() => ({
    autoplay: true,
    controls: true,
    responsive: true,
    fill: true,
    preload: "auto",
    fluid: true,
    liveui: true,
    playsinline: true,
    html5: {
      vhs: {
        overrideNative: true,
        enableLowInitialPlaylist: true,
        smoothQualityChange: true,
        fastQualityChange: true,
      },
      nativeAudioTracks: false,
      nativeVideoTracks: false,
    },
    sources: [
      {
        src: `https://live.dg4e.com/hls/${currentStreamRequest.stream_key}.m3u8`,
        type: "application/x-mpegURL",
      },
    ],
    plugins: {
      qualityLevels: {}
    }
  }), [currentStreamRequest.stream_key]);

  const userVideoOptions = useMemo(() => ({
    autoplay: false,
    controls: true,
    responsive: true,
    fill: true,
    preload: "auto",
    fluid: true,
    liveui: true,
    playsinline: true,
    html5: {
      vhs: {
        overrideNative: true,
        enableLowInitialPlaylist: true,
        smoothQualityChange: true,
        fastQualityChange: true,
      },
      nativeAudioTracks: false,
      nativeVideoTracks: false,
    },
    sources: [
      {
        src: `https://live.dg4e.com/hls/${currentStreamRequest.stream_key}.m3u8`,
        type: "application/x-mpegURL",
      },
    ],
    plugins: {
      qualityLevels: {}
    }
  }), [currentStreamRequest.stream_key]);

  // Check if stream time has ended
  const isStreamTimeExpired = () => {
    if (!safeStreamTiming.streamEndTime) return false;
    return new Date() >= new Date(safeStreamTiming.streamEndTime);
  };

  // Get stream status info
  const getStreamStatusInfo = () => {
    switch (currentStreamRequest.status) {
      case "pending":
        return {
          title: __("Stream Request Pending"),
          message: isStreamer
            ? __("This stream request is waiting for your approval")
            : __("Your stream request is pending approval from the streamer"),
          color: "bg-yellow-600",
          canChat: true,
          canStream: false,
        };
      case "accepted":
        if (isStreamTimeExpired()) {
          return {
            title: __("Stream Time Expired"),
            message: __("The scheduled time for this stream has already ended"),
            color: "bg-red-600",
            canChat: true,
            canStream: false,
          };
        } else if (safeStreamTiming.isExpired) {
          return {
            title: __("Stream Session Expired"),
            message: __("This stream session has ended"),
            color: "bg-gray-600",
            canChat: true,
            canStream: false,
          };
        } else if (currentTiming.canUserJoin || currentTiming.canStartActualStream) {
          return {
            title: __("Stream Session Active"),
            message: __("Stream is ready - users can join"),
            color: "bg-green-600",
            canChat: true,
            canStream: true,
          };
        } else if (currentTiming.isInPreparationPeriod) {
          const minutesUntilStart = Math.floor((currentTiming.timeUntilStartSeconds || currentTiming.timeUntilStart * 60) / 60);
          const secondsUntilStart = ((currentTiming.timeUntilStartSeconds || currentTiming.timeUntilStart * 60) % 60);
          const timeDisplay = minutesUntilStart > 0 
            ? `${minutesUntilStart}m ${secondsUntilStart}s`
            : `${secondsUntilStart}s`;
            
          return {
            title: isStreamer ? __("Ready to Stream") : __("Stream Starting Soon"),
            message: isStreamer 
              ? __("You can start streaming anytime for preparation. Stream can go live when scheduled time arrives.")
              : __("Please wait - streamer will manually start the stream at scheduled time"),
            color: isStreamer ? "bg-green-600" : "bg-blue-600",
            canChat: true,
            canStream: isStreamer,
          };
        } else if (currentTiming.canStreamerStart) {
          const minutesUntilStart = Math.floor((currentTiming.timeUntilStartSeconds || currentTiming.timeUntilStart * 60) / 60);
          const secondsUntilStart = ((currentTiming.timeUntilStartSeconds || currentTiming.timeUntilStart * 60) % 60);
          const timeDisplay = minutesUntilStart > 0 
            ? `${minutesUntilStart}m ${secondsUntilStart}s`
            : `${secondsUntilStart}s`;
            
          return {
            title: __("Stream Ready"),
            message: isStreamer 
              ? __("You can start streaming anytime for preparation. Stream can go live when scheduled time arrives.")
              : __("Streamer is preparing - stream will start manually at scheduled time"),
            color: "bg-blue-600",
            canChat: true,
            canStream: isStreamer,
          };
        } else {
          return {
            title: __("Stream Scheduled"),
            message: `${__("Stream starts in")} ${formatTimeDuration(
              currentTiming.timeUntilStart
            )}`,
            color: "bg-blue-600",
            canChat: true,
            canStream: isStreamer,
          };
        }
      case "rejected":
        return {
          title: __("Stream Request Rejected"),
          message: __("This stream request was rejected"),
          color: "bg-red-600",
          canChat: false,
          canStream: false,
        };
      case "awaiting_feedback":
        return {
          title: __("Stream Completed - Feedback Required"),
          message: __("Please provide feedback about your streaming experience"),
          color: "bg-blue-600",
          canChat: true,
          canStream: false,
        };
      case "completed":
        return {
          title: __("Stream Completed"),
          message: __("This stream session has been completed"),
          color: "bg-green-700",
          canChat: true,
          canStream: false,
        };
      case "no_show":
        return {
          title: __("No Show"),
          message: __("User was marked as no-show for this stream"),
          color: "bg-gray-600",
          canChat: false,
          canStream: false,
        };
      case "completed_with_issues":
        return {
          title: __("Stream Completed with Issues"),
          message: __("This stream ended with participation issues"),
          color: "bg-orange-600",
          canChat: true,
          canStream: false,
        };
      case "streamer_no_show":
        return {
          title: __("Streamer No Show"),
          message: __("The streamer did not start the stream. Full refund has been processed."),
          color: "bg-red-600",
          canChat: true,
          canStream: false,
        };
      case "user_no_show":
        return {
          title: __("User No Show"),
          message: __("The user did not join the stream. Partial refund has been processed."),
          color: "bg-orange-600",
          canChat: true,
          canStream: false,
        };
      case "disputed":
        return {
          title: __("Stream Under Review"),
          message: __("This stream is under admin review due to a dispute"),
          color: "bg-purple-600",
          canChat: true,
          canStream: false,
        };
      case "resolved":
        return {
          title: __("Stream Dispute Resolved"),
          message: __("This stream dispute has been resolved by an admin"),
          color: "bg-green-600",
          canChat: true,
          canStream: false,
        };
      default:
        return {
          title: __("Stream Session"),
          message: __("Stream session details"),
          color: "bg-gray-600",
          canChat: true,
          canStream: false,
        };
    }
  };

  const statusInfo = getStreamStatusInfo();



  // Format date and time for display
  const formatDateTime = (date, time) => {
    try {
      // If we have streamTiming data, use the parsed datetime from backend
      if (streamTiming && streamTiming.streamDateTime) {
        const dateObj = new Date(streamTiming.streamDateTime);
        if (!isNaN(dateObj.getTime())) {
          return dateObj.toLocaleString();
        }
      }

      // Fallback: try to parse the date and time manually
      if (date && time) {
        // Handle Laravel date format (Y-m-d)
        let dateStr = date;
        if (typeof date === 'object' && date.date) {
          dateStr = date.date.split(' ')[0]; // Extract date part if it's a Laravel date object
        } else if (date.includes("T")) {
          dateStr = date.split("T")[0];
        }

        // Ensure time has seconds
        let timeStr = time;
        if (timeStr && timeStr.split(":").length === 2) {
          timeStr += ":00";
        }

        const dateObj = new Date(`${dateStr} ${timeStr}`);
        if (!isNaN(dateObj.getTime())) {
          return dateObj.toLocaleString();
        }
      }

      // If all else fails, return a fallback
      return __("Date not available");
    } catch (error) {
      console.error("Error formatting date:", error, { date, time });
      return __("Invalid date");
    }
  };

  // Handle stream state changes (from controls)
  const handleStreamStateChange = (updatedStreamRequest) => {
    const wasInPrep = currentStreamRequest.countdown_started_at && !currentStreamRequest.actual_start_time;
    const isNowLive = updatedStreamRequest.actual_start_time && !currentStreamRequest.actual_start_time;
    
    setCurrentStreamRequest(updatedStreamRequest);
    
    // If stream just went from preparation to live, show notification
    if (wasInPrep && isNowLive) {
      toast.success(__('Stream is now live! Video player refreshed.'), {
        position: "top-right",
        autoClose: 3000,
      });
    }
    
    // Force page refresh if stream enters feedback phase
    if (updatedStreamRequest.status === 'awaiting_feedback') {
      window.location.reload();
    }
  };

  // Handle real-time stream state changes
  const handleRealtimeStreamStateChange = (updatedStreamRequest, timing, eventType) => {
    setCurrentStreamRequest(updatedStreamRequest);
    setRealtimeTiming(timing);
    
    // Handle specific event types
    if (eventType === 'stream_ended' && updatedStreamRequest.status === 'awaiting_feedback') {
      // Small delay before redirect to show the notification
      setTimeout(() => {
        window.location.reload();
      }, 2000);
    }
    
    // Handle user joining stream
    if (eventType === 'user_joined' && updatedStreamRequest.user_joined) {
      setUserHasJoinedStream(true);
    }
  };

  // Handle real-time countdown updates
  const handleRealtimeCountdownUpdate = (countdownData) => {
    setRealtimeTiming(prev => ({
      ...prev,
      ...countdownData,
      // Map new backend fields
      timeUntilStartSeconds: countdownData.time_until_start_seconds || prev.timeUntilStartSeconds,
      canStartActualStream: countdownData.can_start_actual_stream || prev.canStartActualStream,
      timeUntilStart: countdownData.time_until_start || prev.timeUntilStart
    }));
  };

  // Toggle mobile chat
  const toggleMobileChat = () => {
    setIsMobileChatOpen(!isMobileChatOpen);
  };


  return (
    <AuthenticatedLayout
      user={auth.user}
      header={
        <h2 className="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          {__("Private Streaming Session")}
        </h2>
      }
    >
      <div className="bg-black text-white mb-5">
        {/* Real-time listener */}
        <PrivateStreamRealtimeListener
          streamRequest={currentStreamRequest}
          onStreamStateChange={handleRealtimeStreamStateChange}
          onCountdownUpdate={handleRealtimeCountdownUpdate}
        />
        
        <div className="max-w-7xl mx-auto">
          {/* Header Info */}
          <div className="bg-footer p-4 border-b border-gray-700">
            <div className="flex justify-between items-center">
              <div className="flex items-center space-x-2 md:space-x-4 flex-wrap">
                <div className="flex items-center">
                  <MdPerson className="h-4 w-4 md:h-5 md:w-5 text-gray-400 mr-1 md:mr-2" />
                  <span className="text-sm md:text-base text-gray-primary">
                    {isStreamer
                      ? streamRequest.user?.name
                      : streamRequest.streamer?.name}
                  </span>
                </div>
                <div className="flex items-center">
                  <MdAccessTime className="h-4 w-4 md:h-5 md:w-5 text-gray-400 mr-1 md:mr-2" />
                  <span className="text-sm md:text-base text-gray-primary">
                    {formatDateTime(
                      streamRequest.requested_date,
                      streamRequest.requested_time
                    )}
                  </span>
                </div>
                <div className="flex items-center">
                  {/* <MdAttachMoney className="h-4 w-4 md:h-5 md:w-5 text-gray-400 mr-1 md:mr-2" /> */}
                  <span className="text-sm md:text-base text-gray-primary">
                    ${streamRequest.streamer_fee}
                  </span>
                </div>
                {/* Show stream countdown when we're at/past scheduled time */}
                {!currentStreamRequest.stream_ended_at && streamTimeRemaining !== null && (
                  <div className="flex items-center">
                    <MdAccessTime className="h-4 w-4 md:h-5 md:w-5 text-red-400 mr-1 md:mr-2" />
                    <span className="text-sm md:text-base text-red-400 font-bold">
                      {__("Time Remaining")}: {formatTimeRemaining(streamTimeRemaining)}
                    </span>
                  </div>
                )}
              </div>
              <div className="flex items-center space-x-2 md:space-x-4">
                {/* Calendar Integration */}
                {["accepted", "pending"].includes(currentStreamRequest.status) && (
                  <CalendarIntegration 
                    streamRequest={currentStreamRequest}
                    isVisible={true}
                  />
                )}



                {/* Mobile Chat Toggle Button */}
                {statusInfo.canChat && (
                  <button
                    onClick={toggleMobileChat}
                    className={`lg:hidden p-2 rounded-lg ${
                      currentStreamRequest.status !== "pending" && 
                      !["completed", "awaiting_feedback", "completed_with_issues", "streamer_no_show", "user_no_show", "disputed", "resolved"].includes(currentStreamRequest.status)
                        ? "bg-primary hover:bg-primary/80" 
                        : "bg-gray-600 hover:bg-gray-500"
                    }`}
                    title={
                      isMobileChatOpen ? __("Close Chat") : __("Open Chat")
                    }
                  >
                    {isMobileChatOpen ? (
                      <MdClose className="h-5 w-5" />
                    ) : (
                      <MdChat className="h-5 w-5" />
                    )}
                  </button>
                )}

                <div
                  className={`px-2 py-1 md:px-3 md:py-1 rounded-full text-xs md:text-sm ${statusInfo.color}`}
                >
                  {statusInfo.title}
                </div>
              </div>
            </div>
          </div>

          {/* Dynamic Countdown Display */}
          {currentTiming.isInPreparationPeriod && (
            <div className="mt-4">
              <CountdownDisplay />
            </div>
          )}

          <div className={`flex flex-col lg:flex-row ${
            currentStreamRequest.status === "awaiting_feedback" 
              ? "min-h-[600px] md:min-h-[700px] lg:h-[600px]" 
              : "h-[400px] md:h-[500px] lg:h-[600px]"
          }`}>
            {/* Main Video Area */}
            <div
              className={`flex-1 flex flex-col ${
                isMobileChatOpen ? "hidden lg:flex" : "flex"
              }`}
            >
                            {/* Video Container */}
              <div className="flex-1 bg-gray-900 relative">
                {/* Show video player when stream is active OR when streamer is in preparation */}
                {(currentStreamRequest.actual_start_time && !currentStreamRequest.stream_ended_at) || 
                 (isStreamer && currentStreamRequest.countdown_started_at && !currentStreamRequest.stream_ended_at) ? (
                  <div className="w-full h-full relative">
                    {/* For streamers: show video immediately */}
                    {isStreamer ? (
                      <>
                        <VideoJS
                          key={`streamer-${currentStreamRequest.stream_key}`}
                          options={streamerVideoOptions}
                          onReady={(player) => {
                            setPlayerInstance(player);
                            const streamUrl = `https://live.dg4e.com/hls/${currentStreamRequest.stream_key}.m3u8`;
                            const streamState = currentStreamRequest.actual_start_time ? 'LIVE' : 'PREPARATION';
                            
                            console.log("🎥 Streamer video player ready");
                            console.log("📺 Stream URL:", streamUrl);
                            console.log("🔴 Stream state:", streamState);
                            console.log("🔑 Stream key:", currentStreamRequest.stream_key);
                            
                            // Try to load and play
                            player.ready(() => {
                              console.log("📡 Player is ready, attempting to play...");
                              player.play().catch((error) => {
                                console.warn("⚠️ Auto-play failed:", error);
                              });
                            });
                          }}
                        />
                        
                        {/* Stream info overlay */}
                        <div className="absolute top-4 left-4 z-10">
                          <div className="bg-black bg-opacity-75 text-white px-3 py-2 rounded-lg">
                            <div className="flex items-center space-x-2">
                              {currentStreamRequest.actual_start_time ? (
                                <>
                                  <div className="w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>
                                  <span className="text-sm font-medium">{__("LIVE")}</span>
                                  {streamTimeRemaining !== null && (
                                    <>
                                      <span className="text-sm">•</span>
                                      <span className="text-sm">{formatTimeRemaining(streamTimeRemaining)}</span>
                                    </>
                                  )}
                                </>
                              ) : (
                                <>
                                  <div className="w-2 h-2 bg-yellow-500 rounded-full animate-pulse"></div>
                                  <span className="text-sm font-medium">{__("PREPARATION")}</span>
                                  {currentTiming.canStartActualStream ? (
                                    <>
                                      <span className="text-sm">•</span>
                                      <span className="text-sm text-green-400">
                                        {__("Ready to go live")}
                                      </span>
                                    </>
                                  ) : (
                                    <>
                                      <span className="text-sm">•</span>
                                      <span className="text-sm">
                                        {currentTiming.timeUntilStartSeconds > 60 
                                          ? `${__("Can go live in")} ${Math.floor(currentTiming.timeUntilStartSeconds / 60)}m ${currentTiming.timeUntilStartSeconds % 60}s`
                                          : currentTiming.timeUntilStartSeconds > 0
                                          ? `${__("Can go live in")} ${currentTiming.timeUntilStartSeconds}s`
                                          : __("Ready to go live")
                                        }
                                      </span>
                                    </>
                                  )}
                                </>
                              )}
                            </div>
                          </div>
                        </div>

                        {/* Streamer Controls Overlay - Always visible for streamers */}
                        <div className="absolute bottom-2 left-2 right-2 md:bottom-4 md:left-4 md:right-4 z-10">
                          <div className="bg-footer bg-opacity-95 backdrop-blur-sm border border-gray-600 text-white p-2 md:p-4 rounded-lg">
                            <PrivateStreamControls
                              streamRequest={currentStreamRequest}
                              isStreamer={isStreamer}
                              streamTiming={currentTiming}
                              onStreamStateChange={handleStreamStateChange}
                            />
                          </div>
                        </div>
                      </>
                    ) : (
                      /* For users: show join button or video based on their choice and timing */
                      currentStreamRequest.actual_start_time && (userHasJoinedStream || currentStreamRequest.user_joined) ? (
                        <>
                          <VideoJS
                            key={`user-${currentStreamRequest.stream_key}`}
                            options={userVideoOptions}
                            onReady={(player) => {
                              if (!isStreamer) setPlayerInstance(player);
                              const streamUrl = `https://live.dg4e.com/hls/${currentStreamRequest.stream_key}.m3u8`;
                              
                              console.log("👥 User video player ready");
                              console.log("📺 User stream URL:", streamUrl);
                              console.log("🔑 Stream key:", currentStreamRequest.stream_key);
                              console.log("✅ User joined:", userHasJoinedStream || currentStreamRequest.user_joined);
                              
                              // Don't auto-play for users - let them control it
                              player.ready(() => {
                                console.log("📡 User player is ready");
                              });
                            }}
                          />
                          
                          {/* Stream info overlay */}
                          <div className="absolute top-4 left-4 z-10">
                            <div className="bg-black bg-opacity-75 text-white px-3 py-2 rounded-lg">
                              <div className="flex items-center space-x-2">
                                <div className="w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>
                                <span className="text-sm font-medium">{__("LIVE")}</span>
                                {streamTimeRemaining !== null && (
                                  <>
                                    <span className="text-sm">•</span>
                                    <span className="text-sm">{formatTimeRemaining(streamTimeRemaining)}</span>
                                  </>
                                )}
                              </div>
                            </div>
                          </div>
                        </>
                      ) : currentStreamRequest.actual_start_time ? (
                        /* Show join stream button for users when stream is actually live */
                        <div className="absolute inset-0 flex items-center justify-center bg-gray-900">
                          <div className="text-center">
                            <div className="w-20 h-20 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
                              <MdVideoCall className="text-4xl text-white" />
                            </div>
                            <h3 className="text-2xl font-bold text-gray-primary mb-4">
                              {__("Stream is Live!")}
                            </h3>
                            <p className="text-lg text-gray-400 mb-6">
                              {__("The streamer has started. Click to join the stream.")}
                            </p>
                            {streamTimeRemaining !== null && (
                              <p className="text-red-400 mb-6">
                                {__("Time Remaining")}: {formatTimeRemaining(streamTimeRemaining)}
                              </p>
                            )}
                            <button
                              onClick={handleUserJoinStream}
                              className="px-8 py-4 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold text-lg transition-all duration-200 flex items-center mx-auto"
                            >
                              <MdVideoCall className="mr-3 text-xl" />
                              {__("Join Stream")}
                            </button>
                          </div>
                        </div>
                      ) : currentStreamRequest.countdown_started_at ? (
                        /* Show preparation message for users when streamer is preparing */
                        <div className="absolute inset-0 flex items-center justify-center bg-gray-900">
                          <div className="text-center">
                            <div className="w-20 h-20 bg-yellow-600 rounded-full flex items-center justify-center mx-auto mb-6">
                              <MdAccessTime className="text-4xl text-white" />
                            </div>
                            <h3 className="text-2xl font-bold text-gray-primary mb-4">
                              {__("Streamer is Preparing")}
                            </h3>
                            <p className="text-lg text-gray-400 mb-6">
                              {__("The streamer is setting up. You can join when the streamer manually starts the stream.")}
                            </p>
                            {currentTiming.timeUntilStartSeconds > 0 ? (
                              <div className="mb-6">
                                <p className="text-gray-400 mb-2">
                                  {__("Streamer can start stream in")}:
                                </p>
                                <div className="text-2xl font-mono font-bold text-white bg-footer border border-gray-600 px-4 py-2 rounded-lg inline-block">
                                  {formatCountdownToStart(currentTiming.timeUntilStartSeconds)}
                                </div>
                              </div>
                            ) : (
                              <p className="text-green-400 mb-6">
                                {__("Streamer can now start the stream manually")}
                              </p>
                            )}
                            <div className="px-8 py-4 bg-gray-600 text-white rounded-lg font-semibold text-lg">
                              {__("Please Wait")}
                            </div>
                          </div>
                        </div>
                      ) : (
                        /* Default waiting state */
                        <div className="absolute inset-0 flex items-center justify-center bg-gray-900">
                          <div className="text-center">
                            <div className="w-20 h-20 bg-gray-600 rounded-full flex items-center justify-center mx-auto mb-6">
                              <MdVideoCall className="text-4xl text-white" />
                            </div>
                            <h3 className="text-2xl font-bold text-gray-primary mb-4">
                              {__("Waiting for Streamer")}
                            </h3>
                            <p className="text-lg text-gray-400 mb-6">
                              {__("The streamer hasn't started preparation yet.")}
                            </p>
                            {currentTiming.timeUntilStartSeconds > 0 && (
                              <div className="mb-6">
                                <p className="text-gray-400 mb-2">
                                  {__("Stream starts in")}:
                                </p>
                                <div className="text-2xl font-mono font-bold text-white bg-footer border border-gray-600 px-4 py-2 rounded-lg inline-block">
                                  {formatCountdownToStart(currentTiming.timeUntilStartSeconds)}
                                </div>
                              </div>
                            )}
                          </div>
                        </div>
                      )
                    )}
                  </div>
                ) : currentStreamRequest.status === "awaiting_feedback" ? (
                  /* Feedback form takes full space for better visibility */
                  <div className="absolute inset-0 overflow-y-auto">
                    <div className="min-h-full flex items-start justify-center p-4">
                      <div className="w-full max-w-2xl">
                        <div className="text-center mb-6">
                          <MdVideoCall className="h-16 w-16 mx-auto mb-4 text-blue-500" />
                          <h2 className="text-2xl font-bold text-gray-primary mb-2">
                            {statusInfo.title}
                          </h2>
                          <p className="text-lg text-gray-400 mb-6">
                            {statusInfo.message}
                          </p>
                        </div>
                        
                        {isLoadingFeedback ? (
                          <div className="bg-footer border border-gray-600 rounded-xl shadow-xl p-6">
                            <div className="text-center">
                              <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500 mx-auto mb-4"></div>
                              <p className="text-gray-400">{__('Loading feedback...')}</p>
                            </div>
                          </div>
                        ) : showFeedbackForm ? (
                          <PrivateStreamFeedback
                            streamRequest={currentStreamRequest}
                            isStreamer={isStreamer}
                            onFeedbackSubmitted={(feedbackData) => {
                              setSubmittedFeedback(feedbackData.feedback);
                              setShowFeedbackForm(false);
                            }}
                          />
                        ) : (
                          <SubmittedFeedbackDisplay 
                            feedback={submittedFeedback}
                            isStreamer={isStreamer}
                            onEditFeedback={() => setShowFeedbackForm(true)}
                          />
                        )}
                      </div>
                    </div>
                  </div>
                ) : (
                  /* Status-based content for non-active streams */
                  <div className="absolute inset-0 flex items-center justify-center">
                    <div className="text-center max-w-md mx-auto p-4 md:p-8">
                      <MdVideoCall
                        className={`h-16 w-16 md:h-24 md:w-24 mx-auto mb-4 ${
                          statusInfo.canStream
                            ? "text-primary"
                            : "text-gray-600"
                        }`}
                      />
                      <h2 className="text-xl md:text-2xl font-bold text-gray-primary mb-2">
                        {statusInfo.title}
                      </h2>
                      <p className="text-base md:text-lg text-gray-400 mb-4">
                        {statusInfo.message}
                      </p>

                      {/* Stream controls for both streamers and users */}
                      {(currentStreamRequest.status === "accepted" || currentStreamRequest.status === "in_progress") && (
                        <div className="w-full">
                          <PrivateStreamControls
                            streamRequest={currentStreamRequest}
                            isStreamer={isStreamer}
                            streamTiming={currentTiming}
                            onStreamStateChange={handleStreamStateChange}
                          />
                        </div>
                      )}

                      {/* Additional status-specific info */}
                      {currentStreamRequest.status === "pending" && isStreamer && (
                        <div className="space-y-2">
                          <button
                            onClick={() =>
                              (window.location.href = route(
                                "private-stream.dashboard"
                              ))
                            }
                            className="px-4 py-2 md:px-6 md:py-2 bg-green-600 hover:bg-green-700 rounded-lg text-white font-medium text-sm md:text-base"
                          >
                            {__("Go to Dashboard to Accept")}
                          </button>
                        </div>
                      )}

                      {currentStreamRequest.status === "accepted" &&
                        !statusInfo.canStream && (
                          <div className="text-gray-400">
                            {isStreamTimeExpired() ? (
                              <p className="text-sm md:text-base text-red-400">
                                {__("This stream's scheduled time has ended and can no longer be accessed")}
                              </p>
                            ) : (
                              <p className="text-sm md:text-base">
                                {isStreamer 
                                  && __("You can start streaming anytime for setup and preparation")
                                }
                              </p>
                            )}
                           
                          </div>
                        )}
                    </div>
                  </div>
                )}
              </div>


            </div>

            {/* Chat Sidebar - Show on desktop always, on mobile when toggled */}
            <div
              className={`
              w-full lg:w-80 
              ${isMobileChatOpen ? "flex" : "hidden lg:flex"}
            `}
            >
              <PrivateStreamChat
                streamRequest={currentStreamRequest}
                canChat={statusInfo.canChat}
                isStreamer={isStreamer}
                canSendMessage={
                  currentStreamRequest.status !== "pending" && 
                  !["completed", "awaiting_feedback", "completed_with_issues", "streamer_no_show", "user_no_show", "disputed", "resolved"].includes(currentStreamRequest.status)
                }
              />
            </div>
          </div>
        </div>

        {/* Streaming Instructions for streamers - only show when stream is active or can be started */}
        {isStreamer && 
         !["completed", "awaiting_feedback", "completed_with_issues", "streamer_no_show", "user_no_show", "disputed", "resolved", "rejected", "cancelled"].includes(currentStreamRequest.status) && (
          <div className="w-fullmx-auto mt-6 px-0">
            <PrivateStreamInstructions
              streamKey={
                currentStreamRequest.stream_key ||
                `fallback-${currentStreamRequest.id}-${Date.now()}`
              }
              streamUser={auth.user.username}
              streamRequest={currentStreamRequest}
            />
          </div>
        )}
      </div>
    </AuthenticatedLayout>
  );
}
