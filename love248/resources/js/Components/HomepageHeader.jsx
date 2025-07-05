import React, { useState } from "react";
import { Swiper, SwiperSlide } from "swiper/react";
import {
  Autoplay,
  Navigation,
  Pagination,
  EffectCoverflow,
  EffectCards,
} from "swiper";
import { Link } from "@inertiajs/inertia-react";
import __ from "@/Functions/Translate";
import { AiOutlineEye } from "react-icons/ai";
import { IoIosArrowBack, IoIosArrowForward } from "react-icons/io";

export default function HomepageHeader({ videos = [] }) {
  // Take only the first 5 videos for the slider or fewer if there are less than 5
  const sliderVideos = videos?.length > 0 ? videos.slice(0, 5) : [];
  return (
    <div className="relative homepage-header-slider mb-8">
      {sliderVideos.length > 0 ? (
        <Swiper
          modules={[
            Autoplay,
            Navigation,
            Pagination,
            EffectCoverflow,
            EffectCards,
          ]}
          spaceBetween={0}
          slidesPerView={1}
          centeredSlides={true}
          loop={true}
          effect="coverflow"
          coverflowEffect={{
            rotate: 15,
            stretch: 0,
            depth: 200,
            modifier: 1.5,
            slideShadows: true,
          }}
          autoplay={{
            delay: 5000,
            disableOnInteraction: false,
          }}
          pagination={{
            clickable: true,
          }}
          navigation={{
            nextEl: ".custom-next-button",
            prevEl: ".custom-prev-button",
          }}
          className="w-full py-12 px-4"
        >
          {sliderVideos.map((video, index) => (
            <SwiperSlide key={`slider-${index}`} className="swiper-slide-3d">
              <div className="relative w-full h-[400px] rounded-lg overflow-hidden shadow-xl transition-all duration-300 transform-gpu perspective-1000">
                <div className="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent z-10"></div>
                <img
                  src={video.thumbnail || video.streamer?.cover_picture}
                  alt={video.title}
                  className="w-full h-full object-cover"
                />

                <div className="absolute bottom-0 left-0 p-4 z-20 text-white w-full">
                  <h2 className="text-xl font-bold mb-2">{video.title}</h2>
                  <p className="text-sm mb-3 line-clamp-2">
                    {video.description}
                  </p>
                  <div className="flex items-center gap-3">
                    {video.categories?.length > 0 && (
                      <div className="flex items-center gap-1 text-xs text-white">
                        <span className="text-white font-semibold">
                          Categories:
                        </span>
                        {video.categories.map((categoryItem, i) => (
                          <span
                            className="text-white font-semibold text-capitalize"
                            key={categoryItem?.id || i}
                          >
                            {categoryItem.category}
                            {i < video.categories.length - 1 ? ", " : ""}
                          </span>
                        ))}
                      </div>
                    )}
                    <div className="flex items-center gap-1 text-xs">
                      <AiOutlineEye className="text-primary" />
                      <span className="text-white font-semibold">
                        {video.views === 1
                          ? __("1 view")
                          : __(":viewsCount views", {
                              viewsCount: video.views || 0,
                            })}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </SwiperSlide>
          ))}

          <div className="custom-next-button absolute right-5 top-1/2 transform -translate-y-1/2 z-10 cursor-pointer">
            <IoIosArrowForward className="text-white text-5xl" />
          </div>
          <div className="custom-prev-button absolute left-5 top-1/2 transform -translate-y-1/2 z-10 cursor-pointer">
            <IoIosArrowBack className="text-white text-5xl" />
          </div>
        </Swiper>
      ) : (
        <div className="w-full h-[300px] bg-gray-800 flex items-center justify-center text-white">
          <p className="text-xl">{__("No featured videos to show")}</p>
        </div>
      )}

      <style jsx="true" global="true">{`
        .swiper-button-next,
        .swiper-button-prev {
          display: none !important;
        }
      `}</style>
    </div>
  );
}
