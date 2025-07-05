import React, { useState, useEffect } from "react";
import { Link, usePage } from "@inertiajs/inertia-react";
import { toast } from "react-toastify";
import Logo from "./Logo";
import {
  BiMenu,
  BiX,
  BiUser,
  BiLogIn,
  BiUserPlus,
  BiBell,
  BiCog,
  BiVideo,
  BiImageAdd,
  BiSolidVideos,
} from "react-icons/bi";
import { MdGeneratingTokens } from "react-icons/md";
import __ from "@/Functions/Translate";

const MobileAppHeader = () => {
  const { auth, flash } = usePage().props;
  const [isMenuOpen, setIsMenuOpen] = useState(false);
  const [isScrolled, setIsScrolled] = useState(false);

  useEffect(() => {
    const handleScroll = () => {
      setIsScrolled(window.scrollY > 10);
    };

    window.addEventListener("scroll", handleScroll);
    return () => window.removeEventListener("scroll", handleScroll);
  }, []);

  const toggleMenu = () => {
    setIsMenuOpen(!isMenuOpen);
    // Prevent body scroll when menu is open
    document.body.style.overflow = !isMenuOpen ? "hidden" : "auto";
  };

  const closeMenu = () => {
    setIsMenuOpen(false);
    document.body.style.overflow = "auto";
  };



  const menuItems = [
    { name: "Home", route: "home", icon: "fas fa-home", path: "/" },
    { name: "Channels", route: "channels.browse", icon: "fas fa-tv", path: "/channels" },
    { name: "Videos", route: "videos.browse", icon: "fas fa-play-circle", path: "/videos" },
    { name: "Gallery", route: "gallery.browse", icon: "fas fa-images", path: "/gallery" },
    { name: "Token Packs", route: "token.packages", icon: "fas fa-coins", path: "/tokens" },
    { name: "Subscriptions", route: "subscription.plan", icon: "fas fa-crown", path: "/subscriptions" },
  ];

  const userMenuItems = auth.user ? [
    ...(auth.user.is_streamer === "yes" ? [
      { name: "My Channel", route: "channel", params: { user: auth.user.username }, icon: BiSolidVideos },
      { name: "Channel Settings", route: "channel.settings", icon: BiCog },
      { name: "Upload Videos", route: "videos.list", icon: BiVideo },
      { name: "Upload Gallery", route: "gallery.list", icon: BiImageAdd },
    ] : [
    ]),
    { name: "My Tokens", route: "profile.myTokens", icon: MdGeneratingTokens },
    { name: "Notifications", route: "notifications.inbox", icon: BiBell },
    { name: "My Account", route: "profile.edit", icon: BiUser },
  ] : [
    { name: "Login", route: "login", icon: BiLogIn },
    { name: "Sign Up", route: "signup", icon: BiUserPlus },
  ];

  return (
    <>
      <header className={`mobile-app-header ${isScrolled ? 'scrolled' : ''}`}>
        <div className="mobile-header-container">
          {/* Left: Menu/Back Button */}
          <button
            className="mobile-header-btn"
            onClick={toggleMenu}
            aria-label="Menu"
          >
            {isMenuOpen ? <BiX size={24} /> : <BiMenu size={24} />}
          </button>

          {/* Center: Logo */}
          <div className="mobile-header-logo">
            <Logo />
          </div>

          {/* Right: User Actions */}
          <div className="mobile-header-actions">
            {auth.user && (
              <Link
                href={route("notifications.inbox")}
                className="mobile-header-btn relative"
                aria-label="Notifications"
              >
                <BiBell size={20} />
                {auth.unreadNotifications > 0 && (
                  <span className="notification-badge">
                    {auth.unreadNotifications > 9 ? '9+' : auth.unreadNotifications}
                  </span>
                )}
              </Link>
            )}
          </div>
        </div>
      </header>

      {/* Mobile Menu Overlay */}
      {isMenuOpen && (
        <div className="mobile-menu-overlay" onClick={closeMenu}>
          <div className="mobile-menu-container" onClick={(e) => e.stopPropagation()}>
            <div className="mobile-menu-header">
              <div className="mobile-menu-user">
                {auth.user ? (
                  <div className="user-info">
                    {auth.user.profile_picture && (
                      <img
                        src={auth.user.profile_picture}
                        alt="Profile"
                        className="user-avatar"
                        onError={(e) => { e.target.style.display = 'none'; }}
                      />
                    )}
                    <div className="user-details">
                      <h3 className="user-name">{auth.user.name}</h3>
                      <p className="user-email">{auth.user.email}</p>
                    </div>
                  </div>
                ) : (
                  <div className="guest-info">
                    <h3>Welcome!</h3>
                    <p>Sign in to access all features</p>
                  </div>
                )}
              </div>
              <button className="mobile-menu-close" onClick={closeMenu}>
                <BiX size={24} />
              </button>
            </div>

            <div className="mobile-menu-content">
              {/* Main Navigation */}
              <div className="menu-section">
                <h4 className="menu-section-title">Browse</h4>
                <ul className="menu-list">
                  {menuItems.map((item, index) => (
                    <li key={index} className="menu-item">
                      <Link
                        href={route(item.route)}
                        className="menu-link"
                        onClick={closeMenu}
                      >
                        <i className={item.icon}></i>
                        <span>{item.name}</span>
                      </Link>
                    </li>
                  ))}
                </ul>
              </div>

              {/* User Menu */}
              <div className="menu-section">
                <h4 className="menu-section-title">
                  {auth.user ? "Account" : "Get Started"}
                </h4>
                <ul className="menu-list">
                  {userMenuItems.map((item, index) => (
                    <li key={index} className="menu-item">
                      <Link
                        href={route(item.route, item.params || {})}
                        className="menu-link"
                        onClick={closeMenu}
                        {...(item.route === "logout" ? { method: "post", as: "button" } : {})}
                      >
                        <item.icon size={18} />
                        <span>{item.name}</span>
                        {item.name === "Notifications" && auth.unreadNotifications > 0 && (
                          <span className="menu-badge">{auth.unreadNotifications}</span>
                        )}
                      </Link>
                    </li>
                  ))}
                  {auth.user && (
                    <li className="menu-item">
                      <Link
                        href={route("logout")}
                        method="post"
                        as="button"
                        className="menu-link logout-link"
                        onClick={closeMenu}
                      >
                        <i className="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                      </Link>
                    </li>
                  )}
                </ul>
              </div>
            </div>
          </div>
        </div>
      )}
    </>
  );
};

export default MobileAppHeader; 