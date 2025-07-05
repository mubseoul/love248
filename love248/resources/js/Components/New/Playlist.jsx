import { Col, Container, Row } from "react-bootstrap";
import DefaultImg from "../../../assets/images/default.png";
import React, { Fragment, memo } from "react";
import { FaGrinStars } from "react-icons/fa";
import { Link } from "@inertiajs/inertia-react";
import { MdVideoLibrary } from "react-icons/md";
import { BsTagFill } from "react-icons/bs";

const Playlist = memo(({ channels }) => {
  return (
    <Fragment>
      <div className="section-padding-bottom">
        <Container fluid>
          <div className="overflow-hidden animated fadeInUp">
            <div className="d-flex align-items-center justify-content-between my-4">
              <h5 className="main-title text-capitalize mb-0 text-white">
                Discover Channels
              </h5>
            </div>
            <Row className="row-cols-1 row-cols-md-2 row-cols-lg-3">
              {channels.map((item, index) => {
                return (
                  <Col className="mb-4" key={index}>
                    <div className="watchlist-warpper card-hover-style-two">
                      <div className="block-images position-relative w-100">
                        <div className="img-box">
                          <Link
                            href={route("channel", { user: item?.username })}
                            className="position-absolute top-0 bottom-0 start-0 end-0"
                          ></Link>
                          <img
                            src={item.cover_picture || DefaultImg}
                            alt="movie-card"
                            className="img-fluid object-cover w-100 d-block border-0"
                            onError={(e) => {
                              e.target.onerror = null;
                              e.target.src = DefaultImg;
                            }}
                          />
                        </div>
                        <div className="card-description">
                          <h5 className="text-capitalize fw-500">
                            <Link
                              href={route("channel", { user: item?.username })}
                              className="text-white"
                            >
                              {item.name}
                            </Link>
                          </h5>
                          <div className="d-flex align-items-center gap-2 flex-wrap">
                            {item.categories?.length > 0 && (
                              <div className="d-flex align-items-center gap-1 font-size-12 text-white">
                                <BsTagFill className="fa-solid fa-earth-americas text-primary" />
                                {item.categories.map((item) => {
                                  return (
                                    <span
                                      className="text-white fw-semibold text-capitalize"
                                      key={item?.id}
                                    >
                                      {item.category}
                                    </span>
                                  );
                                })}
                              </div>
                            )}
                            <div className="d-flex align-items-center gap-1 font-size-12">
                              <i className="fa-regular fa-eye text-primary"></i>
                              <span className="text-white fw-semibold text-capitalize">
                                {item.followers_count}{" "}
                                {item.followers_count === 1
                                  ? "follower"
                                  : "followers"}
                              </span>
                            </div>
                            <div className="d-flex align-items-center gap-1 font-size-12">
                              <FaGrinStars className="text-primary" />
                              <span className="text-white fw-semibold text-capitalize">
                                {item.subscribers_count}{" "}
                                {item.subscribers_count === 1
                                  ? "subscriber"
                                  : "subscribers"}
                              </span>
                            </div>
                            <div className="d-flex align-items-center gap-1 font-size-12">
                              <MdVideoLibrary className="text-primary" />
                              <span className="text-white fw-semibold text-capitalize">
                                {item.videos_count}{" "}
                                {item.videos_count === 1 ? "video" : "videos"}
                              </span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </Col>
                );
              })}
            </Row>
            <div className="text-center">
              <div className="iq-button">
                <Link
                  href={route("channels.browse")}
                  className="btn text-uppercase position-relativ"
                >
                  <span className="button-text">View All Channels</span>
                  <i className="fa-solid fa-play"></i>
                </Link>
              </div>
            </div>
          </div>
        </Container>
      </div>
    </Fragment>
  );
});

export default Playlist;
