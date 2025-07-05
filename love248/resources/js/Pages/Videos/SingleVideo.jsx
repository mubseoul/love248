import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import PrimaryButton from "@/Components/PrimaryButton";
import { MdGeneratingTokens } from "react-icons/md";
import { Head, Link, usePage } from "@inertiajs/inertia-react";
import { Inertia } from "@inertiajs/inertia";
import { FcUnlock } from "react-icons/fc";
import { BsTagFill } from "react-icons/bs";
import { AiOutlineEye } from "react-icons/ai";
import { FaGrinStars } from "react-icons/fa";
import __ from "@/Functions/Translate";
import React, { useState } from "react";
import axios from "axios";
import DefaultImg from "../../../assets/images/default.png";
import PurchaseModal from "@/Components/PurchaseModal";

const VideoComponent = ({ video, inModal }) => {
  const [showPurchaseModal, setShowPurchaseModal] = useState(false);
  const { auth } = usePage().props;

  const increaseViews = () => {
    axios.post(route("video.increaseViews", { video: video.id }));
  };

  const handleUnlockClick = () => {
    // Check if user is authenticated
    if (!auth.user) {
      // Redirect to login page
      Inertia.visit(route('login'));
      return;
    }

    // For free content, redirect to unlock route which will auto-grant access
    if (parseFloat(video.price) === 0) {
      Inertia.visit(route('video.unlock', { video: video.id }));
      return;
    }
    
    setShowPurchaseModal(true);
  };

  return (
    <div className={`justify-center w-full ${inModal ? "p-3" : "p-0"}`}>
      <div className="flex items-start">
        <div className="mr-5 flex flex-col items-center flex-shrink-0">
          <Link
            href={route("channel", {
              user: video?.streamer?.username,
            })}
          >
            <img
              src={video?.streamer?.profile_picture}
              className="w-14 h-14 rounded-full"
              onError={(e) => {
                e.target.onerror = null;
                e.target.src = DefaultImg;
              }}
            />
          </Link>
        </div>
        <div>
          <h3 className="text-lg md:text-2xl font-light text-gray-primary">
            {video?.title}
          </h3>

          <div className="flex items-center flex-wrap md:space-x-2 mt-1">
            <Link
              href={route("channel", {
                user: video?.streamer?.username,
              })}
              className="text-sm text-gray-primary mr-2"
            >
              @{video?.streamer?.username}
            </Link>

            {video?.category && (
              <Link
                href={route("videos.browse", {
                  videocategory: video?.category.id,
                  slug: `-${video?.category?.slug}`,
                })}
                className="text-gray-primary mr-2 inline-flex items-center space-x-1 text-sm"
              >
                <BsTagFill className="w-3" />
                <span>{video?.category?.category}</span>
              </Link>
            )}

            <span className="text-gray-primary inline-flex items-center space-x-1 text-sm mr-2">
              <AiOutlineEye className="w-5 h-5 mr-1" />
              {video?.views === 1
                ? __("1 view")
                : __(":viewsCount views", {
                    viewsCount: video?.views,
                  })}
            </span>

            {video?.free_for_subs === "yes" && video?.price !== 0 && (
              <div className="mt-1 md:mt-0 inline-flex items-center text-sm bg-gray-100 rounded px-2 py-1 text-gray-500 dark:text-gray-600">
                <FaGrinStars className="w-4 h-4 mr-1" />
                {__("Free For Subscribers")}
              </div>
            )}
          </div>
        </div>
      </div>

      <div className="mt-5">
        {video.canBePlayed ? (
          <div className="min-h-[300px]">
            <video
              className="w-full aspect-video rounded-lg"
              controls
              disablePictureInPicture
              controlsList="nodownload"
              onPlay={(e) => increaseViews()}
            >
              <source src={`${video.videoUrl}#t`} type="video/mp4" />
            </video>
          </div>
        ) : (
          <div className="flex flex-col items-center md:flex-row space-y-5 md:space-y-0 md:space-x-5 min-h-[300px]">
            <div className="relative w-full">
              <img
                src={video.thumbnailUrl || DefaultImg}
                alt={video?.title || "Video thumbnail"}
                className="rounded-lg w-full"
                style={{ filter: "blur(8px)" }}
                onError={(e) => {
                  e.target.onerror = null;
                  e.target.src = DefaultImg;
                }}
              />

              <div className="absolute top-0 left-0 text-center bg-gray-700 w-full h-full bg-opacity-25 rounded-lg">
                <div className="relative top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 flex flex-col justify-center">
                  <div className="w-full">
                    <div className="bg-white inline-flex bg-opacity-25 rounded-full p-2">
                      <FcUnlock className="w-12 h-12" />
                    </div>
                  </div>

                  <div className="iq-button">
                    <PrimaryButton
                      className="flex items-center h-12 mt-5 mx-auto"
                      onClick={handleUnlockClick}
                    >
                      <div className="flex items-center justify-center">
                        <MdGeneratingTokens className="mr-2 w-6 h-6 md:w-8 md:h-8" />
                        {parseFloat(video.price) === 0 
                          ? __("Get Free Access")
                          : __("Unlock for R$ :price", {
                              price: video.price,
                            })
                        }
                      </div>
                    </PrimaryButton>
                  </div>
                </div>
              </div>
            </div>
          </div>
        )}
      </div>
      
      <PurchaseModal 
        show={showPurchaseModal}
        onClose={() => setShowPurchaseModal(false)}
        item={video}
        type="video"
      />
    </div>
  );
};

export default function SingleVideo({ video, inModal = false }) {
  if (inModal) {
    return <VideoComponent video={video} inModal={true} />;
  } else {
    return (
      <AuthenticatedLayout>
        <Head title={video?.title} />
        <VideoComponent video={video} inModal={false} />
      </AuthenticatedLayout>
    );
  }
}
