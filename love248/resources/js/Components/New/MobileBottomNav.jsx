import React from "react";
import { Link, usePage } from "@inertiajs/inertia-react";
import { useSelector } from "react-redux";

const MobileBottomNav = () => {
  const { auth } = usePage().props;
  const { url } = usePage();
  
  // Navigation items for mobile bottom nav
  const navItems = [
    {
      name: "Home",
      route: "home",
      icon: "fas fa-home",
      path: "/",
    },
    {
      name: "Channels",
      route: "channels.browse",
      icon: "fas fa-tv",
      path: "/channels",
    },
    {
      name: "Videos",
      route: "videos.browse", 
      icon: "fas fa-play-circle",
      path: "/videos",
    },
    {
      name: "Gallery",
      route: "gallery.browse",
      icon: "fas fa-images",
      path: "/gallery",
    },
    {
      name: auth.user ? "Profile" : "Login",
      route: auth.user ? "profile.edit" : "login",
      icon: auth.user ? "fas fa-user-circle" : "fas fa-sign-in-alt",
      path: auth.user ? "/profile" : "/login",
    },
  ];

  const isActive = (path) => {
    if (path === "/" && url === "/") return true;
    if (path !== "/" && url.startsWith(path)) return true;
    return false;
  };

  return (
    <div className="mobile-bottom-nav">
      <div className="mobile-bottom-nav-container">
        {navItems.map((item, index) => (
          <Link
            key={index}
            href={route(item.route)}
            className={`mobile-nav-item ${
              isActive(item.path) ? "active" : ""
            }`}
          >
            <div className="mobile-nav-icon">
              <i className={item.icon}></i>
            </div>
            <span className="mobile-nav-label">{item.name}</span>
          </Link>
        ))}
      </div>
    </div>
  );
};

export default MobileBottomNav; 