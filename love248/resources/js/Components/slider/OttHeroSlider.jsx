import React, { memo, Fragment, useState, useEffect } from "react";
import logo from "../../..//assets/images/movies/imdb-logo.svg";
import ott1 from "../../..//assets/images/movies/ott1.webp";
import ott2 from "../../..//assets/images/movies/ott2.webp";
import ott3 from "../../..//assets/images/movies/ott3.webp";
import { Swiper, SwiperSlide } from "swiper/react";
import { Link } from "@inertiajs/inertia-react";
import { Navigation, Thumbs } from "swiper";
import CustomButton from "./CustomButton";
import { useSelector } from "react-redux";

const OttHeroSlider = memo(() => {
  const themeSchemeDirection = useSelector(state => state.SwiperReducer);
  const [thumbsSwiper, setThumbsSwiper] = useState(null);
  const [render, setRender] = useState(true)
  useEffect(() => {
    setThumbsSwiper(null)
    setRender(false)
    setTimeout(() => {
      setRender(true)
    }, 100);
    return () => { };
  }, [themeSchemeDirection]);

  return (
    <Fragment>
      <div className="iq-banner-thumb-slider">
        <div className="slider">
          <div className="position-relative slider-bg d-flex justify-content-end">
            <div className="position-relative my-auto">
              <div
                className="horizontal_thumb_slider"
                data-swiper="slider-thumbs-ott"
              >
                <div className="banner-thumb-slider-nav">
                  <Swiper
                    key={themeSchemeDirection}
                    dir={themeSchemeDirection}
                    tag="ul"
                    thumbs={{
                      swiper:
                        thumbsSwiper && !thumbsSwiper.destroyed
                          ? thumbsSwiper
                          : null,
                    }}
                    direction="horizontal"
                    navigation={{
                      prevEl: ".slider-prev",
                      nextEl: ".slider-next",
                    }}
                    spaceBetween={24}
                    loop={true}
                    slidesPerView={2}
                    breakpoints={{
                      0: {
                        direction: "horizontal",
                        slidesPerView: 1,
                      },
                      768: {
                        direction: "horizontal",
                        slidesPerView: 2,
                      },
                    }}
                    watchSlidesProgress={true}
                    modules={[Navigation, Thumbs]}
                    className="swiper-horizontal swiper-container mb-0"
                  >
                    <SwiperSlide className="swiper-bg">
                      <div className="block-images position-relative">
                        <div className="img-box">
                          <img
                            src={ott1}
                            className="img-fluid"
                            alt=""
                            loading="lazy"
                          />
                          <div className="block-description ps-3">
                            <h6 className="iq-title fw-500 mb-0 text-white">The Hunter</h6>
                            <span className="fs-12 text-white">2 hr 6 minute</span>
                          </div>
                        </div>
                      </div>
                    </SwiperSlide>
                    <SwiperSlide className="swiper-bg">
                      <div className="block-images position-relative">
                        <div className="img-box">
                          <img
                            src={ott2}
                            className="img-fluid"
                            alt=""
                            loading="lazy"
                          />
                          <div className="block-description ps-3">
                            <h6 className="iq-title fw-500 mb-0 text-white">Live Streaming</h6>
                            <span className="fs-12 text-white">2 hr 6 minute</span>
                          </div>
                        </div>
                      </div>
                    </SwiperSlide>
                    <SwiperSlide className="swiper-bg">
                      <div className="block-images position-relative">
                        <div className="img-box">
                          <img
                            src={ott3}
                            className="img-fluid"
                            alt=""
                            loading="lazy"
                          />
                          <div className="block-description ps-3">
                            <h6 className="iq-title fw-500 mb-0 text-white">Subscription Tiers</h6>
                            <span className="fs-12 text-white">2 hr 6 minute</span>
                          </div>
                        </div>
                      </div>
                    </SwiperSlide>
                  </Swiper>
                  <div className="slider-prev swiper-button">
                    <i className="iconly-Arrow-Left-2 icli"></i>
                  </div>
                  <div className="slider-next swiper-button">
                    <i className="iconly-Arrow-Right-2 icli"></i>
                  </div>
                </div>
              </div>
            </div>
            <div className="slider-images" data-swiper="slider-images-ott">
              <Swiper
                key={themeSchemeDirection}
                dir={themeSchemeDirection}
                tag="ul"
                onSwiper={setThumbsSwiper}
                slidesPerView={1}
                modules={[Navigation, Thumbs]}
                watchSlidesProgress={true}
                allowTouchMove={false}
                loop={true}
                className="swiper-container"
              >
                <SwiperSlide className="p-0">
                  <div className="slider--image block-images">
                    <img src={ott1} loading="lazy" alt="banner" />
                  </div>
                  <div className="description">
                    <div className="row align-items-center h-100">
                      <div className="col-lg-6 col-md-12">
                        <div className="slider-content">
                          <div className="d-flex align-items-center RightAnimate mb-3">
                            <span className="badge rounded-0 text-dark text-uppercase px-3 py-2 me-3 bg-white mr-3">
                              Pg
                            </span>
                            <ul className="p-0 mb-0 list-inline d-flex flex-wrap align-items-center movie-tag">
                              <li className="position-relative text-capitalize font-size-14 letter-spacing-1">
                                <Link
                                  to="/view-all"
                                  className="text-decoration-none text-white"
                                >
                                  Adventure
                                </Link>
                              </li>
                              <li className="position-relative text-capitalize font-size-14 letter-spacing-1">
                                <Link
                                  to="/view-all"
                                  className="text-decoration-none text-white"
                                >
                                  Thriller
                                </Link>
                              </li>
                              <li className="position-relative text-capitalize font-size-14 letter-spacing-1">
                                <Link
                                  to="/view-all"
                                  className="text-decoration-none text-white"
                                >
                                  Drama
                                </Link>
                              </li>
                            </ul>
                          </div>
                          <h1 className="texture-text big-font letter-spacing-1 line-count-1 text-capitalize RightAnimate-two text-white">
                            Live Streaming at Your Fingertips
                          </h1>
                          <p className="line-count-3 RightAnimate-two text-white">
                            Stream & watch live video streams directly from your browser.
                          </p>
                          <div className="d-flex flex-wrap align-items-center gap-3 RightAnimate-three">
                            <div className="slider-ratting d-flex align-items-center">
                              <ul className="ratting-start p-0 m-0 list-inline text-warning d-flex align-items-center justify-content-left">
                                <li>
                                  <i
                                    className="fa fa-star"
                                    aria-hidden="true"
                                  ></i>
                                </li>
                              </ul>
                              <span className="text-white ms-2 font-size-14 fw-500">
                                4.3/5
                              </span>
                              <span className="ms-2">
                                <img
                                  src={logo}
                                  alt="imdb logo"
                                  className="img-fluid"
                                />
                              </span>
                            </div>
                            <span className="font-size-14 fw-500 text-white">
                              2 Hr : 6 Mins
                            </span>
                            <div className="text-primary font-size-14 fw-500 text-capitalize ">
                              genres:{" "}
                              <Link
                                to="/view-all"
                                className="text-decoration-none ms-1 text-white"
                              >
                                Drama
                              </Link>
                            </div>
                            <div className="text-primary font-size-14 fw-500 text-capitalize">
                              Starting:{" "}
                              <Link
                                to="/cast-detail"
                                className="text-decoration-none ms-1 text-white"
                              >
                                Jeffrey Silver
                              </Link>
                            </div>
                          </div>
                        </div>
                        <CustomButton
                          buttonTitle="play now"
                          link="/movies-detail"
                          linkButton="false"
                        />
                      </div>
                    </div>
                  </div>
                </SwiperSlide>
                <SwiperSlide className="p-0">
                  <div className="slider--image block-images">
                    <img src={ott2} loading="lazy" alt="banner" />
                  </div>
                  <div className="description">
                    <div className="row align-items-center h-100">
                      <div className="col-lg-6 col-md-12">
                        <div className="slider-content">
                          <div className="d-flex align-items-center RightAnimate mb-3">
                            <span className="badge rounded-0 text-dark text-uppercase px-3 py-2 me-3 bg-white mr-3">
                              NC-17
                            </span>
                            <ul className="p-0 mb-0 list-inline d-flex flex-wrap align-items-center movie-tag">
                              <li className="position-relative text-capitalize font-size-14 letter-spacing-1">
                                <Link
                                  to="/view-all"
                                  className="text-decoration-none text-white"
                                >
                                  Animation
                                </Link>
                              </li>
                              <li className="position-relative text-capitalize font-size-14 letter-spacing-1">
                                <Link
                                  to="/view-all"
                                  className="text-decoration-none text-white"
                                >
                                  Sci-Fi
                                </Link>
                              </li>
                              <li className="position-relative text-capitalize font-size-14 letter-spacing-1">
                                <Link
                                  to="/view-all"
                                  className="text-decoration-none text-white"
                                >
                                  Action
                                </Link>
                              </li>
                            </ul>
                          </div>
                          <h1 className="texture-text big-font letter-spacing-1 line-count-1 text-capitalize RightAnimate-two text-white">
                          Live Streaming
                          </h1>
                          <p className="line-count-3 RightAnimate-two text-white">
                          Stream directly from your browser. No additional complicated software to install. All you need is your computer & a camera.
                          </p>
                          <div className="d-flex flex-wrap align-items-center gap-3 RightAnimate-three">
                            <div className="slider-ratting d-flex align-items-center">
                              <ul className="ratting-start p-0 m-0 list-inline text-warning d-flex align-items-center justify-content-left">
                                <li>
                                  <i
                                    className="fa fa-star"
                                    aria-hidden="true"
                                  ></i>
                                </li>
                              </ul>
                              <span className="text-white ms-2 font-size-14 fw-500">
                                4.3/5
                              </span>
                            </div>
                            <span className="font-size-14 fw-500 text-white">
                              2 Hr : 14 Mins
                            </span>
                            <div className="text-primary font-size-14 fw-500 text-capitalize">
                              genres:{" "}
                              <Link
                                to="/view-all"
                                className="text-decoration-none ms-1 text-white"
                              >
                                Sci-Fi
                              </Link>
                            </div>
                            <div className="text-primary font-size-14 fw-500 text-capitalize">
                              Starting:{" "}
                              <Link
                                to="/cast-detail"
                                className="text-decoration-none ms-1 text-white"
                              >
                                James Chinlund
                              </Link>
                            </div>
                          </div>
                        </div>
                        <CustomButton
                          buttonTitle="play now"
                          link="/movies-detail"
                          linkButton="false"
                        />
                      </div>
                    </div>
                  </div>
                </SwiperSlide>
                <SwiperSlide className="p-0">
                  <div className="slider--image block-images">
                    <img src={ott3} loading="lazy" alt="banner" />
                  </div>
                  <div className="description">
                    <div className="row align-items-center h-100">
                      <div className="col-lg-6 col-md-12">
                        <div className="slider-content">
                          <div className="d-flex align-items-center RightAnimate mb-3">
                            <span className="badge rounded-0 text-dark text-uppercase px-3 py-2 me-3 bg-white mr-3">
                              G
                            </span>
                            <ul className="p-0 mb-0 list-inline d-flex flex-wrap align-items-center movie-tag">
                              <li className="position-relative text-capitalize font-size-14 letter-spacing-1">
                                <Link
                                  to="/view-all"
                                  className="text-decoration-none text-white"
                                >
                                  History
                                </Link>
                              </li>
                              <li className="position-relative text-capitalize font-size-14 letter-spacing-1">
                                <Link
                                  to="/view-all"
                                  className="text-decoration-none text-white"
                                >
                                  Action
                                </Link>
                              </li>
                            </ul>
                          </div>
                          <h1 className="texture-text big-font letter-spacing-1 line-count-1 text-capitalize RightAnimate-two text-white">
                          Subscription Tiers
                          </h1>
                          <p className="line-count-3 RightAnimate-two text-white">
                          Get recurring revenue from your fan base via membership tiers. You can offer 1, 6 and 12 months with discounts option.
                          </p>
                          <div className="d-flex flex-wrap align-items-center gap-3 RightAnimate-three">
                            <div className="slider-ratting d-flex align-items-center">
                              <ul className="ratting-start p-0 m-0 list-inline text-warning d-flex align-items-center justify-content-left">
                                <li>
                                  <i
                                    className="fa fa-star"
                                    aria-hidden="true"
                                  ></i>
                                </li>
                              </ul>
                            </div>
                            <span className="font-size-14 fw-500 text-white">
                              2 Hr : 55 Mins
                            </span>
                            <div className="text-primary font-size-14 fw-500 text-capitalize">
                              genres:{" "}
                              <Link
                                to="/view-all"
                                className="text-decoration-none ms-1 text-white"
                              >
                                horror
                              </Link>
                            </div>
                            <div className="text-primary font-size-14 fw-500 text-capitalize">
                              Starting:{" "}
                              <Link
                                to="/cast-detail"
                                className="text-decoration-none ms-1 text-white"
                              >
                                Brenda Chapman
                              </Link>
                              <span className="text-body">,</span>
                              <Link
                                to="/cast-detail"
                                className="text-decoration-none ms-1 text-white"
                              >
                                Caleb Deschannelr
                              </Link>
                            </div>
                          </div>
                        </div>
                        <CustomButton
                          buttonTitle="play now"
                          link="/movies-detail"
                          linkButton="false"
                        />
                      </div>
                    </div>
                  </div>
                </SwiperSlide>
              </Swiper>
            </div>
          </div>
        </div>
      </div>
    </Fragment>
  );
});

OttHeroSlider.displayName = OttHeroSlider;
export default OttHeroSlider;
