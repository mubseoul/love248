import React, { useEffect } from "react";
import { Link, Head, usePage } from "@inertiajs/inertia-react";
import Front from "@/Layouts/Front";
import __ from "@/Functions/Translate";
import { toast } from "react-toastify";
import { Button } from "react-bootstrap";

export default function Signup({ props }) {
  const influencerIcon = "/images/streamer-icon.png";
  const userIcon = "/images/user-signup-icon.png";

  const { flash } = usePage().props;

  // Flash messages now handled globally in app.jsx

  return (
    <Front>
      <div
        className="mx-auto dark:bg-zinc-900 shadow py-5 max-w-5xl text-center mb-5"
        style={{ background: "#141314" }}
      >
        <h2 className="text-3xl text-gray-600 text-white font-semibold text-center">
          {__("Join Our Platform")}
        </h2>
        <p className="text-center mb-8 text-xl text-white dark:text-zinc-200 mt-1">
          {__(
            "We are welcoming both streamers and users to our platform to get connected to each other."
          )}
        </p>
        <div className="grid grid-cols-2 mt-10 gap-2">
          <div className="col iq-button text-center">
            <Link href={route("streamer.signup")}>
              <img
                src={influencerIcon}
                alt=""
                className="max-h-96 rounded-full mx-auto border-zinc-200 dark:border-indigo-200 border-4"
              />
            </Link>
            <Button
              href={route("streamer.signup")}
              className="me-2 btn text-uppercase position-relative d-inline-flex mt-5 btn-sm align-items-center btn btn-primary"
            >
              {__("I'm a Streamer")}
              <i class="fa-solid fa-play"></i>
            </Button>
          </div>
          <div className="col iq-button text-center">
            <Link href={route("register")}>
              <img
                src={userIcon}
                alt=""
                className="max-h-96 rounded-full mx-auto border-zinc-200  dark:border-indigo-200 border-4"
              />
            </Link>
            <Button
              href={route("register")}
              className="me-2 btn text-uppercase position-relative d-inline-flex mt-5 btn-sm align-items-center btn btn-primary"
            >
              {__("I am an User")}
              <i class="fa-solid fa-play"></i>
            </Button>
          </div>
        </div>
      </div>
    </Front>
  );
}
