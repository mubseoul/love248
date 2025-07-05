import { useState, useEffect } from 'react';
import { toast } from 'react-toastify';
import __ from '@/Functions/Translate';
import { MdInstallMobile, MdClose, MdDownload } from 'react-icons/md';

export default function PWAInstaller() {
    const [deferredPrompt, setDeferredPrompt] = useState(null);
    const [showInstallPrompt, setShowInstallPrompt] = useState(false);
    const [isInstalled, setIsInstalled] = useState(false);
    const [swRegistration, setSwRegistration] = useState(null);

    useEffect(() => {
        // Check if app is already installed
        if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone) {
            setIsInstalled(true);
        }

        // Register service worker
        registerServiceWorker();

        // Listen for beforeinstallprompt event
        const handleBeforeInstallPrompt = (e) => {
            console.log('PWA: Install prompt available');
            e.preventDefault();
            setDeferredPrompt(e);
            
            // Show install prompt after a delay (don't be too aggressive)
            setTimeout(() => {
                if (!isInstalled && !localStorage.getItem('pwa-install-dismissed')) {
                    setShowInstallPrompt(true);
                }
            }, 10000); // Show after 10 seconds
        };

        // Listen for app installed event
        const handleAppInstalled = () => {
            console.log('PWA: App was installed');
            setIsInstalled(true);
            setShowInstallPrompt(false);
            setDeferredPrompt(null);
            toast.success(__('Love248 has been installed successfully!'));
        };

        window.addEventListener('beforeinstallprompt', handleBeforeInstallPrompt);
        window.addEventListener('appinstalled', handleAppInstalled);

        return () => {
            window.removeEventListener('beforeinstallprompt', handleBeforeInstallPrompt);
            window.removeEventListener('appinstalled', handleAppInstalled);
        };
    }, [isInstalled]);

    const registerServiceWorker = async () => {
        if ('serviceWorker' in navigator) {
            try {
                const registration = await navigator.serviceWorker.register('/sw.js', {
                    scope: '/'
                });
                
                setSwRegistration(registration);
                console.log('PWA: Service Worker registered successfully:', registration);

                // Listen for service worker updates
                registration.addEventListener('updatefound', () => {
                    const newWorker = registration.installing;
                    newWorker.addEventListener('statechange', () => {
                        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            // New content is available
                            showUpdateNotification(registration);
                        }
                    });
                });

                // Check for updates periodically
                setInterval(() => {
                    registration.update();
                }, 60000); // Check every minute

            } catch (error) {
                console.error('PWA: Service Worker registration failed:', error);
            }
        }
    };

    const showUpdateNotification = (registration) => {
        const updateToast = toast.info(
            <div className="flex items-center justify-between">
                <span>{__('A new version is available!')}</span>
                <button
                    onClick={() => {
                        // Tell the service worker to skip waiting
                        if (registration.waiting) {
                            registration.waiting.postMessage({ type: 'SKIP_WAITING' });
                        }
                        window.location.reload();
                        toast.dismiss(updateToast);
                    }}
                    className="ml-2 px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700"
                >
                    {__('Update')}
                </button>
            </div>,
            {
                autoClose: false,
                closeOnClick: false,
                draggable: false
            }
        );
    };

    const handleInstallClick = async () => {
        if (!deferredPrompt) {
            // Fallback for browsers that don't support the install prompt
            showManualInstallInstructions();
            return;
        }

        try {
            // Show the install prompt
            deferredPrompt.prompt();
            
            // Wait for the user to respond to the prompt
            const { outcome } = await deferredPrompt.userChoice;
            
            if (outcome === 'accepted') {
                console.log('PWA: User accepted the install prompt');
                toast.success(__('Installing Love248...'));
            } else {
                console.log('PWA: User dismissed the install prompt');
            }
            
            setDeferredPrompt(null);
            setShowInstallPrompt(false);
        } catch (error) {
            console.error('PWA: Error showing install prompt:', error);
            showManualInstallInstructions();
        }
    };

    const showManualInstallInstructions = () => {
        const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
        const isAndroid = /Android/.test(navigator.userAgent);
        
        let instructions = '';
        
        if (isIOS) {
            instructions = __('To install: Tap the Share button in Safari, then "Add to Home Screen"');
        } else if (isAndroid) {
            instructions = __('To install: Tap the menu button in your browser, then "Add to Home Screen" or "Install App"');
        } else {
            instructions = __('To install: Look for the install button in your browser\'s address bar');
        }
        
        toast.info(instructions, { autoClose: 8000 });
    };

    const dismissInstallPrompt = () => {
        setShowInstallPrompt(false);
        localStorage.setItem('pwa-install-dismissed', 'true');
        
        // Show again after 7 days
        setTimeout(() => {
            localStorage.removeItem('pwa-install-dismissed');
        }, 7 * 24 * 60 * 60 * 1000);
    };

    // Don't show anything if already installed
    if (isInstalled) {
        return null;
    }

    return (
        <>
            {/* Install Prompt Banner */}
            {showInstallPrompt && (
                <div className="fixed bottom-0 left-0 right-0 z-50 bg-gradient-to-r from-purple-600 to-pink-600 text-white p-4 shadow-lg md:hidden">
                    <div className="flex items-center justify-between">
                        <div className="flex items-center flex-1">
                            <MdInstallMobile className="text-2xl mr-3" />
                            <div className="flex-1">
                                <h3 className="font-semibold text-sm">
                                    {__('Install Love248 App')}
                                </h3>
                                <p className="text-xs opacity-90">
                                    {__('Get the full experience with offline access')}
                                </p>
                            </div>
                        </div>
                        <div className="flex items-center space-x-2 ml-3">
                            <button
                                onClick={handleInstallClick}
                                className="bg-white text-purple-600 px-3 py-1 rounded-full text-sm font-medium hover:bg-gray-100 transition-colors"
                            >
                                {__('Install')}
                            </button>
                            <button
                                onClick={dismissInstallPrompt}
                                className="text-white/80 hover:text-white p-1"
                            >
                                <MdClose className="text-lg" />
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {/* Floating Install Button (for desktop) */}
            {deferredPrompt && !showInstallPrompt && (
                <div className="fixed bottom-6 right-6 z-40 hidden md:block">
                    <button
                        onClick={handleInstallClick}
                        className="bg-gradient-to-r from-purple-600 to-pink-600 text-white p-3 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105"
                        title={__('Install Love248 App')}
                    >
                        <MdDownload className="text-xl" />
                    </button>
                </div>
            )}
        </>
    );
} 