import { useState, useEffect } from 'react';
import __ from '@/Functions/Translate';

export default function PWAStatus({ showDetails = false }) {
    const [pwaStatus, setPwaStatus] = useState({
        isInstalled: false,
        isStandalone: false,
        hasServiceWorker: false,
        isOnline: navigator.onLine,
        installPromptAvailable: false
    });

    useEffect(() => {
        // Check PWA status
        const checkPWAStatus = () => {
            const status = {
                isInstalled: window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone,
                isStandalone: window.matchMedia('(display-mode: standalone)').matches,
                hasServiceWorker: 'serviceWorker' in navigator,
                isOnline: navigator.onLine,
                installPromptAvailable: false
            };
            
            setPwaStatus(status);
        };

        checkPWAStatus();

        // Listen for online/offline events
        const handleOnline = () => setPwaStatus(prev => ({ ...prev, isOnline: true }));
        const handleOffline = () => setPwaStatus(prev => ({ ...prev, isOnline: false }));
        
        window.addEventListener('online', handleOnline);
        window.addEventListener('offline', handleOffline);

        // Listen for install prompt
        const handleBeforeInstallPrompt = () => {
            setPwaStatus(prev => ({ ...prev, installPromptAvailable: true }));
        };

        window.addEventListener('beforeinstallprompt', handleBeforeInstallPrompt);

        return () => {
            window.removeEventListener('online', handleOnline);
            window.removeEventListener('offline', handleOffline);
            window.removeEventListener('beforeinstallprompt', handleBeforeInstallPrompt);
        };
    }, []);

    // Don't show anything in production unless explicitly requested
    if (process.env.NODE_ENV === 'production' && !showDetails) {
        return null;
    }

    const getStatusIcon = (status) => status ? '✅' : '❌';
    const getStatusColor = (status) => status ? 'text-green-400' : 'text-red-400';

    return (
        <div className="fixed top-4 left-4 z-50 bg-black/80 text-white p-3 rounded-lg text-xs font-mono max-w-xs">
            <div className="font-bold mb-2">PWA Status</div>
            
            <div className="space-y-1">
                <div className={`flex items-center justify-between ${getStatusColor(pwaStatus.isInstalled)}`}>
                    <span>Installed:</span>
                    <span>{getStatusIcon(pwaStatus.isInstalled)}</span>
                </div>
                
                <div className={`flex items-center justify-between ${getStatusColor(pwaStatus.isStandalone)}`}>
                    <span>Standalone:</span>
                    <span>{getStatusIcon(pwaStatus.isStandalone)}</span>
                </div>
                
                <div className={`flex items-center justify-between ${getStatusColor(pwaStatus.hasServiceWorker)}`}>
                    <span>Service Worker:</span>
                    <span>{getStatusIcon(pwaStatus.hasServiceWorker)}</span>
                </div>
                
                <div className={`flex items-center justify-between ${getStatusColor(pwaStatus.isOnline)}`}>
                    <span>Online:</span>
                    <span>{getStatusIcon(pwaStatus.isOnline)}</span>
                </div>
                
                <div className={`flex items-center justify-between ${getStatusColor(pwaStatus.installPromptAvailable)}`}>
                    <span>Install Prompt:</span>
                    <span>{getStatusIcon(pwaStatus.installPromptAvailable)}</span>
                </div>
            </div>

            {showDetails && (
                <div className="mt-3 pt-2 border-t border-gray-600">
                    <div className="text-gray-400">
                        <div>UA: {navigator.userAgent.includes('Mobile') ? 'Mobile' : 'Desktop'}</div>
                        <div>Display: {window.matchMedia('(display-mode: standalone)').matches ? 'Standalone' : 'Browser'}</div>
                    </div>
                </div>
            )}
        </div>
    );
} 