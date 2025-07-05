import React, { Fragment } from "react";
import __ from "@/Functions/Translate";
import { useState } from "react";
import { toast } from "react-toastify";
import { Button } from "react-bootstrap";
import PrivateChat from "@/Pages/Channel/PrivateChat";
import { usePage } from "@inertiajs/inertia-react";

export default function PrivateLiveSream({ stripePublicKey }) {
  const [showMessage, setShowMessage] = useState(true);
  const { auth } = usePage().props;

  const stopStreaming = async (e) => {
    e.preventDefault();

    axios
      .get(route("chat.stope-streaming"))
      .then((resp) => {
        console.log("resp", resp);
        if (resp.data.status === true) {
          toast.success(__(resp.data.message));
        }
      })
      .catch((Error) => toast.error(__("Error banning user")));
  };

  return (
    <Fragment>
      <div
        className={`${
          showMessage ? "flex" : "hidden"
        } mb-3 lg:mt-0 p-3 bg-dark border dark:bg-zinc-800 me-3 text-white text-indigo-700 font-medium align-items-center justify-content-between`}
      >
        {__(
          "If you just started streaming in OBS, refresh this page after 30 seconds to see your stream."
        )}
        <Button
          className="position-relative btn text-uppercase btn-sm text-indigo-700 dark:text-indigo-500 underline false"
          onClick={(e) => setShowMessage(false)}
        >
          {__("Close message")}
          <i class="fa-solid fa-xmark ms-2"></i>
        </Button>
      </div>
      <div class="flex justify-end my-3">
        <PrivateChat streamer={auth?.user} stripePublicKey={stripePublicKey} />
        <Button
          className="me-2 btn text-uppercase position-relative d-flex"
          onClick={stopStreaming}
        >
          {__("Stop Steaming")}
        </Button>
      </div>
    </Fragment>
  );
}
