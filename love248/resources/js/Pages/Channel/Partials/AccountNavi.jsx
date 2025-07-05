import { usePage } from "@inertiajs/inertia-react";
import { Link } from "@inertiajs/inertia-react";
import __ from "@/Functions/Translate";
import {
  BiSolidVideos,
  BiCog,
  BiBell,
  BiWallet,
  BiCreditCard,
  BiUpload,
  BiVideo,
  BiImageAdd,
  BiImage,
  BiUser,
  BiUserPlus,
  BiPlus,
  BiUserX,
  BiMoney,
  BiCalendarEvent,
  BiReceipt,
} from "react-icons/bi";
import { MdGeneratingTokens, MdVideoCall } from "react-icons/md";

export default function AccountNavi({ active }) {
  const { auth } = usePage().props;
  return (
    <div className="lg:w-80 hidden lg:block lg:flex-shrink-0">
      <div className="bg-footer shadow dark:bg-zinc-900 mb-5">
        <Link
          className="block"
          href="#"
          //   href={`${
          //     auth.user.is_streamer === "yes"
          //       ? route("payout.withdraw")
          //       : route("profile.myTokens")
          //   }`}
        >
          <span className="d-flex items-center bg-footer text-gray-primary text-sm font-bold justify-center py-4 border-b">
            <h5 className="m-0 text-gray-primary">{__("Streamer")}</h5>
          </span>
        </Link>
        {auth.user.is_streamer === "yes" && (
          <Link
            className={`block flex items-center items-center font-bold ${
              active == "account" ? "text-indigo-900" : "text-indigo-600"
            } hover:text-indigo-800 py-2 text-gray-primary dark:hover:text-indigo-300 dark:border-zinc-800 border-b border-indigo-100 py-3 px-5`}
            href={route("channel", {
              user: auth.user.username,
            })}
          >
            <BiSolidVideos className="mr-2" />
            {__("My Channel")}
          </Link>
        )}
        {auth.user.is_streamer === "yes" && (
          <Link
            className={`block flex items-center font-bold ${
              active == "channel-settings"
                ? "text-indigo-900"
                : "text-indigo-600"
            } hover:text-indigo-800 py-2 text-gray-primary dark:hover:text-indigo-300  dark:border-zinc-800 border-b border-indigo-100 py-3 px-5`}
            href={route("channel.settings")}
          >
            <BiCog className="mr-2" />
            {__("Channel Settings")}
          </Link>
        )}
        <Link
          className={`block flex items-center font-bold ${
            active == "notifications" ? "text-indigo-900" : "text-indigo-600"
          } hover:text-indigo-800 py-2 text-gray-primary dark:hover:text-indigo-300  dark:border-zinc-800 border-b border-indigo-100 my-2 px-5`}
          href={route("notifications.inbox")}
        >
          <BiBell className="mr-2" />
          {__("Notifications")}
          <span className="bg-red-100 text-red-500 text-xs font-medium ml-2 px-1.5 py-0.5 rounded-full dark:bg-red-500 dark:text-red-100">
            {__(":unreadNotificationsCount new", {
              unreadNotificationsCount: auth.unreadNotifications,
            })}
          </span>
        </Link>
        {auth.user.is_streamer === "no" && auth.user.is_admin === "no" && (
          <Link
            className={`block flex items-center font-bold ${
              active == "withdraw" ? "text-indigo-900" : "text-indigo-600"
            } hover:text-indigo-800 py-2 text-gray-primary dark:hover:text-indigo-300  dark:border-zinc-800 border-b border-indigo-100 py-3 px-5`}
            href={route("myPlan")}
          >
            <BiMoney className="mr-2" />
            {__("My Plan")}
          </Link>
        )}
        
          <Link
            className={`block flex items-center font-bold ${
              active == "withdraw" ? "text-indigo-900" : "text-indigo-600"
            } hover:text-indigo-800 py-2 text-gray-primary dark:hover:text-indigo-300  dark:border-zinc-800 border-b border-indigo-100 py-3 px-5`}
            href={route("profile.myTokens")}
          >
            <MdGeneratingTokens className="mr-2" />
            {__("My Tokens")}
          </Link>
        <Link
          className={`block flex items-center font-bold ${
            active == "transactions" ? "text-indigo-900" : "text-indigo-600"
          } hover:text-indigo-800 py-2 text-gray-primary dark:hover:text-indigo-300  dark:border-zinc-800 border-b border-indigo-100 py-3 px-5`}
          href={route("profile.transactions")}
        >
          <BiReceipt className="mr-2" />
          {__("Transactions")}
        </Link>
        {auth.user.is_streamer === "yes" && (
          <Link
            className={`block flex items-center font-bold ${
              active == "withdraw" ? "text-indigo-900" : "text-indigo-600"
            } hover:text-indigo-800 py-2 text-gray-primary dark:hover:text-indigo-300  dark:border-zinc-800 border-b border-indigo-100 py-3 px-5`}
            href={route("payout.withdraw")}
          >
            <BiWallet className="mr-2" />
            {__("Withdraw")}
          </Link>
        )}
        {auth.user.is_streamer === "yes" && (
          <Link
            className={`block flex items-center font-bold ${
              active == "tiers" ? "text-indigo-900" : "text-indigo-600"
            } hover:text-indigo-800 py-2 text-gray-primary dark:hover:text-indigo-300  dark:border-zinc-800 border-b border-indigo-100 py-3 px-5`}
            href={route("membership.set-tiers")}
          >
            <BiCreditCard className="mr-2" />
            {__("Membership Tiers")}
          </Link>
        )}
        {auth.user.is_streamer === "yes" && (
          <Link
            className={`block flex items-center font-bold ${
              active == "upload-videos" ? "text-indigo-900" : "text-indigo-600"
            } hover:text-indigo-800 py-2 text-gray-primary dark:hover:text-indigo-300  dark:border-zinc-800 border-b border-indigo-100 py-3 px-5`}
            href={route("videos.list")}
          >
            <BiUpload className="mr-2" />
            {__("Upload Videos")}
          </Link>
        )}
        <Link
          className={`block flex items-center font-bold ${
            active == "videos" ? "text-indigo-900" : "text-indigo-600"
          } hover:text-indigo-800 py-2 text-gray-primary dark:hover:text-indigo-300  dark:border-zinc-800 border-b border-indigo-100 py-3 px-5`}
          href={route("videos.ordered")}
        >
          <BiVideo className="mr-2" />
          {__("My Videos")}
        </Link>
        {auth.user.is_streamer === "yes" && (
          <Link
            className={`block flex items-center font-bold ${
              active == "upload-gallery" ? "text-indigo-900" : "text-indigo-600"
            } hover:text-indigo-800 py-2 text-gray-primary dark:hover:text-indigo-300  dark:border-zinc-800 border-b border-indigo-100 py-3 px-5`}
            href={route("gallery.list")}
          >
            <BiImageAdd className="mr-2" />
            {__("Upload Gallery")}
          </Link>
        )}
        <Link
          className={`block flex items-center font-bold ${
            active == "gallery" ? "text-indigo-900" : "text-indigo-600"
          } hover:text-indigo-800 py-2 text-gray-primary dark:hover:text-indigo-300  dark:border-zinc-800 border-b border-indigo-100 py-3 px-5`}
          href={route("gallery.ordered")}
        >
          <BiImage className="mr-2" />
          {__("My Gallery")}
        </Link>
        {auth.user.is_streamer === "yes" && (
          <Link
            className={`block flex items-center font-bold ${
              active == "followers" ? "text-indigo-900" : "text-indigo-600"
            } hover:text-indigo-800 py-2 text-gray-primary dark:hover:text-indigo-300  dark:border-zinc-800 border-b border-indigo-100 py-3 px-5`}
            href={route("channel.followers", {
              user: auth.user.username,
            })}
          >
            <BiUser className="mr-2" />
            {__("My Followers")}
          </Link>
        )}
        {auth.user.is_streamer === "yes" && (
          <Link
            className={`block flex items-center font-bold ${
              active == "my-subscribers" ? "text-indigo-900" : "text-indigo-600"
            } hover:text-indigo-800 py-2 text-gray-primary dark:hover:text-indigo-300 dark:border-zinc-800 border-b border-indigo-100 py-3 px-5`}
            href={route("mySubscribers")}
          >
            <BiUserPlus className="mr-2" />
            {__("My Subscribers")}
          </Link>
        )}
        {auth.user.is_streamer === "yes" && (
          <Link
            className={`block flex items-center font-bold ${
              active == "add-streaming" ? "text-indigo-900" : "text-indigo-600"
            } hover:text-indigo-800 py-2 text-gray-primary dark:hover:text-indigo-300  dark:border-zinc-800 border-b border-indigo-100 py-3 px-5`}
            href={route("getStreamingList")}
          >
            <BiPlus className="mr-2" />
            {__("Add Streaming")}
          </Link>
        )}
        {auth.user.is_streamer === "yes" && (
          <Link
            className={`block flex items-center font-bold ${
              active == "private-stream-management"
                ? "text-indigo-900"
                : "text-indigo-600"
            } hover:text-indigo-800 py-2 text-gray-primary dark:hover:text-indigo-300  dark:border-zinc-800 border-b border-indigo-100 py-3 px-5`}
            href={route("private-stream.pending-requests")}
          >
            <MdVideoCall className="mr-2" />
            {__("Private Stream Requests")}
          </Link>
        )}
        {auth.user.is_streamer === "no" && (
          <Link
            className={`block flex items-center font-bold ${
              active == "my-private-streams"
                ? "text-indigo-900"
                : "text-indigo-600"
            } hover:text-indigo-800 py-2 text-gray-primary dark:hover:text-indigo-300  dark:border-zinc-800 border-b border-indigo-100 py-3 px-5`}
            href={route("private-stream.my-bookings")}
          >
            <BiCalendarEvent className="mr-2" />
            {__("My Private Streams")}
          </Link>
        )}
        <Link
          className={`block flex items-center font-bold ${
            active == "following" ? "text-indigo-900" : "text-indigo-600"
          } hover:text-indigo-800 py-2 text-gray-primary dark:hover:text-indigo-300  dark:border-zinc-800 border-b border-indigo-100 py-3 px-5`}
          href={route("profile.followings")}
        >
          <BiUserPlus className="mr-2" />
          {__("My Followings")}
        </Link>
        <Link
          className={`block flex items-center font-bold ${
            active == "my-subscriptions" ? "text-indigo-900" : "text-indigo-600"
          } hover:text-indigo-800 py-2 text-gray-primary dark:hover:text-indigo-300  dark:border-zinc-800 border-b border-indigo-100 py-3 px-5`}
          href={route("mySubscriptions")}
        >
          <BiUserPlus className="mr-2" />
          {__("My Subscriptions")}
        </Link>
        {auth.user.is_streamer === "yes" && (
          <Link
            className={`block flex items-center font-bold ${
              active == "channel-settings"
                ? "text-indigo-900"
                : "text-indigo-600"
            } hover:text-indigo-800 py-2 text-gray-primary dark:hover:text-indigo-300  dark:border-zinc-800 border-b border-indigo-100 py-3 px-5`}
            href={route("channel.bannedUsers")}
          >
            <BiUserX className="mr-2" />
            {__("Banned Users")}
          </Link>
        )}
        <Link
          className={`block flex items-center font-bold ${
            active == "account" ? "text-indigo-900" : "text-indigo-600"
          } hover:text-indigo-800 py-2 text-gray-primary dark:hover:text-indigo-300  dark:border-zinc-800 border-b border-indigo-100 py-3 px-5`}
          href={route("profile.edit")}
        >
          <BiUser className="mr-2" />
          {__("My Account")}
        </Link>
      </div>
    </div>
  );
}
