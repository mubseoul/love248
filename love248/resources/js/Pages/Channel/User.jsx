import { Container, Nav, Tab, Button } from "react-bootstrap";
import { FaHandSparkles, FaGrinStars } from "react-icons/fa";
import { usePage, Link } from "@inertiajs/inertia-react";
import SubscribePopup from "./Partials/SubscribePopup";
import ChannelVideos from "./Partials/ChannelVideos";
import { AiFillPlayCircle } from "react-icons/ai";
import { FaPlayCircle } from "react-icons/fa";
import ScheduleTab from "./Partials/ScheduleTab";
import { MdVideoLibrary } from "react-icons/md";
import { Head } from "@inertiajs/inertia-react";
import { Inertia } from "@inertiajs/inertia";
import Tiers from "./Partials/TiresTab";
import { toast } from "react-toastify";
import __ from "@/Functions/Translate";
import Front from "@/Layouts/Front";
import axios from "axios";
import DefaultImg from "../../../assets/images/default.png";

export default function StartStream({
  user,
  streamUser,
  userFollowsChannel,
  userIsSubscribed,
}) {
  const { auth } = usePage().props;

  const followUser = () => {
    if (!user) {
      toast.error(__("Please login to follow this channel"));
    } else {
      axios
        .get(route("follow", { user: streamUser.id }))
        .then((apiRes) => {
          console.log(
            Inertia.reload({
              only: ["userFollowsChannel", "streamUser"],
            })
          );
        })
        .catch((Error) => toast.error(Error.response?.data?.error));
    }
  };

  return (
    <Front>
      <Head
        title={__(":channelName's channel (:handle)", {
          channelName: streamUser.name,
          handle: `@${streamUser.username}`,
        })}
      >
        <meta property="og:title" content="The Rock" />
        <meta
          property="og:url"
          content="https://www.imdb.com/title/tt0117500/"
        />
        <meta
          property="og:image"
          content="https://ia.media-imdb.com/images/rock.jpg"
        />
      </Head>
      <div className="section-padding-bottom">
        <div className="profile-box pt-[60px]" style={{ background: "#000" }}>
          <Container fluid>
            <div className="d-flex flex-column flex-md-row align-items-center align-items-md-center justify-content-between gap-3 gap-md-2">
              <div className="d-flex flex-column flex-sm-row align-items-center gap-3 w-100 w-md-auto">
                <div className="account-logo d-flex align-items-center position-relative">
                  <img
                    src={streamUser.profile_picture}
                    alt="profile"
                    className="img-fluid object-cover rounded-3"
                    style={{ width: "80px", height: "80px", minWidth: "80px" }}
                    onError={(e) => {
                      e.target.onerror = null;
                      e.target.src = DefaultImg;
                    }}
                  />
                  <i className="fa-regular fa-pen-to-square"></i>
                </div>
                <div className="text-center text-sm-start">
                  <h6 className="font-size-18 text-capitalize text-white fw-500 mb-1">
                    {streamUser.name}
                  </h6>
                  <div className="d-flex flex-column flex-sm-row gap-1 gap-sm-2 align-items-center">
                    <span className="font-size-14 text-white fw-500">
                      {streamUser.email}
                    </span>
                    <span className="font-size-14 text-white fw-500">
                      @{streamUser.username}
                    </span>
                  </div>
                </div>
              </div>
              
              {/* Mobile-first modern button layout */}
              <div className="d-block d-md-none">
                <div className="mobile-action-buttons d-flex gap-3 pb-2">
                  {auth?.user?.username === streamUser?.username && (
                    <Link
                      href={route("channel.livestream", {
                        user: streamUser?.username,
                      })}
                      className="mobile-action-btn btn btn-primary d-flex flex-column align-items-center justify-content-center text-center text-decoration-none"
                    >
                      <AiFillPlayCircle size={24} className="mb-1" />
                      <span className="small fw-bold text-white">{__("Stream")}</span>
                    </Link>
                  )}
                  {auth?.user?.username !== streamUser?.username && (
                    <Link
                      className="mobile-action-btn btn btn-primary d-flex flex-column align-items-center justify-content-center text-center text-decoration-none"
                      href={route("channel.livestream", {
                        user: streamUser.username,
                      })}
                    >
                      <FaPlayCircle size={24} className="mb-1" />
                      <span className="small fw-bold text-white">{__("Watch")}</span>
                    </Link>
                  )}
                  <button
                    className="mobile-action-btn btn btn-outline-light d-flex flex-column align-items-center justify-content-center text-center"
                    onClick={(e) => followUser()}
                  >
                    <FaHandSparkles size={22} className="mb-1" />
                    <span className="small fw-bold">
                      {userFollowsChannel ? __("Unfollow") : __("Follow")}
                    </span>
                  </button>
                  <SubscribePopup
                    user={streamUser}
                    userIsSubscribed={userIsSubscribed}
                  />
                </div>
              </div>

              {/* Desktop layout (unchanged) */}
              <div className="iq-button d-none d-md-flex flex-row gap-2">
                {auth?.user?.username === streamUser?.username && (
                  <div>
                  <Link
                    href={route("channel.livestream", {
                      user: streamUser?.username,
                    })}
                    className="me-2 btn text-uppercase position-relative d-flex align-items-center justify-content-center"
                  >
                    <AiFillPlayCircle className="me-2" />
                    <span className="button-text">{__("Start Streaming")}</span>
                  </Link>
                  </div>
                )}
                {auth?.user?.username !== streamUser?.username && (
                  <div>
                  <Link
                    className="me-2 btn text-uppercase position-relative d-flex align-items-center justify-content-center"
                    href={route("channel.livestream", {
                      user: streamUser.username,
                    })}
                  >
                    <FaPlayCircle className="me-2" />
                    <span className="button-text">{__("Request")}</span>
                  </Link>
                  </div>
                )}
                <div>
                <Button
                  className="me-2 btn text-uppercase position-relative d-flex align-items-center justify-content-center"
                  onClick={(e) => followUser()}
                >
                  <FaHandSparkles className="me-2" />
                  <span className="button-text">
                    {userFollowsChannel ? __("Unfollow") : __("Follow")}
                  </span>
                </Button>
                </div>
                <SubscribePopup
                  user={streamUser}
                  userIsSubscribed={userIsSubscribed}
                />
              </div>
            </div>
          </Container>
        </div>
        <Container fluid>
          <div className="content-details iq-custom-tab-style-two">
            <Tab.Container defaultActiveKey="first">
              <Nav className="d-flex justify-content-center nav nav-pills tab-header">
                <Nav className="mb-3 mb-md-5 d-flex flex-wrap justify-content-center" id="nav-tab" role="tablist">
                  <Nav.Link
                    className="text-white px-2 px-md-3 py-2 mx-1 mb-2"
                    eventKey="first"
                    variant=" d-flex align-items-center"
                    id="nav-playlist-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#nav-playlist"
                    type="button"
                    role="tab"
                    aria-controls="nav-playlist"
                    aria-selected="true"
                  >
                    Videos
                  </Nav.Link>
                  <Nav.Link
                    className="text-white px-2 px-md-3 py-2 mx-1 mb-2"
                    eventKey="second"
                    variant=""
                    id="nav-watchlist-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#nav-watchlist"
                    type="button"
                    role="tab"
                    aria-controls="nav-watchlist"
                    aria-selected="false"
                  >
                    Tiers
                  </Nav.Link>
                  <Nav.Link
                    className="text-white px-2 px-md-3 py-2 mx-1 mb-2"
                    eventKey="third"
                    variant=""
                    id="nav-favourite-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#nav-favourite"
                    type="button"
                    role="tab"
                    aria-controls="nav-favourite"
                    aria-selected="false"
                  >
                    Schedule
                  </Nav.Link>
                  <Nav.Link
                    className="text-white px-2 px-md-3 py-2 mx-1 mb-2"
                    eventKey="fourth"
                    variant=""
                    id="nav-favourite-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#nav-favourite"
                    type="button"
                    role="tab"
                    aria-controls="nav-favourite"
                    aria-selected="false"
                  >
                    About
                  </Nav.Link>
                </Nav>
              </Nav>
              <Tab.Content className="p-0">
                <Tab.Pane
                  className=" fade show"
                  eventKey="first"
                  id="nav-playlist"
                  role="tabpanel"
                  aria-labelledby="nav-playlist-tab"
                >
                  <div className="overflow-hidden animated fadeInUp">
                    <ChannelVideos user={user} streamUser={streamUser} />
                  </div>
                </Tab.Pane>
                <Tab.Pane
                  className="fade show"
                  eventKey="second"
                  id="nav-playlist"
                  role="tabpanel"
                  aria-labelledby="nav-playlist-tab"
                >
                  <Tiers user={streamUser} />
                </Tab.Pane>
                <Tab.Pane
                  className="fade show"
                  eventKey="third"
                  id="nav-playlist"
                  role="tabpanel"
                  aria-labelledby="nav-playlist-tab"
                >
                  <ScheduleTab user={streamUser} />
                </Tab.Pane>
                <Tab.Pane
                  className="fade show"
                  eventKey="fourth"
                  id="nav-playlist"
                  role="tabpanel"
                  aria-labelledby="nav-playlist-tab"
                >
                  <div className="mt-4">
                    <div className="row g-3 g-md-4">
                      <div className="col-12 col-sm-6 col-lg-4">
                        <div className="shadow bg-footer text-white p-3 p-md-4 rounded h-100">
                          <h3 className="text-xl text-center flex items-center justify-center text-gray-primary mb-2">
                            <FaHandSparkles className="w-8 h-8 me-2" />
                          </h3>
                          <p className="mt-2 font-medium text-center text-gray-primary mb-0">
                            {streamUser.followers_count === 1
                              ? __("1 Follower")
                              : __(":count Followers", {
                                  count: streamUser.followers_count,
                                })}
                          </p>
                        </div>
                      </div>
                      
                      <div className="col-12 col-sm-6 col-lg-4">
                        <div className="shadow bg-footer text-white p-3 p-md-4 rounded h-100">
                          <h3 className="text-xl text-center flex items-center justify-center text-gray-primary mb-2">
                            <FaGrinStars className="w-8 h-8 me-2" />
                          </h3>
                          <p className="mt-2 font-medium text-center text-gray-primary mb-0">
                            {streamUser.subscribers_count === 1
                              ? __("1 Subscriber")
                              : __(":count Subscribers", {
                                  count: streamUser.subscribers_count,
                                })}
                          </p>
                        </div>
                      </div>
                      
                      <div className="col-12 col-sm-6 col-lg-4">
                        <div className="shadow bg-footer text-white p-3 p-md-4 rounded h-100">
                          <h3 className="text-xl text-center flex items-center justify-center text-gray-primary mb-2">
                            <MdVideoLibrary className="w-8 h-8 me-2" />
                          </h3>
                          <p className="mt-2 font-medium text-center text-gray-primary mb-0">
                            {streamUser.videos_count === 1
                              ? __("1 Video")
                              : __(":count Videos", {
                                  count: streamUser.videos_count,
                                })}
                          </p>
                        </div>
                      </div>
                    </div>
                    
                    {streamUser?.about && (
                      <div className="mt-4">
                        <div className="bg-footer text-gray-primary rounded shadow p-3">
                          <h4 className="text-white mb-3">{__("About this channel")}</h4>
                          <div
                            dangerouslySetInnerHTML={{
                              __html: streamUser.about,
                            }}
                          />
                        </div>
                      </div>
                    )}
                  </div>
                </Tab.Pane>
              </Tab.Content>
            </Tab.Container>
          </div>
        </Container>
      </div>
    </Front>
  );
}
