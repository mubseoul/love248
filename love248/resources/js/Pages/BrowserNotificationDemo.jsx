import React from 'react';
import { Head } from '@inertiajs/inertia-react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import BrowserNotifications from '@/Components/BrowserNotifications';

const BrowserNotificationDemo = ({ auth }) => {
    return (
        <AuthenticatedLayout auth={auth}>
            <Head title="AWS SNS Browser Notifications" />
            
            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <div className="mb-8">
                                <h1 className="text-3xl font-bold mb-4">AWS SNS Browser Notifications</h1>
                                <p className="text-gray-600 dark:text-gray-400">
                                    This page demonstrates browser notifications using your existing AWS SNS infrastructure.
                                    No additional setup required - notifications work through your configured SNS topics.
                                </p>
                            </div>

                            {/* Browser Notifications Component */}
                            <div className="mb-8">
                                <BrowserNotifications />
                            </div>

                            {/* How it Works */}
                            <div className="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-8">
                                <h2 className="text-xl font-semibold mb-4">How AWS SNS Browser Notifications Work</h2>
                                <div className="space-y-4 text-sm text-gray-600 dark:text-gray-400">
                                    <div className="flex items-start space-x-3">
                                        <span className="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium">1</span>
                                        <p>
                                            <strong>Service Worker Registration:</strong> A service worker is registered 
                                            to handle push notifications from AWS SNS.
                                        </p>
                                    </div>
                                    <div className="flex items-start space-x-3">
                                        <span className="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium">2</span>
                                        <p>
                                            <strong>Permission Request:</strong> The browser asks for permission 
                                            to show notifications.
                                        </p>
                                    </div>
                                    <div className="flex items-start space-x-3">
                                        <span className="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium">3</span>
                                        <p>
                                            <strong>AWS SNS Integration:</strong> Your existing SNS topics can now 
                                            send notifications directly to browsers.
                                        </p>
                                    </div>
                                    <div className="flex items-start space-x-3">
                                        <span className="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium">4</span>
                                        <p>
                                            <strong>No Additional Setup:</strong> Uses your existing AWS SNS configuration 
                                            - no VAPID keys or additional services needed.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {/* Admin Controls */}
                            <div className="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-8">
                                <h2 className="text-xl font-semibold mb-4">Admin Controls</h2>
                                <p className="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                    Administrators can send notifications through the existing admin panel 
                                    at <code className="bg-gray-200 dark:bg-gray-600 px-2 py-1 rounded">/admin/notifications</code>
                                </p>
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div className="bg-white dark:bg-gray-600 p-4 rounded border">
                                        <h3 className="font-medium mb-2">Broadcast Notifications</h3>
                                        <p className="text-sm text-gray-600 dark:text-gray-400">
                                            Send notifications to all users via your configured SNS general topic.
                                        </p>
                                    </div>
                                    <div className="bg-white dark:bg-gray-600 p-4 rounded border">
                                        <h3 className="font-medium mb-2">Maintenance Alerts</h3>
                                        <p className="text-sm text-gray-600 dark:text-gray-400">
                                            Notify users about scheduled maintenance and service interruptions.
                                        </p>
                                    </div>
                                    <div className="bg-white dark:bg-gray-600 p-4 rounded border">
                                        <h3 className="font-medium mb-2">Feature Announcements</h3>
                                        <p className="text-sm text-gray-600 dark:text-gray-400">
                                            Announce new features and updates to your users.
                                        </p>
                                    </div>
                                    <div className="bg-white dark:bg-gray-600 p-4 rounded border">
                                        <h3 className="font-medium mb-2">Security Alerts</h3>
                                        <p className="text-sm text-gray-600 dark:text-gray-400">
                                            Send important security notifications that require user attention.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {/* Technical Details */}
                            <div className="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                <h2 className="text-xl font-semibold mb-4">Technical Implementation</h2>
                                <div className="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                                    <div>
                                        <strong>Service Worker:</strong> <code>/sw.js</code> handles incoming push notifications
                                    </div>
                                    <div>
                                        <strong>Integration:</strong> Uses existing AWS SNS topics and configuration
                                    </div>
                                    <div>
                                        <strong>Notifications:</strong> Delivered through browser's native notification system
                                    </div>
                                    <div>
                                        <strong>Compatibility:</strong> Works with Chrome, Firefox, Safari, and Edge
                                    </div>
                                    <div>
                                        <strong>Requirements:</strong> HTTPS enabled, modern browser, user permission
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
};

export default BrowserNotificationDemo; 