<?php

namespace App\Services;

use App\Services\PushNotificationService;
use Illuminate\Support\Facades\Log;
use Exception;

class WebsiteNotificationService
{
    protected $pushService;

    public function __construct(PushNotificationService $pushService)
    {
        $this->pushService = $pushService;
    }

    /**
     * Send notification to all users via AWS SNS topic
     */
    public function sendToAllUsers(string $title, string $message, array $options = []): array
    {
        try {
            $topicArn = config('aws-sns.topics.general');

            if (!$topicArn) {
                return [
                    'success' => false,
                    'message' => 'No SNS topic configured',
                ];
            }

            // Send to AWS SNS topic (this will trigger webhooks)
            $success = $this->pushService->sendToTopic($topicArn, $title, $message, $options);

            if ($success) {
                Log::info('SNS notification sent to topic', [
                    'title' => $title,
                    'topic' => $topicArn,
                ]);

                return [
                    'success' => true,
                    'message' => 'Notification sent to SNS topic successfully',
                    'topic' => $topicArn,
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to send notification to SNS topic',
                ];
            }
        } catch (Exception $e) {
            Log::error('Failed to send SNS notification', [
                'error' => $e->getMessage(),
                'title' => $title,
                'message' => $message,
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send notification',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send maintenance notification
     */
    public function sendMaintenanceNotification(string $scheduledTime): array
    {
        $title = 'Scheduled Maintenance';
        $message = "We'll be performing maintenance on {$scheduledTime}. Service may be briefly interrupted.";

        $options = [
            'icon' => '/icons/maintenance.png',
            'require_interaction' => true,
            'actions' => [
                [
                    'action' => 'view',
                    'title' => 'Learn More',
                ],
            ],
        ];

        return $this->sendToAllUsers($title, $message, $options);
    }

    /**
     * Send new feature announcement
     */
    public function sendFeatureAnnouncement(string $featureName, string $description, string $url = null): array
    {
        $title = "New Feature: {$featureName}";
        $message = $description;

        $options = [
            'icon' => '/icons/feature.png',
            'url' => $url,
            'actions' => [
                [
                    'action' => 'view',
                    'title' => 'Check it out',
                ],
            ],
        ];

        return $this->sendToAllUsers($title, $message, $options);
    }

    /**
     * Send security alert notification
     */
    public function sendSecurityAlert(string $alertMessage, string $actionUrl = null): array
    {
        $title = 'Security Alert';
        $message = $alertMessage;

        $options = [
            'icon' => '/icons/security.png',
            'url' => $actionUrl,
            'require_interaction' => true,
            'actions' => [
                [
                    'action' => 'view',
                    'title' => 'Take Action',
                ],
            ],
        ];

        return $this->sendToAllUsers($title, $message, $options);
    }

    /**
     * Send email notification
     */
    public function sendEmail(string $subject, string $message): bool
    {
        $topicArn = config('aws-sns.topics.email');

        if (!$topicArn) {
            Log::error('No email topic configured');
            return false;
        }

        return $this->pushService->sendEmail($topicArn, $subject, $message);
    }

    /**
     * Subscribe email to notifications
     */
    public function subscribeEmail(string $email): bool
    {
        $topicArn = config('aws-sns.topics.email');

        if (!$topicArn) {
            Log::error('No email topic configured');
            return false;
        }

        return $this->pushService->subscribeEmailToTopic($topicArn, $email);
    }

    /**
     * Get available topics
     */
    public function getTopics(): array
    {
        return $this->pushService->listTopics();
    }

    /**
     * Create a new topic
     */
    public function createTopic(string $name): ?string
    {
        return $this->pushService->createTopic($name);
    }
}
