import React, { useEffect } from "react";
import ApplicationLogo from "@/Components/ApplicationLogo";
import MobileAppHeader from "@/Components/New/MobileAppHeader";
import MobileBottomNav from "@/Components/New/MobileBottomNav";
import { Link } from "@inertiajs/inertia-react";
import { ToastContainer } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";
import BrowserNotifications from "@/Components/BrowserNotifications";

export default function Guest({ children }) {
    // Add mobile nav active class to body for proper spacing
    useEffect(() => {
        document.body.classList.add('mobile-nav-active');
        
        return () => {
            document.body.classList.remove('mobile-nav-active');
        };
    }, []);

    return (
        <div className="min-h-screen flex flex-col" style={{ background: "#000" }}>
            <ToastContainer theme="dark" />
            <BrowserNotifications />
            
            {/* Mobile App Header - Hidden on desktop */}
            <div className="d-block d-md-none">
                <MobileAppHeader />
            </div>
            
            <div className="flex-grow flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
                {/* Desktop Logo - Hidden on mobile */}
                <div className="d-none d-md-block mb-6">
                    <Link href="/">
                        <ApplicationLogo className="w-20 h-20 fill-current text-gray-500" />
                    </Link>
                </div>

                <div className="w-full sm:max-w-md mt-6 px-6 py-4 bg-dark shadow-md overflow-hidden sm:rounded-lg">
                    {children}
                </div>
            </div>
            
            {/* Mobile Bottom Navigation - Hidden on desktop */}
            <div className="d-block d-md-none">
                <MobileBottomNav />
            </div>
        </div>
    );
}
