import { Head } from "@inertiajs/inertia-react";
import Front from "@/Layouts/Front";
import __ from "@/Functions/Translate";
import { useEffect } from "react";
import { Inertia } from "@inertiajs/inertia";
import Playlist from "@/Components/New/Playlist";
import Video from "@/Components/New/Video";
import HomepageHeader from "@/Components/HomepageHeader";
import PWAStatus from "@/Components/PWAStatus";

export default function Homepage({
  channels,
  videos,
  meta_title,
  meta_keys,
  meta_description,
}) {
  useEffect(() => {
    // listen for live streaming events
    window.Echo.channel("LiveStreamRefresh").listen(
      ".livestreams.refresh",
      (data) => {
        console.log(`refresh livestreams`);
        Inertia.reload();
      }
    );
  }, []);

  return (
    <Front containerClass="w-full">
      <Head>
        <title>{meta_title}</title>
        <meta name="description" content={meta_description} />
        <meta name="keywords" content={meta_keys} />
      </Head>

      {/* PWA Status - Only in development */}
      {/* {process.env.NODE_ENV === 'development' && (
        <PWAStatus showDetails={true} />
      )} */}

      {/* featured videos */}
      <HomepageHeader videos={videos} />

      {/* channels code */}
      {!channels?.length && (
        <div className="text-center text-xl font-medium dark:text-white text-white py-5">
          No channels to show
        </div>
      )}
      {channels?.length > 0 && <Playlist channels={channels} />}

      {/* Videos code */}
      {!videos?.length && (
        <div className="text-center text-xl font-medium dark:text-white text-white py-5">
          No videos to show
        </div>
      )}
      {videos?.length > 0 && <Video videos={videos} />}
    </Front>
  );
}
