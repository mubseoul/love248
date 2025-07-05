import DefaultImg from "../../../../assets/images/default.png";
import { MdGeneratingTokens } from "react-icons/md";
import { Link } from "@inertiajs/inertia-react";
import "react-tooltip/dist/react-tooltip.css";
import { AiOutlineEye } from "react-icons/ai";
import SingleVideo from "../SingleGallery";
import { BsTagFill } from "react-icons/bs";
import { Col, Row } from "react-bootstrap";
import { Tooltip } from "react-tooltip";
import __ from "@/Functions/Translate";
import Modal from "@/Components/Modal";
import { useState } from "react";
import { FaLock } from "react-icons/fa";

export default function GalleryLoop({ gallery }) {
  const [playVideo, setPlayVideo] = useState(false);
  const [modal, setModal] = useState(false);

  const playModal = (e, gallery) => {
    e.preventDefault();
    setPlayVideo(gallery);
    setModal(true);
  };

  return (
    <>
      <Modal show={modal} onClose={(e) => setModal(false)}>
        {playVideo && <SingleVideo gallery={playVideo} inModal={true} />}
      </Modal>
      <Row className="row-cols-1 row-cols-md-2 row-cols-lg-3">
        {gallery.map((v) => {
          return (
            <Col className="mb-4" key={`video-${v.id}`}>
              <div className="watchlist-warpper card-hover-style-two">
                <div className="block-images position-relative w-100">
                  <div className="img-box">
                    <img
                      style={{
                        cursor: "pointer",
                        filter: !v.canBePlayed ? "blur(8px)" : "none",
                      }}
                      onClick={(e) => playModal(e, v)}
                      src={v.thumbnail || DefaultImg}
                      onError={(e) => {
                        e.target.onerror = null;
                        e.target.src = DefaultImg;
                      }}
                      alt={v.title || "Gallery image"}
                      className="img-fluid object-cover w-100 d-block border-0"
                    />
                    {!v.canBePlayed && (
                      <div className="position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center">
                        <div className="bg-dark bg-opacity-50 p-3 rounded-circle">
                          <FaLock className="text-white fs-4" />
                        </div>
                      </div>
                    )}
                  </div>
                  <div className="card-description">
                    <h5 className="text-capitalize fw-500">
                      <Link
                        onClick={(e) => playModal(e, v)}
                        className="text-white"
                      >
                        {v.title}
                      </Link>
                    </h5>
                    <div className="d-flex align-items-center gap-2 flex-wrap">
                      {v.categories?.length > 0 && (
                        <div className="d-flex align-items-center gap-1 font-size-12 text-white">
                          <BsTagFill className="fa-solid fa-earth-americas text-primary" />
                          {v.categories.map((item) => {
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
                        <span className="text-primary">@</span>
                        <span className="text-white fw-semibold text-capitalize">
                          {v?.streamer?.username}
                        </span>
                      </div>
                      <div className="d-flex align-items-center gap-1 font-size-12">
                        <BsTagFill className="text-primary" />
                        <span className="text-white fw-semibold text-capitalize">
                          {v?.category?.category}
                        </span>
                      </div>
                      <div className="d-flex align-items-center gap-1 font-size-12">
                        <AiOutlineEye className="text-primary" />
                        <span className="text-white fw-semibold text-capitalize">
                          {v.views === 1
                            ? __("1 view")
                            : __(":viewsCount views", {
                                viewsCount: v.views,
                              })}
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
    </>
  );
}
