import PrimaryButton from "@/Components/PrimaryButton";
import TextInput from "@/Components/TextInput";
import Textarea from "@/Components/Textarea";
import __ from "@/Functions/Translate";
import { usePage } from "@inertiajs/inertia-react";
import { useState, useEffect } from "react";
import { Button } from "react-bootstrap";
import { toast } from "react-toastify";

export default function StreamInstructions({ streamKey, streamUser }) {
  const { auth, rtmp_url } = usePage().props;
  const [tab, setTab] = useState("desktop");
  const [isStreamLive, setIsStreamLive] = useState(false);
  const [isToggling, setIsToggling] = useState(false);

  if (auth?.user?.username !== streamUser) {
    return __("Streamer Private or  offline!");
    toast.success("Streamer Private or  offline!");
  }

  // Check initial stream status
  useEffect(() => {
    if (auth?.user?.live_status === "online") {
      setIsStreamLive(true);
    } else {
      setIsStreamLive(false);
    }
  }, [auth?.user?.live_status]);

  const toggleStream = async (e) => {
    e.preventDefault();
    setIsToggling(true);

    try {
      if (isStreamLive) {
        // Stop streaming
        const response = await axios.get(route("chat.stope-streaming"));
        if (response.data.status === true) {
          setIsStreamLive(false);
          toast.success(__(response.data.message));
        }
      } else {
        // Start streaming
        const response = await axios.get(route("chat.re-start-streaming"));
        if (response.data.status === true) {
          setIsStreamLive(true);
          toast.success(__(response.data.message));
        }
      }
    } catch (error) {
      toast.error(__("Error updating stream status"));
    } finally {
      setIsToggling(false);
    }
  };

  return (
    <div className="bg-footer mb-5 dark:bg-zinc-900 mr-10 p-5 ">
      <div className="mb-5 d-flex justify-content-between align-items-center flex-wrap">
        <div className="iq-button">
          <button
            onClick={(e) => setTab("desktop")}
            className="position-relative btn text-uppercase btn-sm text-indigo-700 dark:text-indigo-500 underline false"
          >
            {__("Desktop Instructions")}
            <i class="fa-solid fa-play ms-2"></i>
          </button>
          <button
            onClick={(e) => setTab("mobile")}
            className="position-relative btn text-uppercase btn-sm text-indigo-700 dark:text-indigo-500 underline false ms-2"
          >
            {__("Mobile Instructions")}
            <i class="fa-solid fa-play ms-2"></i>
          </button>
        </div>

        <div class="flex justify-end items-center">
          <div className="flex items-center space-x-3">
            <span className="text-sm font-medium text-gray-primary">
              {__("Public Stream")}
            </span>
            <div className="flex items-center">
              <button
                onClick={toggleStream}
                disabled={isToggling}
                className={`relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 ${
                  isStreamLive ? "bg-green-600" : "bg-gray-200 dark:bg-gray-600"
                } ${
                  isToggling
                    ? "opacity-50 cursor-not-allowed"
                    : "cursor-pointer"
                }`}
              >
                <span
                  className={`inline-block h-4 w-4 transform rounded-full bg-white transition-transform ${
                    isStreamLive ? "translate-x-6" : "translate-x-1"
                  }`}
                />
              </button>
              <span
                className={`ml-2 text-sm font-medium ${
                  isStreamLive ? "text-green-600" : "text-gray-500"
                }`}
              >
                {isToggling
                  ? __("Updating...")
                  : isStreamLive
                  ? __("LIVE")
                  : __("OFFLINE")}
              </span>
            </div>
          </div>
        </div>
      </div>

      <h2 className="text-2xl pb-2 mt-5 border-b dark:border-b-zinc-800 font-semibold text-gray-primary">
        {__("RTMP Server URL")}
      </h2>
      <TextInput
        className="text-xl mt-3 w-full form-control"
        value={tab === "desktop" ? rtmp_url : `${rtmp_url}/${streamKey}`}
      />

      {tab == "desktop" ? (
        <>
          <h2 className="text-xl pb-2 mt-5 border-b dark:border-b-zinc-800 font-semibold text-gray-primary">
            {__("RTMP Streaming Key")}
          </h2>
          <Textarea className="text-xl w-full form-control" value={streamKey} />

          <h2 className="mt-5 text-2xl pb-2 border-b dark:border-b-zinc-800 font-semibold text-gray-primary">
            {__("Download OBS - Open Broadcaster Software")}
          </h2>
          <a
            className="flex text-primary text-xl hover:underline"
            target="blank"
            href="https://obsproject.com/"
          >
            https://obsproject.com
          </a>

          <h2 className="text-2xl pb-2 mt-5 border-b dark:border-b-zinc-800 font-semibold text-gray-primary">
            {__("Go to OBS->Settings->Stream")}
          </h2>

          <img src="/images/obs.png" alt="obs.png" />

          <h2 className="text-2xl pb-2 mt-5 border-b dark:border-b-zinc-800 font-semibold text-gray-primary">
            {__("Happy Streaming!")}
          </h2>
        </>
      ) : (
        <>
          <h2 className="text-2xl pb-2 mt-5 border-b dark:border-b-zinc-800 font-semibold text-gray-primary">
            {__("Get a Mobile RTMP Ingesting App (ex. Larix Broadcaster)")}
          </h2>

          <a
            className="flex my-5 text-primary text-xl hover:underline"
            target="blank"
            href="https://apps.apple.com/us/app/larix-broadcaster/id1042474385"
          >
            Larix Broadcaster iOS
          </a>

          <a
            className="flex my-5 text-primary text-xl hover:underline"
            target="blank"
            href="https://play.google.com/store/apps/details?id=com.wmspanel.larix_broadcaster&hl=en&gl=US&pli=1"
          >
            Larix Broadcaster Android
          </a>

          <p className="text-gray-primary">
            {__("Click Settings Cog -> Connections -> New Connection")}
          </p>

          <a
            href="https://www.youtube.com/watch?v=Dhj0_QbtfTw&t=24s"
            target="_blank"
          >
            <img src="/images/larix.jpeg" alt="larix app" />
          </a>
        </>
      )}
    </div>
  );
}
