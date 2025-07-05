import "./bootstrap";
import "../css/app.css";

import React from "react";
import { createRoot } from "react-dom/client";
import { createInertiaApp } from "@inertiajs/inertia-react";
import { InertiaProgress } from "@inertiajs/progress";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { Provider } from "react-redux";
import store from "./store";
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/effect-fade";
import "swiper/css/free-mode";
import "../assets/scss/streamit.scss";
import "../assets/scss/custom.scss";
import "../assets/scss/rtl.scss";
import "animate.css/animate.css";
import "choices.js/public/assets/styles/choices.min.css";
import "../assets/vendor/font-awesome/css/all.min.css";
import "../assets/vendor/iconly/css/style.css";
import("videojs-youtube/dist/Youtube.min.js");
import { toast } from 'react-toastify';

// Global flash message handler
let lastProcessedMessage = null;

const handleFlashMessages = (flash) => {
    if (!flash) return;
    
    // Prevent processing the same message multiple times
    const currentMessage = JSON.stringify(flash);
    if (currentMessage === lastProcessedMessage) return;
    lastProcessedMessage = currentMessage;
    
    // Handle different types of flash messages
    if (flash.message) {
        toast.success(flash.message);
    }
    if (flash.error) {
        toast.error(flash.error);
    }
    if (flash.info) {
        toast.info(flash.info);
    }
    if (flash.warning) {
        toast.warning(flash.warning);
    }
};

const appName =
  window.document.getElementsByTagName("title")[0]?.innerText || "Laravel";

if (document.getElementById("modal-root")) {
  createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
      resolvePageComponent(
        `./Pages/${name}.jsx`,
        import.meta.glob("./Pages/**/*.jsx")
      ),
    setup({ el, App, props }) {
      const root = createRoot(el);

      // Process flash messages at the app level
      handleFlashMessages(props.initialPage?.props?.flash);

      root.render(
        <Provider store={store}>
          <App {...props} />
        </Provider>
      );
    },
    progress: {
      color: '#4B5563',
    },
  });

  InertiaProgress.init({ color: "#4B5563" });
}

// Listen for Inertia page visits to handle flash messages
document.addEventListener('inertia:success', (event) => {
    const flash = event.detail.page?.props?.flash;
    handleFlashMessages(flash);
});
