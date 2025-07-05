<?php

namespace App\Services;

use Aws\Sns\SnsClient;
use Aws\Exception\AwsException;
use Illuminate\Support\Facades\Log;
use Exception;

class PushNotificationService
{
    protected $snsClient;

    public function __construct()
    {
        $this->snsClient = new SnsClient([
            'credentials' => [
                'key' => config('aws-sns.credentials.key'),
                'secret' => config('aws-sns.credentials.secret'),
            ],
            'region' => config('aws-sns.region'),
            'version' => config('aws-sns.version'),
        ]);
    }

    /**
     * Send push notification to a topic (broadcast to all users)
     */
    public function sendToTopic(string $topicArn, string $title, string $message, array $options = []): bool
    {
        try {
            $payload = [
                'title' => $title,
                'body' => $message,
                'icon' => $options['icon'] ?? '/favicon.ico',
                'badge' => $options['badge'] ?? '/favicon.ico',
                'data' => [
                    'url' => $options['url'] ?? null,
                    'timestamp' => now()->toISOString(),
                ],
                'actions' => $options['actions'] ?? [],
                'requireInteraction' => $options['require_interaction'] ?? false,
                'silent' => $options['silent'] ?? false,
                'tag' => $options['tag'] ?? null,
            ];

            // Format message for different platforms
            $message = json_encode([
                'default' => $message,
                'GCM' => json_encode([
                    'data' => $payload,
                    'notification' => [
                        'title' => $title,
                        'body' => $message,
                        'icon' => $payload['icon'],
                    ],
                ]),
            ]);

            $this->snsClient->publish([
                'TopicArn' => $topicArn,
                'Message' => $message,
                'MessageStructure' => 'json',
            ]);

            return true;
        } catch (AwsException $e) {
            Log::error('Failed to send push notification to topic', [
                'error' => $e->getMessage(),
                'topic' => $topicArn,
                'title' => $title,
            ]);
            return false;
        }
    }

    /**
     * Send email notification
     */
    public function sendEmail(string $topicArn, string $subject, string $message): bool
    {
        try {
            $this->snsClient->publish([
                'TopicArn' => $topicArn,
                'Subject' => $subject,
                'Message' => $message,
            ]);

            return true;
        } catch (AwsException $e) {
            Log::error('Failed to send email notification', [
                'error' => $e->getMessage(),
                'topic' => $topicArn,
                'subject' => $subject,
            ]);
            return false;
        }
    }

    /**
     * Create a topic for notifications
     */
    public function createTopic(string $name): ?string
    {
        try {
            $result = $this->snsClient->createTopic([
                'Name' => $name,
            ]);

            return $result['TopicArn'];
        } catch (AwsException $e) {
            Log::error('Failed to create SNS topic', [
                'error' => $e->getMessage(),
                'name' => $name,
            ]);
            return null;
        }
    }

    /**
     * Subscribe email to topic
     */
    public function subscribeEmailToTopic(string $topicArn, string $email): bool
    {
        try {
            $this->snsClient->subscribe([
                'TopicArn' => $topicArn,
                'Protocol' => 'email',
                'Endpoint' => $email,
            ]);

            return true;
        } catch (AwsException $e) {
            Log::error('Failed to subscribe email to topic', [
                'error' => $e->getMessage(),
                'topic' => $topicArn,
                'email' => $email,
            ]);
            return false;
        }
    }

    /**
     * List topics
     */
    public function listTopics(): array
    {
        try {
            $result = $this->snsClient->listTopics();

            return array_map(function ($topic) {
                return $topic['TopicArn'];
            }, $result['Topics'] ?? []);
        } catch (AwsException $e) {
            Log::error('Failed to list SNS topics', [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Get topic attributes
     */
    public function getTopicAttributes(string $topicArn): ?array
    {
        try {
            $result = $this->snsClient->getTopicAttributes([
                'TopicArn' => $topicArn,
            ]);

            return $result['Attributes'];
        } catch (AwsException $e) {
            Log::error('Failed to get topic attributes', [
                'error' => $e->getMessage(),
                'topic' => $topicArn,
            ]);
            return null;
        }
    }


}
