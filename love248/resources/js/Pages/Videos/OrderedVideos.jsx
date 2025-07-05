import __ from "@/Functions/Translate";
import PrimaryButton from "@/Components/PrimaryButton";
import SecondaryButton from "@/Components/SecondaryButton";
import DangerButton from "@/Components/DangerButton";
import { Head, Link, useForm, usePage } from "@inertiajs/inertia-react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Inertia } from "@inertiajs/inertia";
import { AiOutlineEye } from "react-icons/ai";
import "react-tooltip/dist/react-tooltip.css";
import { useState } from "react";
import Modal from "@/Components/Modal";
import { BsTagFill } from "react-icons/bs";
import TextInput from "@/Components/TextInput";
import AccountNavi from "../Channel/Partials/AccountNavi";
import { Tooltip } from "react-tooltip";
import { MdOutlineVideoLibrary } from "react-icons/md";

export default function VideosOrdered({ videos }) {
  const [showDeleteConfirmation, setShowDeleteConfirmation] = useState(false);
  const [deleteId, setDeleteId] = useState(0);
  const [searchTerm, setSearchTerm] = useState("");
  const { auth } = usePage().props;

  // active tab class
  const activeTabClass =
    "font-bold mr-2 md:mr-4 text-primary border-b-2 border-primary";
  const inactiveTabClass = "mr-2 md:mr-4 text-gray-primary";

  const searchVideos = (e) => {
    e.preventDefault();

    console.log(
      `Would visit: ${route("videos.ordered")}?search_term=${searchTerm}`
    );
    Inertia.visit(`${route("videos.ordered")}?search_term=${searchTerm}`, {
      method: "GET",
      preserveState: true,
      only: videos,
    });
  };

  return (
    <AuthenticatedLayout>
      <Head title={__("Purchased Videos")} />

      <div className="lg:flex lg:space-x-10">
        <AccountNavi active={"videos"} />

        <div className="ml-0 w-full">
          {auth.user.is_streamer == "yes" && (
            <div className="mb-5">
              <Link href={route("videos.list")} className={inactiveTabClass}>
                {__("Upload Videos")}
              </Link>
              <Link href={route("videos.ordered")} className={activeTabClass}>
                {__("Videos Ordered")}
              </Link>
            </div>
          )}

          <div className="p-4 sm:p-8 bg-footer shadow mb-5">
            <header>
              <h2 className="text-lg inline-flex items-center md:text-xl font-medium text-gray-primary">
                <MdOutlineVideoLibrary className="mr-2" />
                {__("My Purchased Videos")}
              </h2>

              <p className="mt-1 mb-2 text-sm text-gray-primary">
                {__("Access to videos you've purchased")}
              </p>

              <form onSubmit={searchVideos}>
                <div className="flex items-center">
                  <TextInput
                    name="search_term"
                    placeholder={__("Search Video")}
                    value={searchTerm}
                    className="form-control"
                    handleChange={(e) => setSearchTerm(e.target.value)}
                  />
                  <div className="iq-button">
                    <PrimaryButton
                      className="ml-2 py-3"
                      onClick={(e) => searchVideos(e)}
                    >
                      {__("IR")}
                    </PrimaryButton>
                  </div>
                </div>
              </form>
            </header>

            {videos.total === 0 && (
              <div className="text-gray-primary">
                {__("No videos to show.")}
              </div>
            )}

            {videos.total !== 0 && (
              <div className="grid grid-cols-1 md:grid-cols-2 md:gap-x-5 gap-y-10">
                {videos.data.map((v) => (
                  <div
                    key={`video-${v.id}`}
                    className="w-full md:w-[340px] xl:w-[420px] mt-5 rounded-xl overflow-hidden bg-black shadow-lg hover:shadow-xl transition-all duration-300"
                  >
                    <div className="relative">
                      <video
                        className="w-full aspect-video"
                        controls
                        disablePictureInPicture
                        controlsList="nodownload"
                        poster={v.thumbnail}
                      >
                        <source src={`${v.videoUrl}`} type="video/mp4" />
                      </video>
                    </div>

                    <div className="px-4 py-3">
                      <div className="mb-3 h-6 overflow-hidden text-white font-semibold">
                        <a
                          data-tooltip-content={v.title}
                          data-tooltip-id={`tooltip-${v.id}`}
                        >
                          {v.title}
                        </a>
                        <Tooltip anchorSelect="a" />
                      </div>

                      <div className="flex items-center flex-wrap space-x-2 text-xs">
                        <Link
                          href={route("channel", {
                            user: v.streamer?.username,
                          })}
                          className="text-gray-300"
                        >
                          @{v.streamer?.username}
                        </Link>

                        <div className="text-gray-300 flex items-center text-xs">
                          <BsTagFill className="mr-1" /> {v.category.category}
                        </div>
                      </div>

                      <div className="mt-3 flex items-center space-x-2 text-sm justify-between">
                        <span className="text-gray-300">
                          <AiOutlineEye className="inline mr-1" />
                          {__("Views")}
                        </span>
                        <span className="px-2 py-1 rounded-lg bg-gray-600 text-white">
                          {v.views}
                        </span>
                      </div>

                      <div className="mt-2 flex items-center space-x-2 text-sm justify-between">
                        <span className="text-gray-300">{__("Streamer")} </span>
                        <Link
                          href={route("channel", {
                            user: v.streamer?.username,
                          })}
                          className="px-2 py-1 rounded-lg bg-sky-500 text-white hover:bg-sky-600 transition-colors duration-300"
                        >
                          {__("Visit Channel")}
                        </Link>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            )}

            {videos.last_page > 1 && (
              <>
                <div className="flex text-gray-600 my-3 text-sm">
                  {__("Page: :pageNumber of :lastPage", {
                    pageNumber: videos.current_page,
                    lastPage: videos.last_page,
                  })}
                </div>

                <SecondaryButton
                  processing={videos.prev_page_url ? false : true}
                  className="mr-3"
                  onClick={(e) => Inertia.visit(videos.prev_page_url)}
                >
                  {__("Previous")}
                </SecondaryButton>

                <SecondaryButton
                  processing={videos.next_page_url ? false : true}
                  onClick={(e) => Inertia.visit(videos.next_page_url)}
                >
                  {__("Next")}
                </SecondaryButton>
              </>
            )}
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
