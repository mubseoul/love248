import React from 'react'

//react-router-dom
import { Link } from "@inertiajs/inertia-react";

const CustomToggle = React.forwardRef(({ children, variant, onClick }, ref) => (
    <Link
        href={route("home")}
        onClick={(e) => {
            e.preventDefault();
            onClick(e);
        }}
        className={variant}
    >
        {children}

    </Link>
));
export default CustomToggle;