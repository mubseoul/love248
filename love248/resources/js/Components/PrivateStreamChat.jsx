import { useState, useEffect, useRef } from "react";
import { usePage } from "@inertiajs/inertia-react";
import __ from "@/Functions/Translate";
import axios from "axios";
import { toast } from "react-toastify";
import { MdMessage, MdSend } from "react-icons/md";
import PrivateStreamTip from "./PrivateStreamTip";

export default function PrivateStreamChat({
  streamRequest,
  canChat = false,
  isStreamer = false,
  canSendMessage = true,
}) {
  const [chatMessages, setChatMessages] = useState([]);
  const [newMessage, setNewMessage] = useState("");
  const [isSendingMessage, setIsSendingMessage] = useState(false);

  // Chat room name for this private stream
  const chatRoomName = `stream-session-${
    streamRequest.stream_key || streamRequest.id
  }`;

  // Reference for chat scroll container
  const chatScrollRef = useRef(null);

  const { auth, coins_sound } = usePage().props;

  // Create tip sound effect
  const tipSound = coins_sound ? new Audio(coins_sound) : null;

  // Load existing chat messages and set up real-time listening
  useEffect(() => {
    if (!canChat) return;

    // Load existing messages first
    loadChatMessages();

    // Set up real-time chat listening
    const echoChannel = window.Echo.channel(chatRoomName);

    echoChannel.listen(".private-stream-chat", (data) => {
      // Use functional update to ensure we get the latest state
      setChatMessages((prevMessages) => [...prevMessages, data.chat]);

      // Play tip sound if the message contains a tip
      if (data.chat.tip > 0 && tipSound) {
        tipSound.play();
      }
    });

    // Cleanup function
    return () => {
      window.Echo.leaveChannel(chatRoomName);
    };
  }, [chatRoomName, canChat]);

  // Auto-scroll chat to bottom
  const scrollChatToBottom = () => {
    if (chatScrollRef.current) {
      const { scrollHeight, clientHeight } = chatScrollRef.current;
      chatScrollRef.current.scrollTo({
        top: scrollHeight - clientHeight,
        behavior: "smooth",
      });
    }
  };

  // Auto-scroll when new messages arrive
  useEffect(() => {
    scrollChatToBottom();
  }, [chatMessages]);

  // Load chat messages from backend
  const loadChatMessages = async () => {
    try {
      const response = await axios.get(
        route("private-stream.chat.messages", streamRequest.id)
      );
      if (response.data.status) {
        setChatMessages(response.data.messages || []);
      }
    } catch (error) {
      console.error("Error loading chat messages:", error);
    }
  };

  // Send chat message
  const sendMessage = async () => {
    if (!newMessage.trim() || isSendingMessage) return;

    setIsSendingMessage(true);
    try {
      const response = await axios.post(
        route("private-stream.chat.send", streamRequest.id),
        { message: newMessage }
      );

      if (response.data.status) {
        setNewMessage("");
        // Message will be added via real-time event
      } else {
        toast.error(response.data.message || __("Failed to send message"));
      }
    } catch (error) {
      toast.error(error.response?.data?.message || __("Error sending message"));
    } finally {
      setIsSendingMessage(false);
    }
  };

  // Handle enter key press in chat input
  const handleKeyPress = (e) => {
    if (e.key === "Enter" && !e.shiftKey) {
      e.preventDefault();
      sendMessage();
    }
  };

  // Handle tip sent - refresh messages
  const handleTipSent = () => {
    // Messages will be updated via real-time events, but we could refresh if needed
    // loadChatMessages();
  };

  // Don't render chat if not allowed
  if (!canChat) {
    return null;
  }

  return (
    <div className="w-80 bg-footer border-l border-gray-700 flex flex-col">
      {/* Chat Header */}
      <div className="p-4 border-b border-gray-700">
        <div className="flex items-center">
          <MdMessage className="h-5 w-5 text-primary mr-2" />
          <h3 className="text-lg font-medium text-gray-primary">
            {__("Chat")}
          </h3>
        </div>
        {!canSendMessage && (
          <p className="text-xs text-gray-400 mt-1">
            {__("Messaging disabled - stream has ended")}
          </p>
        )}
      </div>

      {/* Chat Messages */}
      <div ref={chatScrollRef} className="flex-1 overflow-y-auto p-4 space-y-3">
        {chatMessages.length === 0 ? (
          <div className="text-center text-gray-400 py-8">
            <p>{__("No messages yet")}</p>
            {canSendMessage && (
              <p className="text-sm">{__("Start the conversation!")}</p>
            )}
          </div>
        ) : (
          chatMessages.map((msg) => (
            <div
              key={msg.id}
              className={`flex flex-col ${
                msg.tip > 0
                  ? "bg-yellow-200 rounded-lg p-3 text-gray-900 my-2"
                  : ""
              }`}
            >
              <div className="flex items-center space-x-2 mb-1">
                <span
                  className={`text-sm font-medium ${
                    msg.user_id === streamRequest.streamer_id
                      ? "text-primary"
                      : msg.tip > 0
                      ? "text-yellow-800"
                      : "text-gray-primary"
                  }`}
                >
                  {msg.user?.name || msg.user?.username || __("User")}
                </span>
                <span
                  className={`text-xs ${
                    msg.tip > 0 ? "text-yellow-700" : "text-gray-400"
                  }`}
                >
                  {new Date(msg.created_at).toLocaleTimeString()}
                </span>
              </div>
              <div
                className={`text-sm ml-2 ${
                  msg.tip > 0 ? "text-gray-900" : "text-gray-300"
                }`}
              >
                {msg.tip > 0 && (
                  <span className="font-bold text-yellow-800">
                    {__("Just tipped :tip tokens! ", { tip: msg.tip })}
                  </span>
                )}
                {msg.message}
              </div>
            </div>
          ))
        )}
      </div>

      {/* Chat Input - Only show when canSendMessage is true */}
      {canSendMessage ? (
        <div className="p-4 border-t border-gray-700">
          <div className="flex items-center space-x-2">
            {/* Tip Button - Only show for non-streamers, positioned on the left */}
            {!isStreamer && (
              <PrivateStreamTip
                streamRequest={streamRequest}
                onTipSent={handleTipSent}
              />
            )}

            <input
              type="text"
              value={newMessage}
              onChange={(e) => setNewMessage(e.target.value)}
              onKeyPress={handleKeyPress}
              placeholder={__("Type a message...")}
              className="flex-1 bg-black border border-gray-600 rounded-lg px-3 py-2 text-gray-primary placeholder-gray-400 focus:outline-none focus:border-primary"
              disabled={isSendingMessage}
            />
            <button
              onClick={sendMessage}
              className="p-2 bg-primary hover:bg-primary/80 rounded-lg disabled:opacity-50"
              disabled={!newMessage.trim() || isSendingMessage}
            >
              <MdSend className="h-4 w-4" />
            </button>
          </div>
        </div>
      ) : (
        <div className="p-4 border-t border-gray-700">
          <div className="text-center text-gray-400">
            <p className="text-sm">{__("Chat is now read-only")}</p>
            <p className="text-xs mt-1">{__("Stream session has ended")}</p>
          </div>
        </div>
      )}
    </div>
  );
}
