import {
  Col,
  Container,
  Row,
  Button,
  Modal as BootstrapModal,
  Form,
} from "react-bootstrap";
import DefaultImg from "../../../assets/images/default.png";
import { MdVideoLibrary, MdGeneratingTokens } from "react-icons/md";
import React, { Fragment, memo, useState } from "react";
import { Link } from "@inertiajs/inertia-react";
import { BsTagFill } from "react-icons/bs";
import SingleVideo from "../../Pages/Videos/SingleVideo";
import Modal from "@/Components/Modal";

const Video = memo(({ videos }) => {
  const [show, setShow] = useState(false);
  const [playVideo, setPlayVideo] = useState(false);
  const [videoModal, setVideoModal] = useState(false);

  const handleClose = () => setShow(false);
  const handleShow = () => setShow(true);

  const playModal = (e, video) => {
    e.preventDefault();
    setPlayVideo(video);
    setVideoModal(true);
  };

  return (
    <Fragment>
      <div className="section-padding-bottom">
        <Container fluid>
          <div className="overflow-hidden animated fadeInUp">
            <div className="d-flex align-items-center justify-content-between my-4">
              <h5 className="main-title text-capitalize mb-0 text-white">
                Latest Videos
              </h5>
            </div>
            <Modal show={videoModal} onClose={(e) => setVideoModal(false)}>
              {playVideo && <SingleVideo video={playVideo} inModal={true} />}
            </Modal>
            <Row className="row-cols-1 row-cols-md-2 row-cols-lg-3">
              {videos.map((item, index) => {
                return (
                  <Col className="mb-4" key={index}>
                    <div className="watchlist-warpper card-hover-style-two">
                      <div className="block-images position-relative w-100">
                        <div className="img-box">
                          <img
                            style={{ cursor: "pointer" }}
                            onClick={(e) => playModal(e, item)}
                            src={item.thumbnailUrl || DefaultImg}
                            alt="movie-card"
                            className="img-fluid object-cover w-100 d-block border-0"
                          />
                        </div>
                        <div className="card-description">
                          <h5 className="text-capitalize fw-500">
                            <Link
                              onClick={(e) => playModal(e, item)}
                              className="text-white"
                            >
                              {item.title}
                            </Link>
                          </h5>
                          <div className="d-flex align-items-center gap-2 flex-wrap">
                            <div className="d-flex align-items-center gap-1 font-size-12">
                              <BsTagFill className="mr-0.5 text-primary" />
                              <span className="text-white fw-semibold text-capitalize">
                                {item.category?.category}
                              </span>
                            </div>
                            <div className="d-flex align-items-center gap-1 font-size-12">
                              <i className="fa-regular fa-eye text-primary"></i>
                              <span className="text-white fw-semibold text-capitalize">
                                {item.views}{" "}
                                {item.views === 1 ? "view" : "views"}
                              </span>
                            </div>
                            <div className="d-flex align-items-center gap-1 font-size-12">
                              <MdVideoLibrary className="text-primary" />
                              <span className="text-white fw-semibold text-capitalize">
                                {item.videos_count}{" "}
                                {item.videos_count === 1 ? "video" : "videos"}
                              </span>
                            </div>
                            <div className="d-flex align-items-center gap-1 font-size-12">
                              <MdGeneratingTokens className="text-primary" />
                              <span className="text-white fw-semibold text-capitalize">
                                {item.price}
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
                  href={route("videos.browse")}
                  className="btn text-uppercase position-relativ"
                >
                  <span className="button-text">View All videos</span>
                  <i className="fa-solid fa-play"></i>
                </Link>
              </div>
            </div>
          </div>
          <BootstrapModal
            show={show}
            onHide={handleClose}
            size="lg"
            contentClassName="border-0"
          >
            <BootstrapModal.Header closeButton className="border-0">
              <div>
                <BootstrapModal.Title
                  as="h1"
                  className="text-capitalize fs-5 fw-500"
                >
                  Create New Playlist
                </BootstrapModal.Title>
                <p className="mb-0">
                  Please fill in all information below to create new playlist.
                </p>
              </div>
            </BootstrapModal.Header>
            <BootstrapModal.Body>
              <Form>
                <Form.Group className="form-group">
                  <Form.Label className="text-white fw-500 mb-2">
                    Name *
                  </Form.Label>
                  <Form.Control type="text" />
                </Form.Group>
                <Form.Group className="form-group">
                  <Form.Label className="text-white fw-500 mb-2">
                    Description
                  </Form.Label>
                  <textarea className="form-control" cols="5"></textarea>
                </Form.Group>
                <Form.Group className="form-group">
                  <Form.Label className="text-white fw-500 mb-2">
                    Privacy
                  </Form.Label>
                  <Form.Select className="form-control">
                    <option>Public</option>
                    <option>Private</option>
                  </Form.Select>
                </Form.Group>
                <Form.Group className="form-group">
                  <Form.Label className="text-white fw-500">
                    Playlist Thumbnail
                  </Form.Label>
                  <small className="d-block my-2">
                    Support *.webp, *webp, *.gif, *.webp. Maximun upload file
                    size: 5mb.
                  </small>
                  <Form.Control type="file" />
                </Form.Group>
                <Form.Group className="form-group d-flex align-items-center gap-3">
                  <Button
                    className="btn btn-sm btn-light text-uppercase fw-medium"
                    onClick={handleClose}
                  >
                    cancel
                  </Button>
                  <Button
                    className="btn btn-sm btn-primary text-uppercase fw-medium"
                    onClick={handleClose}
                  >
                    save
                  </Button>
                </Form.Group>
              </Form>
            </BootstrapModal.Body>
          </BootstrapModal>
        </Container>
      </div>
    </Fragment>
  );
});

export default Video;
