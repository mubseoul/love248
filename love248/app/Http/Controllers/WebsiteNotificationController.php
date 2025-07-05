<?php

namespace App\Http\Controllers;

use App\Services\WebsiteNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Exception;

class WebsiteNotificationController extends Controller
{
    protected $websiteNotificationService;

    public function __construct(WebsiteNotificationService $websiteNotificationService)
    {
        $this->websiteNotificationService = $websiteNotificationService;
    }

    /**
     * Send notification to all users
     */
    public function sendToAll(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'icon' => 'nullable|string',
            'url' => 'nullable|url',
            'require_interaction' => 'nullable|boolean',
            'actions' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid notification data',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $options = [
                'icon' => $request->input('icon'),
                'url' => $request->input('url'),
                'require_interaction' => $request->input('require_interaction', false),
                'actions' => $request->input('actions', []),
            ];

            $result = $this->websiteNotificationService->sendToAllUsers(
                $request->title,
                $request->message,
                array_filter($options) // Remove null values
            );

            return response()->json($result);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send notification',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send maintenance notification
     */
    public function sendMaintenanceNotification(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'scheduled_time' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid maintenance notification data',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $result = $this->websiteNotificationService->sendMaintenanceNotification(
                $request->scheduled_time
            );

            return response()->json($result);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send maintenance notification',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send feature announcement
     */
    public function sendFeatureAnnouncement(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'feature_name' => 'required|string|max:255',
            'description' => 'required|string',
            'url' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid feature announcement data',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $result = $this->websiteNotificationService->sendFeatureAnnouncement(
                $request->feature_name,
                $request->description,
                $request->url
            );

            return response()->json($result);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send feature announcement',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send security alert
     */
    public function sendSecurityAlert(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'alert_message' => 'required|string',
            'action_url' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid security alert data',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $result = $this->websiteNotificationService->sendSecurityAlert(
                $request->alert_message,
                $request->action_url
            );

            return response()->json($result);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send security alert',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send SMS notification
     */
    public function sendSMS(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid SMS data',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $success = $this->websiteNotificationService->sendSMS(
                $request->phone_number,
                $request->message
            );

            return response()->json([
                'success' => $success,
                'message' => $success ? 'SMS sent successfully' : 'Failed to send SMS',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send SMS',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send email notification
     */
    public function sendEmail(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email data',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $success = $this->websiteNotificationService->sendEmail(
                $request->subject,
                $request->message
            );

            return response()->json([
                'success' => $success,
                'message' => $success ? 'Email sent successfully' : 'Failed to send email',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Subscribe email to notifications
     */
    public function subscribeEmail(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $success = $this->websiteNotificationService->subscribeEmail($request->email);

            return response()->json([
                'success' => $success,
                'message' => $success ? 'Email subscribed successfully' : 'Failed to subscribe email',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to subscribe email',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Subscribe SMS to notifications
     */
    public function subscribeSMS(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid phone number',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $success = $this->websiteNotificationService->subscribeSMS($request->phone_number);

            return response()->json([
                'success' => $success,
                'message' => $success ? 'SMS subscribed successfully' : 'Failed to subscribe SMS',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to subscribe SMS',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get available topics
     */
    public function getTopics(): JsonResponse
    {
        try {
            $topics = $this->websiteNotificationService->getTopics();

            return response()->json([
                'success' => true,
                'topics' => $topics,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get topics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a new topic
     */
    public function createTopic(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid topic name',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $topicArn = $this->websiteNotificationService->createTopic($request->name);

            return response()->json([
                'success' => (bool) $topicArn,
                'message' => $topicArn ? 'Topic created successfully' : 'Failed to create topic',
                'topic_arn' => $topicArn,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create topic',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
