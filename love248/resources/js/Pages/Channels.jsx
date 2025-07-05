import React, { useRef, useState } from "react";
import { Head, usePage } from "@inertiajs/inertia-react";
import __ from "@/Functions/Translate";
import SecondaryButton from "@/Components/SecondaryButton";
import { FcEmptyFilter } from "react-icons/fc";
import { Inertia } from "@inertiajs/inertia";
import ChannelsLoop from "@/Components/ChannelsLoop";
import Spinner from "@/Components/Spinner";
import Front from "@/Layouts/Front";
import TextInput from "@/Components/TextInput";
import { IoMdFunnel, IoMdClose } from "react-icons/io";
import { Button } from "react-bootstrap";

export default function Channels({ channels, exploreImage }) {
    const { categories } = usePage().props;

    const filters = useRef();

    console.log(exploreImage);

    const [sort, setSort] = useState("Popularity");
    const [search, setSearch] = useState("");
    const [isLoading, setLoading] = useState(false);
    const [selectedCategories, setSelectedCategories] = useState([]);
    const [showMobileFilters, setShowMobileFilters] = useState(false);

    const submit = (e) => {
        e.preventDefault();

        Inertia.visit(
            route("channels.browse", {
                search,
                sort,
                selectedCategories,
            }),
            {
                only: ["channels"],
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
            setSelectedCategories((current) =>
                current.filter((v) => v !== value)
            );
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
            extraHeaderTitle={__("Discover Channels")}
            extraHeaderImage={exploreImage}
            extraHeaderText={""}
            extraImageHeight={"h-14"}
        >
            <Head title={__("Discover Channels")} />

            <div className="flex w-full -mt-16">
                {/* Desktop Filters */}
                <form onSubmit={submit} className="desktop-filters">
                    <div className="w-56 flex-shrink-0 mr-5">
                        <h3 className="text-white text-xl font-bold block p-3 bg-footer shadow rounded-0">
                            {__("Search")}
                        </h3>
                        <div className="bg-footer dark:bg-zinc-800 rounded-b-lg shadow p-3 rounded-0">
                            <TextInput
                                className="w-full rounded-0"
                                name="search"
                                value={search}
                                handleChange={(e) => setSearch(e.target.value)}
                                placeholder={__("Search Channel")}
                            />
                        </div>

                        <h3 className="mt-5 text-white text-xl font-bold block p-3 bg-footer shadowrounded-0">
                            {__("Sort By")}
                        </h3>
                        <div className="bg-footer shadow p-3">
                            <div className="flex items-center text-white">
                                <input
                                    type={"radio"}
                                    name="sort"
                                    value="Popularity"
                                    checked={sort === "Popularity"}
                                    className="mr-2 text-white"
                                    onChange={(e) => setSort(e.target.value)}
                                />
                                {__("Popularity")}
                            </div>
                            <div className="flex items-center  text-white">
                                <input
                                    type={"radio"}
                                    name="sort"
                                    value="Recently Joined"
                                    checked={sort === "Recently Joined"}
                                    className="mr-2"
                                    onChange={(e) => setSort(e.target.value)}
                                />
                                {__("Recently Joined")}
                            </div>
                            <div className="flex items-center text-white">
                                <input
                                    type={"radio"}
                                    name="sort"
                                    checked={sort === "Followers"}
                                    value="Followers"
                                    className="mr-2"
                                    onChange={(e) => setSort(e.target.value)}
                                />
                                {__("Followers")}
                            </div>
                            <div className="flex items-center text-white">
                                <input
                                    type={"radio"}
                                    name="sort"
                                    checked={sort === "Alphabetically"}
                                    value="Alphabetically"
                                    className="mr-2"
                                    onChange={(e) => setSort(e.target.value)}
                                />
                                {__("Alphabetically")}
                            </div>
                        </div>

                        <h3 className="mt-5 text-white text-xl font-bold block p-3 shadow bg-footer">
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
                                            checked={selectedCategories.includes(
                                                cat.id.toString()
                                            )}
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
                            <Button className="me-2 btn text-uppercase position-relative d-flex w-100 mt-2 mb-5">
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
                                            placeholder={__("Search Channel")}
                                        />
                                    </div>

                                    {/* Sort Section */}
                                    <div className="mobile-filter-section">
                                        <h4 className="mobile-filter-section-title">{__("Sort By")}</h4>
                                        <div className="mobile-filter-option">
                                            <input
                                                type="radio"
                                                name="sort"
                                                value="Popularity"
                                                checked={sort === "Popularity"}
                                                onChange={(e) => setSort(e.target.value)}
                                            />
                                            {__("Popularity")}
                                        </div>
                                        <div className="mobile-filter-option">
                                            <input
                                                type="radio"
                                                name="sort"
                                                value="Recently Joined"
                                                checked={sort === "Recently Joined"}
                                                onChange={(e) => setSort(e.target.value)}
                                            />
                                            {__("Recently Joined")}
                                        </div>
                                        <div className="mobile-filter-option">
                                            <input
                                                type="radio"
                                                name="sort"
                                                value="Followers"
                                                checked={sort === "Followers"}
                                                onChange={(e) => setSort(e.target.value)}
                                            />
                                            {__("Followers")}
                                        </div>
                                        <div className="mobile-filter-option">
                                            <input
                                                type="radio"
                                                name="sort"
                                                value="Alphabetically"
                                                checked={sort === "Alphabetically"}
                                                onChange={(e) => setSort(e.target.value)}
                                            />
                                            {__("Alphabetically")}
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

                    {channels.total === 0 && (
                        <div className="text-xl bg-white dark:bg-zinc-900 rounded-lg shadow text-gray-600 dark:text-white font-light p-3 flex items-center">
                            <FcEmptyFilter className="w-12 h-12 mr-2" />
                            {__("No channels to show")}
                        </div>
                    )}

                    <ChannelsLoop channels={channels.data} />

                    {channels.last_page > 1 && (
                        <>
                            <div className="flex text-gray-600 mt-10 mb-5 text-sm">
                                {__("Page: :pageNumber of :lastPage", {
                                    pageNumber: channels.current_page,
                                    lastPage: channels.last_page,
                                })}
                            </div>

                            <SecondaryButton
                                processing={
                                    channels.prev_page_url ? false : true
                                }
                                className="mr-3"
                                onClick={(e) =>
                                    Inertia.visit(channels.prev_page_url)
                                }
                            >
                                {__("Previous")}
                            </SecondaryButton>

                            <SecondaryButton
                                processing={
                                    channels.next_page_url ? false : true
                                }
                                onClick={(e) =>
                                    Inertia.visit(channels.next_page_url)
                                }
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
