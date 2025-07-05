import {
  Button,
  Nav,
  Navbar,
  Offcanvas,
  Container,
  Dropdown,
} from "react-bootstrap";
import {
  BiSolidVideos,
  BiCog,
  BiBell,
  BiCreditCard,
  BiUpload,
  BiVideo,
  BiImageAdd,
  BiImage,
  BiUser,
  BiUserPlus,
  BiPlus,
} from "react-icons/bi";
import { memo, Fragment, useState, useEffect } from "react";
import user from "../../../assets/images/user/user1.webp";
import { Link, usePage } from "@inertiajs/inertia-react";
import "react-toastify/dist/ReactToastify.css";
import CustomToggle from "./CustomToggle";
import __ from "@/Functions/Translate";
import { toast } from "react-toastify";
import Logo from "./Logo";
import { MdGeneratingTokens } from "react-icons/md";

const HeaderDefault = memo(() => {
  const { auth, flash } = usePage().props;
  const [isMega, setIsMega] = useState(true);
  const [show1, setShow1] = useState(false);
  const [show, setShow] = useState(false);

  useEffect(() => {
    if (flash?.message) {
      toast(flash.message);
    }

    const handleScroll = () => {
      const headerSticky = document.querySelector(".header-sticky");
      if (headerSticky) {
        if (window.scrollY > 1) {
          headerSticky.classList.add("sticky");
        } else {
          headerSticky.classList.remove("sticky");
        }
      }
    };

    // const updateIsMega = () => {
    //     setIsMega(location.pathname === "/");
    // };

    window.addEventListener("scroll", handleScroll);
    // updateIsMega();

    return () => {
      window.removeEventListener("scroll", handleScroll);
    };
  }, []);
  return (
    <Fragment>
      <header className="header-center-home header-default header-sticky">
        <Navbar
          expand="xl"
          className="nav navbar-light iq-navbar header-hover-menu py-xl-0"
        >
          <Container fluid className="navbar-inner">
            <div className="d-flex align-items-center justify-content-between w-100 landing-header">
              <div className="d-flex gap-3 gap-xl-0 align-items-center">
                <div>
                  <button
                    type="button"
                    data-bs-toggle="offcanvas"
                    data-bs-target="#navbar_main"
                    aria-controls="navbar_main"
                    className="d-xl-none btn btn-primary rounded-pill p-1 pt-0 toggle-rounded-btn"
                    onClick={() => setShow1(!show1)}
                  >
                    <svg width="20px" className="icon-20" viewBox="0 0 24 24">
                      <path
                        fill="currentColor"
                        d="M4,11V13H16L10.5,18.5L11.92,19.92L19.84,12L11.92,4.08L10.5,5.5L16,11H4Z"
                      ></path>
                    </svg>
                  </button>
                </div>
                <Logo></Logo>
              </div>
              <Navbar
                expand="xl"
                className={`offcanvas mobile-offcanvas nav hover-nav horizontal-nav py-xl-0 ${
                  show1 === true ? "show" : ""
                } ${isMega ? "mega-menu-content" : ""}`}
                style={{
                  visibility: `${show1 === true ? "visible" : "hidden"}`,
                }}
                id="navbar_main"
              >
                <Container fluid className="container-fluid p-lg-0">
                  <Offcanvas.Header className="px-0" closeButton>
                    <div className="navbar-brand ms-3">
                      <Logo></Logo>
                    </div>
                  </Offcanvas.Header>
                  <ul
                    className="navbar-nav iq-nav-menu list-unstyled"
                    id="header-menu"
                  >
                    <Nav.Item as="li">
                      <Nav.Link
                        as={Link}
                        href={route("home")}
                        className={`${
                          location.pathname === "/" ||
                          location.pathname === "/home"
                            ? "active"
                            : ""
                        }`}
                      >
                        <span className="item-name">Home</span>
                      </Nav.Link>
                    </Nav.Item>
                    <Nav.Item as="li">
                      <Nav.Link
                        as={Link}
                        href={route("channels.browse")}
                        className={`${
                          location.pathname === "/browse-channels"
                            ? "active"
                            : ""
                        }`}
                      >
                        <span className="item-name">Channels</span>
                      </Nav.Link>
                    </Nav.Item>
                    <Nav.Item as="li">
                      <Nav.Link
                        as={Link}
                        href={route("videos.browse")}
                        className={`${
                          location.pathname === "/browse-videos" ? "active" : ""
                        }`}
                      >
                        <span className="item-name">Videos</span>
                      </Nav.Link>
                    </Nav.Item>
                    <Nav.Item as="li">
                      <Nav.Link
                        as={Link}
                        href={route("gallery.browse")}
                        className={`${
                          location.pathname === "/browse-gallery"
                            ? "active"
                            : ""
                        }`}
                      >
                        <span className="item-name">Gallery</span>
                      </Nav.Link>
                    </Nav.Item>
                    <Nav.Item as="li">
                      <Nav.Link
                        as={Link}
                        href={route("token.packages")}
                        className={`${
                          location.pathname === "/get-tokens" ? "active" : ""
                        }`}
                      >
                        <span className="item-name">Token Packs</span>
                      </Nav.Link>
                    </Nav.Item>
                    <Nav.Item as="li">
                      <Nav.Link
                        as={Link}
                        href={route("subscription.plan")}
                        className={`${
                          location.pathname === "/get-subscriptions"
                            ? "active"
                            : ""
                        }`}
                      >
                        <span className="item-name">Subscription Plans</span>
                      </Nav.Link>
                    </Nav.Item>

                    {!auth.user && (
                      <Fragment>
                        <Nav.Item as="li">
                          <Nav.Link
                            as={Link}
                            href={route("login")}
                            className={`${
                              location.pathname === "/login" ? "active" : ""
                            }`}
                          >
                            <span className="item-name">Login</span>
                          </Nav.Link>
                        </Nav.Item>
                        <Nav.Item as="li">
                          <Nav.Link
                            as={Link}
                            href={route("signup")}
                            className={`${
                              location.pathname === "/signup" ? "active" : ""
                            }`}
                          >
                            <span className="item-name">Signup</span>
                          </Nav.Link>
                        </Nav.Item>
                      </Fragment>
                    )}
                  </ul>
                </Container>
              </Navbar>
              <div className="right-panel">
                <Button
                  id="navbar-toggle"
                  bsPrefix="navbar-toggler"
                  type="button"
                  aria-expanded={show}
                  data-bs-toggle="collapse"
                  data-bs-target="#navbarSupportedContent"
                  onClick={() => setShow(!show)}
                >
                  <span className="navbar-toggler-btn">
                    <span className="navbar-toggler-icon"></span>
                  </span>
                </Button>

                <div
                  className={`navbar-collapse ${
                    show === true ? "collapse" : "show"
                  }`}
                  id="navbarSupportedContent"
                >
                  {auth.user && (
                    <ul className="navbar-nav align-items-center ms-auto mb-2 mb-xl-0 rounded-0">
                      <Dropdown as="li" className="nav-item">
                        <Dropdown.Toggle
                          as={CustomToggle}
                          href="#"
                          variant=" nav-link d-flex align-items-center"
                          size="sm"
                          id="dropdownMenuButton1"
                        >
                          <div className="btn-icon rounded-pill user-icons">
                            <span className="btn-inner">
                              <svg
                                className="icon-18"
                                width="18"
                                viewBox="0 0 24 24"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                              >
                                <path
                                  fillRule="evenodd"
                                  clipRule="evenodd"
                                  d="M9.87651 15.2063C6.03251 15.2063 2.74951 15.7873 2.74951 18.1153C2.74951 20.4433 6.01251 21.0453 9.87651 21.0453C13.7215 21.0453 17.0035 20.4633 17.0035 18.1363C17.0035 15.8093 13.7415 15.2063 9.87651 15.2063Z"
                                  stroke="currentColor"
                                  strokeWidth="1.5"
                                  strokeLinecap="round"
                                  strokeLinejoin="round"
                                ></path>
                                <path
                                  fillRule="evenodd"
                                  clipRule="evenodd"
                                  d="M9.8766 11.886C12.3996 11.886 14.4446 9.841 14.4446 7.318C14.4446 4.795 12.3996 2.75 9.8766 2.75C7.3546 2.75 5.3096 4.795 5.3096 7.318C5.3006 9.832 7.3306 11.877 9.8456 11.886H9.8766Z"
                                  stroke="currentColor"
                                  strokeWidth="1.5"
                                  strokeLinecap="round"
                                  strokeLinejoin="round"
                                ></path>
                                <path
                                  d="M19.2036 8.66919V12.6792"
                                  stroke="currentColor"
                                  strokeWidth="1.5"
                                  strokeLinecap="round"
                                  strokeLinejoin="round"
                                ></path>
                                <path
                                  d="M21.2497 10.6741H17.1597"
                                  stroke="currentColor"
                                  strokeWidth="1.5"
                                  strokeLinecap="round"
                                  strokeLinejoin="round"
                                ></path>
                              </svg>
                            </span>
                          </div>
                        </Dropdown.Toggle>
                        <Dropdown.Menu
                          as="ul"
                          className="dropdown-menu-end dropdown-user border-0 p-0 m-0 rounded-0 bg-dark"
                        >
                          <li className="user-info d-flex align-items-center gap-3 mb-3">
                            {auth?.user?.profile_picture && (
                              <img
                                src={auth?.user?.profile_picture}
                                className="img-fluid"
                                alt=""
                                loading="lazy"
                                onError={(e) => {
                                  e.target.style.display = 'none';
                                }}
                              />
                            )}
                            <span className="font-size-14 fw-500 text-capitalize text-white">
                              {auth?.user?.name}
                            </span>
                          </li>
                          {auth.user.is_streamer === "yes" && (
                            <li>
                              <Link
                                href={route("channel", {
                                  user: auth?.user?.username,
                                })}
                                className="iq-sub-card d-flex align-items-center gap-3"
                              >
                                <BiSolidVideos />
                                <h6 className="mb-0 font-size-14 fw-normal">
                                  {__("My Channel")}
                                </h6>
                              </Link>
                            </li>
                          )}
                          {auth.user.is_streamer === "yes" && (
                            <li>
                              <Link
                                href={route("channel.settings")}
                                className="iq-sub-card d-flex align-items-center gap-3"
                              >
                                <BiCog />
                                <h6 className="mb-0 font-size-14 fw-normal">
                                  {__("Channel Settings")}
                                </h6>
                              </Link>
                            </li>
                          )}
                          <li>
                            <Link
                              href={route("notifications.inbox")}
                              className="iq-sub-card d-flex align-items-center gap-3"
                            >
                              <BiBell />
                              <h6 className="mb-0 font-size-14 fw-normal">
                                {__("Notifications")}
                                <span className="bg-red-100 text-red-500 text-xs font-medium ml-2 px-1.5 py-0.5 rounded-full dark:bg-red-500 dark:text-red-100">
                                  {__(":unreadNotificationsCount new", {
                                    unreadNotificationsCount:
                                      auth.unreadNotifications,
                                  })}
                                </span>
                              </h6>
                            </Link>
                          </li>
                          {auth.user.is_streamer === "no" && (
                            <li>
                              <Link
                                href={route("profile.myTokens")}
                                className="iq-sub-card d-flex align-items-center gap-3"
                              >
                                <MdGeneratingTokens className="text-gray-primary" />
                                <h6 className="mb-0 font-size-14 fw-normal">
                                  {__("My Tokens")}
                                </h6>
                              </Link>
                            </li>
                          )}
                          {auth.user.is_streamer === "yes" && (
                            <li>
                              <Link
                                href={route("membership.set-tiers")}
                                className="iq-sub-card d-flex align-items-center gap-3"
                              >
                                <BiCreditCard />
                                <h6 className="mb-0 font-size-14 fw-normal">
                                  {__("Membership Tiers")}
                                </h6>
                              </Link>
                            </li>
                          )}
                          {auth.user.is_streamer === "yes" && (
                            <li>
                              <Link
                                href={route("videos.list")}
                                className="iq-sub-card d-flex align-items-center gap-3"
                              >
                                <BiUpload />
                                <h6 className="mb-0 font-size-14 fw-normal">
                                  {__("Upload Videos")}
                                </h6>
                              </Link>
                            </li>
                          )}
                          <li>
                            <Link
                              href={route("videos.ordered")}
                              className="iq-sub-card d-flex align-items-center gap-3"
                            >
                              <BiVideo />
                              <h6 className="mb-0 font-size-14 fw-normal">
                                {__("My Videos")}
                              </h6>
                            </Link>
                          </li>
                          {auth.user.is_streamer === "yes" && (
                            <li>
                              <Link
                                href={route("gallery.list")}
                                className="iq-sub-card d-flex align-items-center gap-3"
                              >
                                <BiImageAdd />
                                <h6 className="mb-0 font-size-14 fw-normal">
                                  {__("Upload Gallery")}
                                </h6>
                              </Link>
                            </li>
                          )}
                          <li>
                            <Link
                              href={route("gallery.ordered")}
                              className="iq-sub-card d-flex align-items-center gap-3"
                            >
                              <BiImage />
                              <h6 className="mb-0 font-size-14 fw-normal">
                                {__("My Gallery")}
                              </h6>
                            </Link>
                          </li>
                          {auth.user.is_streamer === "yes" && (
                            <li>
                              <Link
                                href={route("channel.followers", {
                                  user: auth?.user?.username,
                                })}
                                className="iq-sub-card d-flex align-items-center gap-3"
                              >
                                <BiUser />
                                <h6 className="mb-0 font-size-14 fw-normal">
                                  {__("My Followers")}
                                </h6>
                              </Link>
                            </li>
                          )}
                          {auth.user.is_streamer === "yes" && (
                            <li>
                              <Link
                                href={route("mySubscribers")}
                                className="iq-sub-card d-flex align-items-center gap-3"
                              >
                                <BiUserPlus />
                                <h6 className="mb-0 font-size-14 fw-normal">
                                  {__("My Subscribers")}
                                </h6>
                              </Link>
                            </li>
                          )}
                          {auth.user.is_streamer === "yes" && (
                            <li>
                              <Link
                                href={route("getStreamingList")}
                                className="iq-sub-card d-flex align-items-center gap-3"
                              >
                                <BiPlus />
                                <h6 className="mb-0 font-size-14 fw-normal">
                                  {__("Add Streaming")}
                                </h6>
                              </Link>
                            </li>
                          )}
                          <li>
                            <Link
                              href={route("profile.followings")}
                              className="iq-sub-card d-flex align-items-center gap-3"
                            >
                              <BiUserPlus />
                              <h6 className="mb-0 font-size-14 fw-normal">
                                {__("My Followings")}
                              </h6>
                            </Link>
                          </li>
                          <li>
                            <Link
                              href={route("mySubscriptions")}
                              className="iq-sub-card d-flex align-items-center gap-3"
                            >
                              <BiUserPlus />
                              <h6 className="mb-0 font-size-14 fw-normal">
                                {__("My Subscriptions")}
                              </h6>
                            </Link>
                          </li>
                          <li>
                            <Link
                              href={route("profile.edit")}
                              className="iq-sub-card d-flex align-items-center gap-3"
                            >
                              <BiUser />
                              <h6 className="mb-0 font-size-14 fw-normal">
                                {__("My Account")}
                              </h6>
                            </Link>
                          </li>
                          <li>
                            <Link
                              href={route("logout")}
                              method="post"
                              as="button"
                              className="iq-sub-card iq-logout-2 mt-1 d-flex justify-content-center gap-2 w-full"
                            >
                              <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="16"
                                height="16"
                                viewBox="0 0 16 16"
                                fill="none"
                              >
                                <path
                                  d="M1.82209 15.9999C1.46654 15.9999 1.16283 15.874 0.910981 15.6221C0.659129 15.3703 0.533203 15.0666 0.533203 14.711V1.73322C0.533203 1.37767 0.659129 1.07397 0.910981 0.822114C1.16283 0.570262 1.46654 0.444336 1.82209 0.444336H7.95543V1.44434H1.82209C1.74802 1.44434 1.68135 1.47397 1.62209 1.53322C1.56283 1.59248 1.5332 1.65915 1.5332 1.73322V14.711C1.5332 14.7851 1.56283 14.8517 1.62209 14.911C1.68135 14.9703 1.74802 14.9999 1.82209 14.9999H7.95543V15.9999H1.82209ZM12.0888 11.5999L11.3554 10.8888L13.5332 8.73322H5.68876V7.711H13.511L11.3332 5.55545L12.0665 4.82211L15.4665 8.24434L12.0888 11.5999Z"
                                  fill="currentColor"
                                ></path>
                              </svg>
                              <h6 className="mb-0 font-size-14 fw-normal">
                                Logout
                              </h6>
                            </Link>
                          </li>
                        </Dropdown.Menu>
                      </Dropdown>
                    </ul>
                  )}
                </div>
              </div>
            </div>
          </Container>
        </Navbar>
      </header>
    </Fragment>
  );
});

HeaderDefault.displayName = "HeaderDefault";
export default HeaderDefault;
