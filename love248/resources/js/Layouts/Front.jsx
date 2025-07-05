import HeaderDefault from "@/Components/New/HeaderDefault";
import MobileAppHeader from "@/Components/New/MobileAppHeader";
import FooterDefault from "@/Components/New/FooterDefault";
import MobileBottomNav from "@/Components/New/MobileBottomNav";
import { usePage, Link } from "@inertiajs/inertia-react";
import CookieConsent from "react-cookie-consent";
import { ToastContainer } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";
import { Container } from "react-bootstrap";
import __ from "@/Functions/Translate";
import { FiEdit2 } from "react-icons/fi";
import { useEffect } from "react";
import BrowserNotifications from "@/Components/BrowserNotifications";
import PWAInstaller from "@/Components/PWAInstaller";

export default function Front({
  children,
  extraHeader = false,
  extraHeaderTitle = "",
  extraHeaderImage = "",
  extraImageHeight = "",
}) {
  const { seo_title, pages } = usePage().props;

  // Add mobile nav active class to body for proper spacing
  useEffect(() => {
    document.body.classList.add('mobile-nav-active');
    
    return () => {
      document.body.classList.remove('mobile-nav-active');
    };
  }, []);

  return (
    <div className="flex flex-col min-h-screen" style={{ background: "#000" }}>
      <ToastContainer theme="dark" />
      <BrowserNotifications />
      <PWAInstaller />
      
      {/* Desktop Header - Hidden on mobile */}  
      <div className="d-none d-md-block">
        <HeaderDefault />
      </div>
      
      {/* Mobile App Header - Hidden on desktop */}
      <div className="d-block d-md-none">
        <MobileAppHeader />
      </div>

      {extraHeader && (
        <div className="profile-box pt-[60px] mb-0 md:mt-[74px] md:mb-0 mb-5 mt-[0px] bg-dark">
          <Container fluid>
            <div className="d-flex flex-wrap align-items-center justify-content-between gap-2">
              <div className="d-flex align-items-center gap-3">
                <div className="account-logo d-flex align-items-center position-relative">
                  <img
                    src={extraHeaderImage}
                    className={extraImageHeight}
                    alt="profile"
                  />
                </div>
                <div className="flex-grow-1">
                  <h4 className="text-capitalize text-white fw-500">
                    {extraHeaderTitle}
                  </h4>
                </div>
              </div>
            </div>
          </Container>
        </div>
      )}

      <div
        className="max-w-7xl mx-auto flex-grow min-h-full px-3 w-full"
        style={{ background: "#000" }}
      >
        <div className="mt-[100px] md:mt-[100px] pt-0 md:pt-0 pb-4 md:pb-0">{children}</div>
      </div>

      {/* Desktop Cookie Consent - Hidden on mobile */}
      <div className="d-none d-md-block">
        <CookieConsent>
          {__("This website uses cookies to enhance the user experience.")}
        </CookieConsent>
      </div>

      {/* Desktop Footer - Hidden on mobile */}
      <div className="d-none d-md-block">
        <FooterDefault />
      </div>

      {/* Mobile Bottom Navigation - Hidden on desktop */}
      <div className="d-block d-md-none">
        <MobileBottomNav />
      </div>
    </div>
  );
}
