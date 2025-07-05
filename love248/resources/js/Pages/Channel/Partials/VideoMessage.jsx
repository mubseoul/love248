import React, { useState, useRef } from "react";
import InputError from "@/Components/InputError";
import InputLabel from "@/Components/InputLabel";
import PrimaryButton from "@/Components/PrimaryButton";
import { useForm, usePage } from "@inertiajs/inertia-react";
import { Transition } from "@headlessui/react";
import __ from "@/Functions/Translate";
import axios from "axios";
import { toast } from "react-toastify";

export default function VideoMessage() {
  const user = usePage().props.auth.user;
  const [uploading, setUploading] = useState(false);
  const [uploadError, setUploadError] = useState("");
  const fileInputRef = useRef();

  const { data, setData, errors, processing, recentlySuccessful, reset } =
    useForm({
      video: null,
    });

  const handleFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
      setData("video", file);
      // Create a preview URL for the selected video
      const previewUrl = URL.createObjectURL(file);
      setUploadError("");
    }
  };

  const uploadVideo = async (e) => {
    e.preventDefault();

    if (!data.video) {
      setUploadError("Please select a video file");
      return;
    }

    setUploading(true);
    setUploadError("");

    const formData = new FormData();
    formData.append("video", data.video);
    formData.append("user", user.id);

    try {
      const response = await axios.post(route("video.MessageVideo"), formData, {
        headers: {
          "Content-Type": "multipart/form-data",
        },
        onUploadProgress: (progressEvent) => {
          // Implement progress tracking if needed
          const percentCompleted = Math.round(
            (progressEvent.loaded * 100) / progressEvent.total
          );
          console.log(`Upload progress: ${percentCompleted}%`);
        },
      });

      if (response.data.success) {
        user.message_video = response.data.file_path;
        // Show toast notification
        toast.success(__("Video uploaded successfully!"));
        reset();
        if (fileInputRef.current) {
          fileInputRef.current.value = "";
        }
      }
    } catch (error) {
      console.error("Upload error:", error);
      setUploadError(
        error.response?.data?.message ||
          "Failed to upload video. Please try again."
      );
      // Show error toast notification
      toast.error(
        error.response?.data?.message ||
          __("Failed to upload video. Please try again.")
      );
    } finally {
      setUploading(false);
    }
  };

  return (
    <section className="mt-5">
      <header>
        <div className="flex items-center">
          <h2 className="text-xl font-medium text-gray-primary">
            {__("Message Video")}
          </h2>
        </div>
        <p className="mt-1 text-sm text-gray-500">
          {__("Upload a video message for your followers (Max 2MB)")}
        </p>
        {user.message_video && user.message_video.trim() !== "" && (
          <video
            controls
            className="w-full max-h-64 rounded"
            src={`/storage/${user.message_video}`}
          >
            {__("Your browser does not support the video tag.")}
          </video>
        )}
      </header>

      <form onSubmit={uploadVideo} className="mt-6 space-y-6">
        <div>
          <InputLabel
            className="block font-medium text-sm text-gray-primary fw-500 mb-2 form-label"
            htmlFor="messageVideo"
            value={__("Message Video")}
          />

          <input
            className="text-white form-control"
            id="messageVideo"
            type="file"
            ref={fileInputRef}
            accept="video/mp4,video/webm,video/ogg,video/mov,video/qt"
            onChange={handleFileChange}
          />

          {uploadError && <InputError className="mt-2" message={uploadError} />}
        </div>

        <div className="flex items-center gap-4">
          <PrimaryButton disabled={uploading || !data.video}>
            {uploading ? __("Uploading...") : __("Save")}
          </PrimaryButton>
        </div>
      </form>
    </section>
  );
}
