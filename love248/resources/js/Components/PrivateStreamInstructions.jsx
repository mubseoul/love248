import TextInput from "@/Components/TextInput";
import Textarea from "@/Components/Textarea";
import __ from "@/Functions/Translate";
import { usePage } from "@inertiajs/inertia-react";
import { useState } from "react";

export default function PrivateStreamInstructions({
  streamKey,
  streamUser,
  streamRequest,
}) {
  const { auth, rtmp_url } = usePage().props;
  const [tab, setTab] = useState("desktop");

  if (auth?.user?.username !== streamUser) {
    return (
      <div className="bg-footer mb-5 dark:bg-zinc-900 mr-10 p-5">
        <p className="text-gray-primary">
          {__("Access denied to streaming instructions.")}
        </p>
      </div>
    );
  }

  return (
    <div className="bg-footer mb-5 dark:bg-zinc-900 mr-10 p-5">
      {/* Header with private stream info */}
      <div className="mb-5 pb-3 border-b dark:border-b-zinc-800">
        <h2 className="text-2xl font-semibold text-gray-primary mb-2">
          {__("Private Stream Setup Instructions")}
        </h2>
        <p className="text-gray-400 text-sm">
          {__(
            "Configure your streaming software for this private session with :user",
            {
              user: streamRequest?.user?.name || "the viewer",
            }
          )}
        </p>
      </div>

      {/* Tab buttons */}
      <div className="mb-5 d-flex justify-content-start align-items-center flex-wrap">
        <div className="iq-button">
          <button
            onClick={() => setTab("desktop")}
            className={`position-relative btn text-uppercase btn-sm mr-2 px-4 py-2 rounded-lg transition-colors ${
              tab === "desktop"
                ? "bg-primary text-white"
                : "text-indigo-700 dark:text-indigo-500 hover:bg-gray-100 dark:hover:bg-gray-700"
            }`}
          >
            {__("Desktop Instructions")}
            <i className="fa-solid fa-desktop ms-2"></i>
          </button>
          <button
            onClick={() => setTab("mobile")}
            className={`position-relative btn text-uppercase btn-sm px-4 py-2 rounded-lg transition-colors ${
              tab === "mobile"
                ? "bg-primary text-white"
                : "text-indigo-700 dark:text-indigo-500 hover:bg-gray-100 dark:hover:bg-gray-700"
            }`}
          >
            {__("Mobile Instructions")}
            <i className="fa-solid fa-mobile ms-2"></i>
          </button>
        </div>
      </div>

      {/* RTMP Server URL */}
      <h2 className="text-2xl pb-2 mt-5 border-b dark:border-b-zinc-800 font-semibold text-gray-primary">
        {__("RTMP Server URL")}
      </h2>
      <TextInput
        className="text-xl mt-3 w-full form-control"
        value={tab === "desktop" ? rtmp_url : `${rtmp_url}/${streamKey}`}
        readOnly
      />

      {tab === "desktop" ? (
        <>
          {/* Desktop Instructions */}
          <h2 className="text-xl pb-2 mt-5 border-b dark:border-b-zinc-800 font-semibold text-gray-primary">
            {__("RTMP Streaming Key")}
          </h2>
          <Textarea
            className="text-xl w-full form-control"
            value={streamKey}
            readOnly
            rows={2}
          />

          <div className="bg-yellow-100 dark:bg-yellow-900 border border-yellow-400 rounded-lg p-4 mt-4">
            <div className="flex items-center">
              <i className="fa-solid fa-exclamation-triangle text-yellow-600 mr-2"></i>
              <p className="text-yellow-800 dark:text-yellow-200 text-sm">
                <strong>{__("Important:")}</strong>{" "}
                {__(
                  "This streaming key is unique to this private session. Do not share it with anyone."
                )}
              </p>
            </div>
          </div>

          <h2 className="mt-5 text-2xl pb-2 border-b dark:border-b-zinc-800 font-semibold text-gray-primary">
            {__("Download OBS - Open Broadcaster Software")}
          </h2>
          <a
            className="flex text-primary text-xl hover:underline"
            target="_blank"
            rel="noopener noreferrer"
            href="https://obsproject.com/"
          >
            https://obsproject.com
          </a>

          <h2 className="text-2xl pb-2 mt-5 border-b dark:border-b-zinc-800 font-semibold text-gray-primary">
            {__("OBS Setup Steps for Private Streaming")}
          </h2>

          <div className="mt-4 space-y-3">
            <div className="flex items-start space-x-3">
              <span className="bg-primary text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold">
                1
              </span>
              <p className="text-gray-primary">
                {__("Open OBS Studio and go to Settings → Stream")}
              </p>
            </div>
            <div className="flex items-start space-x-3">
              <span className="bg-primary text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold">
                2
              </span>
              <p className="text-gray-primary">
                {__("Select 'Custom...' as your Service")}
              </p>
            </div>
            <div className="flex items-start space-x-3">
              <span className="bg-primary text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold">
                3
              </span>
              <p className="text-gray-primary">
                {__("Copy and paste the Server URL above")}
              </p>
            </div>
            <div className="flex items-start space-x-3">
              <span className="bg-primary text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold">
                4
              </span>
              <p className="text-gray-primary">
                {__("Copy and paste the Stream Key above")}
              </p>
            </div>
            <div className="flex items-start space-x-3">
              <span className="bg-primary text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold">
                5
              </span>
              <p className="text-gray-primary">{__("Click Apply, then OK")}</p>
            </div>
            <div className="flex items-start space-x-3">
              <span className="bg-green-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold">
                ✓
              </span>
              <p className="text-gray-primary">
                {__("You're ready to start your private stream!")}
              </p>
            </div>
          </div>

          <img
            src="/images/obs.png"
            alt="OBS Settings Screenshot"
            className="mt-4 rounded-lg border border-gray-600"
          />

          <h2 className="text-2xl pb-2 mt-5 border-b dark:border-b-zinc-800 font-semibold text-gray-primary">
            {__("Ready to Stream!")}
          </h2>
          <p className="text-gray-primary mt-2">
            {__(
              "Once you've configured OBS, click 'Start Streaming' in OBS to begin your private session."
            )}
          </p>
        </>
      ) : (
        <>
          {/* Mobile Instructions */}
          <h2 className="text-2xl pb-2 mt-5 border-b dark:border-b-zinc-800 font-semibold text-gray-primary">
            {__("Mobile RTMP Streaming Apps")}
          </h2>

          <div className="grid md:grid-cols-2 gap-4 mt-4">
            <div className="bg-black dark:bg-gray-800 rounded-lg p-4">
              <h3 className="font-semibold text-gray-primary mb-2">
                {__("iOS - Larix Broadcaster")}
              </h3>
              <a
                className="text-primary text-lg hover:underline block"
                target="_blank"
                rel="noopener noreferrer"
                href="https://apps.apple.com/us/app/larix-broadcaster/id1042474385"
              >
                {__("Download from App Store")}
              </a>
            </div>

            <div className="bg-black dark:bg-gray-800 rounded-lg p-4">
              <h3 className="font-semibold text-gray-primary mb-2">
                {__("Android - Larix Broadcaster")}
              </h3>
              <a
                className="text-primary text-lg hover:underline block"
                target="_blank"
                rel="noopener noreferrer"
                href="https://play.google.com/store/apps/details?id=com.wmspanel.larix_broadcaster&hl=en&gl=US&pli=1"
              >
                {__("Download from Google Play")}
              </a>
            </div>
          </div>

          <h2 className="text-2xl pb-2 mt-5 border-b dark:border-b-zinc-800 font-semibold text-gray-primary">
            {__("Mobile Setup Steps")}
          </h2>

          <div className="mt-4 space-y-3">
            <div className="flex items-start space-x-3">
              <span className="bg-primary text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold">
                1
              </span>
              <p className="text-gray-primary">
                {__("Install and open Larix Broadcaster")}
              </p>
            </div>
            <div className="flex items-start space-x-3">
              <span className="bg-primary text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold">
                2
              </span>
              <p className="text-gray-primary">
                {__("Tap the Settings cog → Connections → New Connection")}
              </p>
            </div>
            <div className="flex items-start space-x-3">
              <span className="bg-primary text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold">
                3
              </span>
              <p className="text-gray-primary">
                {__(
                  "Enter the complete RTMP URL from above (includes stream key)"
                )}
              </p>
            </div>
            <div className="flex items-start space-x-3">
              <span className="bg-primary text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold">
                4
              </span>
              <p className="text-gray-primary">
                {__("Save the connection and start streaming")}
              </p>
            </div>
          </div>

          <div className="mt-4">
            <a
              href="https://www.youtube.com/watch?v=Dhj0_QbtfTw&t=24s"
              target="_blank"
              rel="noopener noreferrer"
              className="block"
            >
              <img
                src="/images/larix.jpeg"
                alt="Larix Broadcaster Setup Guide"
                className="rounded-lg border border-gray-600 hover:opacity-90 transition-opacity"
              />
            </a>
            <p className="text-sm text-gray-400 mt-2 text-center">
              {__("Click to watch setup tutorial on YouTube")}
            </p>
          </div>
        </>
      )}
    </div>
  );
}
