import React, { useState, useRef } from "react";
import Front from "@/Layouts/Front";
import { Head } from "@inertiajs/inertia-react";
import __ from "@/Functions/Translate";
import SecondaryButton from "@/Components/SecondaryButton";
import { FcEmptyFilter } from "react-icons/fc";
import { Inertia } from "@inertiajs/inertia";
import Spinner from "@/Components/Spinner";
import VideosLoop from "./Partials/VideosLoop";
import Modal from "@/Components/Modal";
import SingleVideo from "./SingleVideo";
import TextInput from "@/Components/TextInput";
import debounce from "lodash.debounce";
import { IoMdFunnel, IoMdClose } from "react-icons/io";
import { Button } from "react-bootstrap";

export default function BrowseVideos({
  videos,
  category,
  categories,
  exploreImage,
}) {
  const [sort, setSort] = useState("Most Viewed");
  const [search, setSearch] = useState("");
  const [isLoading, setLoading] = useState(false);
  const [playVideo, setPlayVideo] = useState(false);
  const [modal, setModal] = useState(false);
  const [showMobileFilters, setShowMobileFilters] = useState(false);

  const updateTerm = debounce((e) => {
    console.log(`debounced term updated to: ${e.target.value}`);

    if (e.target.value.length > 2) {
      setLoading(true);
      Inertia.reload({
        data: {
          keyword: e.target.value,
          sortBy: sort,
        },
        only: ["videos"],
        onFinish: () => setLoading(false),
      });
    } else {
      Inertia.reload({
        data: {
          sortBy: sort,
          keyword: "",
        },
        only: ["videos"],
        onFinish: () => setLoading(false),
      });
    }
  }, 500);

  const sortItems = (e, sortBy) => {
    setSort(sortBy);
    setLoading(true);

    Inertia.reload({
      data: {
        sortBy,
      },
      only: ["videos"],
      onFinish: () => setLoading(false),
    });
  };

  const playModal = (e, video) => {
    e.preventDefault();
    setPlayVideo(video);
    setModal(true);
  };

  const filters = useRef();

  const [selectedCategories, setSelectedCategories] = useState([]);

  const submit = (e) => {
    e.preventDefault();

    Inertia.visit(
      route("videos.browse", {
        search,
        sort,
        selectedCategories,
      }),
      {
        only: ["videos"],
        preserveState: true,
        onBefore: () => setLoading(true),
        onFinish: () => setLoading(false),
      }
    );

    hideFilters();
  };

  const handleCategories = (event) => {
    const { value, checked } = event.target;
    if (checked) {
      setSelectedCategories((current) => [...current, value]);
    } else {
      setSelectedCategories((current) => current.filter((v) => v !== value));
    }
  };

  const showFilters = (e) => {
    e.preventDefault();
    setShowMobileFilters(true);
    // Prevent body scroll when filters are open
    document.body.style.overflow = "hidden";
  };

  const hideFilters = (e) => {
    e?.preventDefault();
    setShowMobileFilters(false);
    // Restore body scroll
    document.body.style.overflow = "auto";
  };

  return (
    <Front
      containerClass="w-full"
      extraHeader={true}
      extraHeaderTitle={__("Browse Videos")}
      extraHeaderImage={exploreImage}
      extraHeaderText={""}
      extraImageHeight={"h-14"}
    >
      <Head
        title={`${
          category !== null
            ? __(":categoryName Videos", {
                categoryName: category.category,
              })
            : __("Browse Videos")
        }`}
      />

      <Modal show={modal} onClose={(e) => setModal(false)}>
        {playVideo && <SingleVideo video={playVideo} inModal={true} />}
      </Modal>

      <div className="flex w-full -mt-16">
        {/* Desktop Filters */}
        <form onSubmit={submit} className="desktop-filters">
          <div className="w-56 flex-shrink-0 mr-5">
            <h3 className="text-xl font-bold block text-white p-3 bg-footer shadow">
              {__("Search")}
            </h3>
            <div className="bg-footer shadow p-3">
              <TextInput
                className="w-full rounded-0"
                name="search"
                value={search}
                handleChange={(e) => setSearch(e.target.value)}
                placeholder={__("Search Video")}
              />
            </div>

            <h3 className="mt-5 text-xl font-bold block p-3 text-white shadow bg-footer">
              {__("Sort By")}
            </h3>
            <div className="bg-footer shadow p-3">
              <div className="flex items-center text-white">
                <input
                  type={"radio"}
                  name="sort"
                  value="Most Viewed"
                  checked={sort === "Most Viewed"}
                  className="mr-2"
                  onChange={(e) => setSort(e.target.value)}
                />
                {__("Most Viewed")}
              </div>
              <div className="flex items-center text-white">
                <input
                  type={"radio"}
                  name="sort"
                  value="Recently Uploaded"
                  checked={sort === "Recently Uploaded"}
                  className="mr-2"
                  onChange={(e) => setSort(e.target.value)}
                />
                {__("Recently Uploaded")}
              </div>
              <div className="flex items-center text-white">
                <input
                  type={"radio"}
                  name="sort"
                  checked={sort === "Older"}
                  value="Older"
                  className="mr-2"
                  onChange={(e) => setSort(e.target.value)}
                />
                {__("Older Videos")}
              </div>
              <div className="flex items-center text-white">
                <input
                  type={"radio"}
                  name="sort"
                  checked={sort === "Highest Price"}
                  value="Highest Price"
                  className="mr-2"
                  onChange={(e) => setSort(e.target.value)}
                />
                {__("Highest Price")}
              </div>
              <div className="flex items-center text-white">
                <input
                  type={"radio"}
                  name="sort"
                  checked={sort === "Lowest Price"}
                  value="Lowest Price"
                  className="mr-2"
                  onChange={(e) => setSort(e.target.value)}
                />
                {__("Lowest Price")}
              </div>
            </div>

            <h3 className="mt-5text-xl font-bold block p-3 text-white shadow">
              {__("Category")}
            </h3>
            <div className="bg-footer shadow p-3">
              {categories.map((cat) => {
                return (
                  <div
                    key={`catFilter-${cat.id}`}
                    className="flex items-center text-white"
                  >
                    <input
                      type="checkbox"
                      name="categories[]"
                      className="mr-2"
                      value={cat.id}
                      onChange={handleCategories}
                      checked={selectedCategories.includes(cat.id.toString())}
                    />
                    {cat.category}
                  </div>
                );
              })}
            </div>

            {isLoading ? (
              <div className="my-3">
                <Spinner />
              </div>
            ) : (
              <Button className="me-2 btn text-uppercase position-relative d-flex w-full mt-2 mb-5">
                {__("Apply Filters")}
              </Button>
            )}

          </div>
        </form>

        {/* Mobile Filter Overlay */}
        {showMobileFilters && (
          <div className="mobile-filter-overlay lg:hidden" onClick={hideFilters}>
            <div className="mobile-filter-panel" onClick={(e) => e.stopPropagation()}>
              <div className="mobile-filter-header">
                <h3 className="mobile-filter-title">{__("Filters")}</h3>
                <button className="mobile-filter-close" onClick={hideFilters}>
                  <IoMdClose />
                </button>
              </div>
              
              <div className="mobile-filter-content">
                <form onSubmit={submit}>
                  {/* Search Section */}
                  <div className="mobile-filter-section">
                    <h4 className="mobile-filter-section-title">{__("Search")}</h4>
                    <input
                      type="text"
                      className="mobile-filter-search-input"
                      name="search"
                      value={search}
                      onChange={(e) => setSearch(e.target.value)}
                      placeholder={__("Search Video")}
                    />
                  </div>

                  {/* Sort Section */}
                  <div className="mobile-filter-section">
                    <h4 className="mobile-filter-section-title">{__("Sort By")}</h4>
                    <div className="mobile-filter-option">
                      <input
                        type="radio"
                        name="sort"
                        value="Most Viewed"
                        checked={sort === "Most Viewed"}
                        onChange={(e) => setSort(e.target.value)}
                      />
                      {__("Most Viewed")}
                    </div>
                    <div className="mobile-filter-option">
                      <input
                        type="radio"
                        name="sort"
                        value="Recently Uploaded"
                        checked={sort === "Recently Uploaded"}
                        onChange={(e) => setSort(e.target.value)}
                      />
                      {__("Recently Uploaded")}
                    </div>
                    <div className="mobile-filter-option">
                      <input
                        type="radio"
                        name="sort"
                        value="Older"
                        checked={sort === "Older"}
                        onChange={(e) => setSort(e.target.value)}
                      />
                      {__("Older Videos")}
                    </div>
                    <div className="mobile-filter-option">
                      <input
                        type="radio"
                        name="sort"
                        value="Highest Price"
                        checked={sort === "Highest Price"}
                        onChange={(e) => setSort(e.target.value)}
                      />
                      {__("Highest Price")}
                    </div>
                    <div className="mobile-filter-option">
                      <input
                        type="radio"
                        name="sort"
                        value="Lowest Price"
                        checked={sort === "Lowest Price"}
                        onChange={(e) => setSort(e.target.value)}
                      />
                      {__("Lowest Price")}
                    </div>
                  </div>

                  {/* Category Section */}
                  <div className="mobile-filter-section">
                    <h4 className="mobile-filter-section-title">{__("Category")}</h4>
                    {categories.map((cat) => (
                      <div key={`catFilter-${cat.id}`} className="mobile-filter-option">
                        <input
                          type="checkbox"
                          name="categories[]"
                          value={cat.id}
                          onChange={handleCategories}
                          checked={selectedCategories.includes(cat.id.toString())}
                        />
                        {cat.category}
                      </div>
                    ))}
                  </div>

                  {/* Apply Button */}
                  {isLoading ? (
                    <div className="text-center mt-6">
                      <Spinner />
                    </div>
                  ) : (
                    <button type="submit" className="mobile-filter-apply-btn">
                      {__("Apply Filters")}
                    </button>
                  )}
                </form>
              </div>
            </div>
          </div>
        )}

        <div className="flex-grow">
          <button
            onClick={(e) => showFilters(e)}
            className="mobile-show-filters-btn lg:hidden"
          >
            <IoMdFunnel />
            {__("Show Filters")}
          </button>

          {videos.total === 0 && (
            <div className="text-xl bg-white rounded-lg shadow text-gray-600 dark:bg-zinc-900 dark:text-white font-light p-3 flex items-center">
              <FcEmptyFilter className="w-12 h-12 mr-2" />
              {__("No videos to show")}
            </div>
          )}

          <VideosLoop videos={videos.data} />

          {videos.last_page > 1 && (
            <>
              <div className="flex text-gray-600 mt-10 mb-5 text-sm">
                {__("Page: :pageNumber of :lastPage", {
                  pageNumber: videos.current_page,
                  lastPage: videos.last_page,
                })}
              </div>

              <SecondaryButton
                processing={videos.prev_page_url ? false : true}
                className="mr-3"
                onClick={(e) => Inertia.visit(videos.prev_page_url)}
              >
                {__("Previous")}
              </SecondaryButton>

              <SecondaryButton
                processing={videos.next_page_url ? false : true}
                onClick={(e) => Inertia.visit(videos.next_page_url)}
              >
                {__("Next")}
              </SecondaryButton>
            </>
          )}
        </div>
      </div>
    </Front>
  );
}
