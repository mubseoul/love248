import playstore from "../../../assets/images/footer/google-play.webp";
import { Link, setAnimationClass, usePage } from "@inertiajs/inertia-react";
import apple from "../../../assets/images/footer/apple.webp";
import { memo, Fragment, useState, useEffect } from "react";
import { Container, Row, Col } from "react-bootstrap";
import __ from "@/Functions/Translate";
import Logo from "./Logo";

const FooterMega = memo(() => {
  const { pages } = usePage().props;
  const [animationClass, setAnimationClass] = useState("animate__fadeIn");

  const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: "smooth" });
  };

  const handleScroll = () => {
    if (document.documentElement.scrollTop > 250) {
      setAnimationClass("animate__fadeIn");
    } else {
      setAnimationClass("animate__fadeOut");
    }
  };

  useEffect(() => {
    handleScroll();
    window.addEventListener("scroll", handleScroll);

    return () => {
      window.removeEventListener("scroll", handleScroll);
    };
  }, []);

  useEffect(() => {
    scrollToTop();
  }, []);
  return (
    <>
      <Fragment>
        <footer
          className="footer footer-default"
          style={{ background: "#141314" }}
        >
          <Container fluid>
            <div className="footer-top">
              <Row>
                <Col xl={3} lg={6} className="mb-5 mb-lg-0">
                  <div className="footer-logo">
                    <Logo></Logo>
                  </div>
                  <p className="mb-4 font-size-14 text-white">
                    Email us:{" "}
                    <span className="text-white">customer@streamit.com</span>
                  </p>
                  <p className="text-uppercase letter-spacing-1 font-size-14 mb-1 text-white">
                    customer services
                  </p>
                  <p className="mb-0 contact text-white">+ (480) 555-0103</p>
                </Col>
                <Col xl={3} lg={6} className="mb-5 mb-lg-0">
                  <h4 className="footer-link-title text-white">Quick Links</h4>
                  <ul className="list-unstyled footer-menu">
                    <li className="mb-3">
                      <Link href={route("home")} className="ms-3">
                        Home
                      </Link>
                    </li>
                    <li className="mb-3">
                      <Link href={route("channels.browse")} className="ms-3">
                        Channels
                      </Link>
                    </li>
                    <li className="mb-3">
                      <Link href={route("videos.browse")} className="ms-3">
                        Videos
                      </Link>
                    </li>
                    <li>
                      <Link href={route("gallery.browse")} className="ms-3">
                        Gallery
                      </Link>
                    </li>
                  </ul>
                </Col>

                <Col xl={3} lg={6} className="mb-5 mb-lg-0">
                  <h4 className="footer-link-title text-white">
                    About company
                  </h4>
                  <ul className="list-unstyled footer-menu">
                    {pages.map((p) => (
                      <li key={`page-${p.id}`} className="mb-3">
                        <Link
                          className="ms-3"
                          href={route("page", { page: p.page_slug })}
                        >
                          {" "}
                          {p.page_title}{" "}
                        </Link>
                      </li>
                    ))}
                  </ul>
                </Col>
                <Col xl={3} lg={6}>
                  <h4 className="footer-link-title text-white">
                    Subscribe Newsletter
                  </h4>
                  <div className="mailchimp mailchimp-dark">
                    <div className="input-group mb-3 mt-4">
                      <input
                        type="text"
                        className="form-control mb-0 font-size-14"
                        placeholder="Email*"
                        aria-describedby="button-addon2"
                      />
                      <div className="iq-button">
                        <button
                          type="submit"
                          className="btn btn-sm"
                          id="button-addon2"
                        >
                          Subscribe
                        </button>
                      </div>
                    </div>
                  </div>
                  <div className="d-flex align-items-center mt-5">
                    <span className="font-size-14 me-2 text-white">
                      Follow Us:
                    </span>
                    <ul className="p-0 m-0 list-unstyled widget_social_media">
                      <li className="">
                        <Link
                          to="https://www.facebook.com/"
                          className="position-relative"
                        >
                          <i className="fab fa-facebook"></i>
                        </Link>
                      </li>
                      <li className="">
                        <Link
                          to="https://twitter.com/"
                          className="position-relative"
                        >
                          <i className="fab fa-twitter"></i>
                        </Link>
                      </li>
                      <li className="">
                        <Link
                          to="https://github.com/"
                          className="position-relative"
                        >
                          <i className="fab fa-github"></i>
                        </Link>
                      </li>
                      <li className="">
                        <Link
                          to="https://www.instagram.com/"
                          className="position-relative"
                        >
                          <i className="fab fa-instagram"></i>
                        </Link>
                      </li>
                    </ul>
                  </div>
                </Col>
              </Row>
            </div>
            <div className="footer-bottom border-top">
              <Row className="align-items-center">
                <Col md={10} className="m-auto">
                  <ul className="menu list-inline p-0 d-flex flex-wrap align-items-center justify-content-center">
                    {pages.map((p) => (
                      <li key={`page-${p.id}`} className="menu-item">
                        <Link href={route("page", { page: p.page_slug })}>
                          {" "}
                          {p.page_title}{" "}
                        </Link>
                      </li>
                    ))}
                    <li className="menu-item">
                      <Link href={route("contact.form")}>
                        {" "}
                        {__("Get in Touch")}{" "}
                      </Link>
                    </li>
                  </ul>
                  <p className="font-size-14 text-white text-center">
                    ©{" "}
                    <span className="currentYear">
                      {new Date().getFullYear()}
                    </span>{" "}
                    <span className="text-primary">STREAMIT</span>. All Rights
                    Reserved. All videos and shows on this platform are
                    trademarks of, and all related images and content are the
                    property of, Streamit Inc. Duplication and copy of this is
                    strictly prohibited. All rights reserved.
                  </p>
                </Col>
                {/* <Col md={3}></Col> */}
                {/* <Col md={3}>
                                    <h6 className="font-size-14 pb-1 text-white">Download Streamit Apps</h6>
                                    <div className="d-flex align-items-center">
                                        <Link className="app-image" to="#">
                                            <img src={playstore} loading="lazy" alt="play-store" />
                                        </Link>
                                        <br />
                                        <Link className="ms-3 app-image" to="#">
                                            <img src={apple} loading="lazy" alt="app-store" />
                                        </Link>
                                    </div>
                                </Col> */}
              </Row>
            </div>
          </Container>
        </footer>
        <div
          id="back-to-top"
          style={{ display: "none" }}
          className={`animate__animated ${animationClass}`}
          onClick={scrollToTop}
        >
          <Link
            className="p-0 btn bg-primary btn-sm position-fixed top border-0 rounded-circle"
            id="top"
            to="#top"
          >
            <i className="fa-solid fa-chevron-up"></i>
          </Link>
        </div>
      </Fragment>
    </>
  );
});
FooterMega.displayName = "FooterMega";
export default FooterMega;
