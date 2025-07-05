import __ from "@/Functions/Translate";
import PrimaryButton from "@/Components/PrimaryButton";
import SecondaryButton from "@/Components/SecondaryButton";
import DangerButton from "@/Components/DangerButton";
import { Head, Link } from "@inertiajs/inertia-react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Inertia } from "@inertiajs/inertia";
import { AiOutlineEdit, AiOutlineArrowRight } from "react-icons/ai";
import { RiDeleteBin5Line } from "react-icons/ri";
import { Tooltip } from "react-tooltip";
import "react-tooltip/dist/react-tooltip.css";
import { useState } from "react";
import Modal from "@/Components/Modal";
import { BsTagFill } from "react-icons/bs";
import AccountNavi from "../Channel/Partials/AccountNavi";
import { MdOutlineVideoLibrary } from "react-icons/md";

export default function Upload({ videos }) {
  const [showDeleteConfirmation, setShowDeleteConfirmation] = useState(false);
  const [deleteId, setDeleteId] = useState(0);

  const confirmDelete = (e, id) => {
    e.preventDefault();

    setShowDeleteConfirmation(true);
    setDeleteId(id);
  };

  const deleteVideo = () => {
    Inertia.visit(route("videos.delete"), {
      method: "POST",
      data: { video: deleteId },
      preserveState: false,
    });
  };

  // active tab class
  const activeTabClass =
    "font-bold mr-2 md:mr-4 text-primary border-b-2 border-primary";
  const inactiveTabClass = "mr-2 md:mr-4 text-gray-primary";

  return (
    <AuthenticatedLayout>
      <Head title={__("Videos")} />

      <Modal
        show={showDeleteConfirmation}
        onClose={(e) => setShowDeleteConfirmation(false)}
      >
        <div className="px-5 py-10 text-center">
          <h3 className="text-xl mb-3 text-zinc-700 dark:text-white">
            {__("Are you sure you want to remove this Video?")}
          </h3>
          <DangerButton onClick={(e) => deleteVideo()}>
            {__("Yes")}
          </DangerButton>
          <SecondaryButton
            className="ml-3"
            onClick={(e) => setShowDeleteConfirmation(false)}
          >
            {__("No")}
          </SecondaryButton>
        </div>
      </Modal>

      <div className="lg:flex lg:space-x-10">
        <AccountNavi active={"upload-videos"} />
        <div className="ml-0">
          <Link href={route("videos.list")} className={activeTabClass}>
            {__("Upload Videos")}
          </Link>
          <Link href={route("videos.ordered")} className={inactiveTabClass}>
            {__("Videos Ordered")}
          </Link>

          <div className="my-5 p-4 sm:p-8 bg-footer shadow mb-5">
            <header>
              <h2 className="text-lg inline-flex items-center md:text-xl font-medium text-gray-primary">
                <MdOutlineVideoLibrary className="mr-2" />
                {__("My Videos")}
              </h2>

              <p className="mt-1 mb-2 text-sm text-gray-primary">
                {__("Upload & manage videos for the channel")}
              </p>
              <div className="iq-button">
                <PrimaryButton
                  onClick={(e) => Inertia.visit(route("videos.upload"))}
                >
                  {__("Upload Video")}
                  <i className="fa-solid fa-play ml-2"></i>
                </PrimaryButton>
              </div>
            </header>

            {videos.total === 0 && (
              <div className="text-gray-primary">
                {__("You didn't upload any videos yet.")}
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

                      {v.status === 0 && (
                        <div className="absolute top-2 right-2 bg-yellow-500 text-white px-2 py-1 rounded text-xs font-semibold">
                          {__("Pending Approval")}
                        </div>
                      )}
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

                      <div className="text-gray-300 flex items-center space-x-2 text-xs">
                        <BsTagFill className="mr-1" /> {v.category.category}
                      </div>

                      <div className="mt-3 flex items-center space-x-2 text-sm justify-between">
                        <span className="text-gray-300">{__("Price")} </span>
                        <span className="px-2 py-1 text-sm rounded-lg bg-sky-500 text-white">
                          {v.price > 0
                            ? __(":tokensPrice tokens", {
                                tokensPrice: v.price,
                              })
                            : __("Free")}
                        </span>
                      </div>

                      <div className="mt-2 flex items-center space-x-2 text-sm justify-between">
                        <span className="text-gray-300">
                          {__("Free for subs")}{" "}
                        </span>
                        <span className="px-2 py-1 rounded-lg bg-teal-500 text-white">
                          {v.free_for_subs}
                        </span>
                      </div>

                      <div className="flex mt-2 items-center space-x-2 text-sm justify-between">
                        <span className="text-gray-300">{__("Views")} </span>
                        <span className="px-2 py-1 rounded-lg bg-gray-600 text-white">
                          {v.views}
                        </span>
                      </div>

                      <div className="flex mt-2 items-center space-x-2 text-sm justify-between">
                        <span className="text-gray-300">{__("Status")} </span>
                        <span
                          className={`px-2 py-1 rounded-lg text-white ${
                            v.status === 1 ? "bg-green-500" : "bg-yellow-500"
                          }`}
                        >
                          {v.status === 1 ? __("Approved") : __("Pending")}
                        </span>
                      </div>

                      <div className="flex mt-2 items-center space-x-2 text-sm justify-between">
                        <span className="text-gray-300">{__("Earnings")} </span>
                        <span className="px-2 py-1 rounded-lg bg-pink-500 text-white">
                          {v.sales_sum_price
                            ? __(":salesTokens tokens", {
                                salesTokens: v.sales_sum_price,
                              })
                            : "--"}
                        </span>
                      </div>

                      <div className="border-t border-gray-700 pt-3 mt-3 flex items-center justify-between">
                        <div className="flex space-x-2">
                          <Link
                            href={route("videos.edit", {
                              video: v.id,
                            })}
                            className="px-2 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-md flex items-center transition-colors duration-300"
                          >
                            <AiOutlineEdit />
                          </Link>
                          <button
                            onClick={(e) => confirmDelete(e, v.id)}
                            className="px-2 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md flex items-center transition-colors duration-300"
                          >
                            <RiDeleteBin5Line />
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            )}

            {videos.last_page > 1 && (
              <>
                <hr className="my-5" />

                <div className="flex text-gray-primary my-3 text-sm">
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
