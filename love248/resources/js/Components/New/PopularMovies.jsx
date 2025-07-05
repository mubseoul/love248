import SectionSlider from "../slider/SectionSlider";
import { useState, Fragment, memo } from "react";
import CardStyle from "../cards/CardStyle";

const PopularMovies = memo(({ channels }) => {
    const [title] = useState("Popular Movies");

    return (
        <Fragment>
            <SectionSlider
                title={title}
                list={channels}
                className="popular-movies-block streamit-block"
            >
                {(data) => (
                    <CardStyle
                        image={data.cover_picture}
                        title={data.name}
                        movieTime={data.movieTime}
                        watchlistLink="/playlist"
                        link="/movies-detail"
                    />
                )}
            </SectionSlider>
        </Fragment>
    );
})

PopularMovies.displayName = 'PopularMovies';
export default PopularMovies;
