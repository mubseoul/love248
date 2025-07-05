import { Swiper, SwiperSlide } from "swiper/react";
import { Link } from "@inertiajs/inertia-react";
import { Autoplay, Navigation } from "swiper";
import { Container } from "react-bootstrap";
import { useSelector } from "react-redux";
import { memo, useRef } from "react";
const modules = [Autoplay, Navigation];

function generateUUID() {
    return "xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(/[xy]/g, function (c) {
        var r = (Math.random() * 16) | 0,
            v = c == "x" ? r : (r & 0x3) | 0x8;
        return v.toString(16);
    });
}

const SectionSlider = memo(
    ({
        children,
        title,
        list,
        slidesPerView = 6,
        loop = false,
        spaceBetween = 0,
        className = "",
        link,
    }) => {
        const themeSchemeDirection = useSelector(state => state.SwiperReducer);

        const slider = useRef(null);

        const initSwiper = (swiper) => {
            addCustomClassToVisibleSlides(swiper);
        };

        const addCustomClassToVisibleSlides = (swiper) => {
            if (slider.current) {
                if (swiper) {
                    slider.current
                        .querySelectorAll(".swiper-slide")
                        .forEach((separateSlide) => separateSlide.classList.remove("last"));

                    const swiperSlide = slider.current.querySelectorAll(
                        ".swiper-slide-visible"
                    );

                    const lastVisibleSlide = swiperSlide[swiperSlide.length - 1];

                    setTimeout(() => {
                        if (lastVisibleSlide) {
                            lastVisibleSlide.classList.add("swiper-active", "last");
                        }
                    }, 0);
                }
            }
        };

        return (
            <div className={className}>
                <Container fluid>
                    <div className="overflow-hidden card-style-slider" ref={slider}>
                        <div className="d-flex align-items-center justify-content-between px-3 my-4">
                            <h5 className="main-title text-capitalize mb-0">{title}</h5>
                            <Link
                                to={link ? link : "/view-all"}
                                className="text-primary iq-view-all text-decoration-none"
                            >
                                View All
                            </Link>
                        </div>
                        <Swiper
                            key={themeSchemeDirection}
                            dir={themeSchemeDirection}
                            className="position-relative swiper swiper-card"
                            slidesPerView={slidesPerView}
                            loop={loop}
                            watchSlidesProgress
                            spaceBetween={spaceBetween}
                            navigation={{
                                prevEl: ".swiper-button-prev",
                                nextEl: ".swiper-button-next",
                            }}
                            breakpoints={{
                                0: {
                                    slidesPerView: 2,
                                    spaceBetween: 0,
                                },
                                576: {
                                    slidesPerView: 2,
                                    spaceBetween: 0,
                                },
                                768: {
                                    slidesPerView: 3,
                                    spaceBetween: 0,
                                },
                                1025: {
                                    slidesPerView: slidesPerView,
                                    spaceBetween: 0,
                                },
                                1500: {
                                    slidesPerView: slidesPerView,
                                    spaceBetween: 0,
                                },
                            }}
                            onSwiper={initSwiper}
                            onSlideChange={initSwiper}
                            modules={modules}
                        >
                            {list.map((data, index) => (
                                <SwiperSlide tag="li" key={index + generateUUID() + "slider"}>
                                    {children(data)}
                                </SwiperSlide>
                            ))}
                            <div className="swiper-button swiper-button-next"></div>
                            <div className="swiper-button swiper-button-prev"></div>
                        </Swiper>
                    </div>
                </Container>
            </div>
        );
    }
);

SectionSlider.displayName = "SectionSlider";

export default SectionSlider;
