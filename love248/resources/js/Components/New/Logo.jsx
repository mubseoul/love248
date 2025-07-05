import hostar from "../../../assets/images/logo-hotstar.webp";
import prime from "../../../assets/images/logo-prime.webp";
import hulu from "../../../assets/images/logo-hulu.webp";
import img1 from "../../../assets/images/logo.webp";
import { Link } from "@inertiajs/inertia-react";
import React, { memo, Fragment } from "react";

const Logo = memo(() => {
    return (
        <Fragment>
            <div className="logo-default d-inline-flex">
                <Link className="navbar-brand text-primary" href={route("home")}>
                    <img
                        className="img-fluid logo"
                        src={img1}
                        loading="lazy"
                        alt="streamit"
                    />
                </Link>
            </div>
            <div className="logo-hotstar">
                <Link className="navbar-brand text-primary" href={route("home")}>
                    <img
                        className="img-fluid logo"
                        src={hostar}
                        loading="lazy"
                        alt="streamit"
                    />
                </Link>
            </div>
            <div className="logo-prime">
                <Link className="navbar-brand text-primary" href={route("home")}>
                    <img
                        className="img-fluid logo"
                        src={prime}
                        loading="lazy"
                        alt="streamit"
                    />
                </Link>
            </div>
            <div className="logo-hulu">
                <Link className="navbar-brand text-primary" href={route("home")}>
                    <img
                        className="img-fluid logo"
                        src={hulu}
                        loading="lazy"
                        alt="streamit"
                    />
                </Link>
            </div>
        </Fragment>
    );
});

Logo.displayName = "Logo";
export default Logo;
