<?php

namespace App\Http\Controllers;

use App\Services\WebsiteNotificationService;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class AdminNotificationController extends Controller
{
    protected $websiteNotificationService;
    protected $pushNotificationService;

    public function __construct(WebsiteNotificationService $websiteNotificationService, PushNotificationService $pushNotificationService)
    {
        $this->websiteNotificationService = $websiteNotificationService;
        $this->pushNotificationService = $pushNotificationService;
    }

    /**
     * Display SNS notification dashboard
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->hasRole('subadmin') && !$user->can('notification-management')) {
            return redirect('/admin')->with('msg', 'You do not have permission for this route!');
        }

        try {
            // Get available topics
            $topics = $this->websiteNotificationService->getTopics();
            
            // Get SNS configuration status
            $config = config('aws-sns');
            $hasCredentials = !empty($config['credentials']['key']) && !empty($config['credentials']['secret']);
            $hasTopics = !empty(array_filter($config['topics']));
            
            return view('admin.notifications.index', compact('topics', 'hasCredentials', 'hasTopics'))
                ->with('active', 'notifications');
        } catch (Exception $e) {
            Log::error('Failed to load notification dashboard', ['error' => $e->getMessage()]);
            return view('admin.notifications.index', [
                'topics' => [],
                'hasCredentials' => false,
                'hasTopics' => false,
                'error' => 'Failed to connect to SNS service'
            ])->with('active', 'notifications');
        }
    }

    /**
     * Show form to send broadcast notification
     */
    public function sendBroadcast()
    {
        $user = Auth::user();
        if ($user->hasRole('subadmin') && !$user->can('notification-management')) {
            return redirect('/admin')->with('msg', 'You do not have permission for this route!');
        }

        return view('admin.notifications.send-broadcast')->with('active', 'notifications');
    }

    /**
     * Process broadcast notification
     */
    public function processBroadcast(Request $request)
    {
        $user = Auth::user();
        if ($user->hasRole('subadmin') && !$user->can('notification-management')) {
            return redirect('/admin')->with('msg', 'You do not have permission for this route!');
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'icon' => 'nullable|string',
            'url' => 'nullable|url',
            'require_interaction' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $options = [
                'icon' => $request->input('icon'),
                'url' => $request->input('url'),
                'require_interaction' => $request->input('require_interaction', false),
            ];

            $result = $this->websiteNotificationService->sendToAllUsers(
                $request->title,
                $request->message,
                array_filter($options)
            );

            if ($result['success']) {
                return back()->with('success', 'Broadcast notification sent successfully!');
            } else {
                return back()->with('error', $result['message'])->withInput();
            }
        } catch (Exception $e) {
            Log::error('Failed to send broadcast notification', [
                'error' => $e->getMessage(),
                'title' => $request->title
            ]);
            return back()->with('error', 'Failed to send notification. Please check your SNS configuration.')->withInput();
        }
    }

    /**
     * Show maintenance notification form
     */
    public function maintenanceForm()
    {
        $user = Auth::user();
        if ($user->hasRole('subadmin') && !$user->can('notification-management')) {
            return redirect('/admin')->with('msg', 'You do not have permission for this route!');
        }

        return view('admin.notifications.maintenance')->with('active', 'notifications');
    }

    /**
     * Send maintenance notification
     */
    public function sendMaintenance(Request $request)
    {
        $user = Auth::user();
        if ($user->hasRole('subadmin') && !$user->can('notification-management')) {
            return redirect('/admin')->with('msg', 'You do not have permission for this route!');
        }

        $validator = Validator::make($request->all(), [
            'scheduled_time' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $result = $this->websiteNotificationService->sendMaintenanceNotification($request->scheduled_time);

            if ($result['success']) {
                return back()->with('success', 'Maintenance notification sent successfully!');
            } else {
                return back()->with('error', $result['message'])->withInput();
            }
        } catch (Exception $e) {
            Log::error('Failed to send maintenance notification', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to send maintenance notification.')->withInput();
        }
    }

    /**
     * Show security alert form
     */
    public function securityForm()
    {
        $user = Auth::user();
        if ($user->hasRole('subadmin') && !$user->can('notification-management')) {
            return redirect('/admin')->with('msg', 'You do not have permission for this route!');
        }

        return view('admin.notifications.security')->with('active', 'notifications');
    }

    /**
     * Send security alert
     */
    public function sendSecurity(Request $request)
    {
        $user = Auth::user();
        if ($user->hasRole('subadmin') && !$user->can('notification-management')) {
            return redirect('/admin')->with('msg', 'You do not have permission for this route!');
        }

        $validator = Validator::make($request->all(), [
            'alert_message' => 'required|string',
            'action_url' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $result = $this->websiteNotificationService->sendSecurityAlert(
                $request->alert_message,
                $request->action_url
            );

            if ($result['success']) {
                return back()->with('success', 'Security alert sent successfully!');
            } else {
                return back()->with('error', $result['message'])->withInput();
            }
        } catch (Exception $e) {
            Log::error('Failed to send security alert', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to send security alert.')->withInput();
        }
    }

    /**
     * Show topic management
     */
    public function topicManagement()
    {
        $user = Auth::user();
        if ($user->hasRole('subadmin') && !$user->can('notification-management')) {
            return redirect('/admin')->with('msg', 'You do not have permission for this route!');
        }

        try {
            $topics = $this->websiteNotificationService->getTopics();
            $config = config('aws-sns.topics');
            
            return view('admin.notifications.topics', compact('topics', 'config'))
                ->with('active', 'notifications');
        } catch (Exception $e) {
            Log::error('Failed to load topics', ['error' => $e->getMessage()]);
            return view('admin.notifications.topics', [
                'topics' => [],
                'config' => [],
                'error' => 'Failed to load topics'
            ])->with('active', 'notifications');
        }
    }

    /**
     * Create new topic
     */
    public function createTopic(Request $request)
    {
        $user = Auth::user();
        if ($user->hasRole('subadmin') && !$user->can('notification-management')) {
            return redirect('/admin')->with('msg', 'You do not have permission for this route!');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $topicArn = $this->websiteNotificationService->createTopic($request->name);

            if ($topicArn) {
                return back()->with('success', "Topic '{$request->name}' created successfully! ARN: {$topicArn}");
            } else {
                return back()->with('error', 'Failed to create topic.')->withInput();
            }
        } catch (Exception $e) {
            Log::error('Failed to create topic', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to create topic.')->withInput();
        }
    }

    /**
     * Show SNS configuration
     */
    public function configuration()
    {
        $user = Auth::user();
        if ($user->hasRole('subadmin') && !$user->can('notification-management')) {
            return redirect('/admin')->with('msg', 'You do not have permission for this route!');
        }

        $config = config('aws-sns');
        $envVars = [
            'AWS_ACCESS_KEY_ID' => env('AWS_ACCESS_KEY_ID'),
            'AWS_SECRET_ACCESS_KEY' => env('AWS_SECRET_ACCESS_KEY') ? '***' : null,
            'AWS_DEFAULT_REGION' => env('AWS_DEFAULT_REGION'),
            'AWS_SNS_GENERAL_TOPIC_ARN' => env('AWS_SNS_GENERAL_TOPIC_ARN'),
            'AWS_SNS_MAINTENANCE_TOPIC_ARN' => env('AWS_SNS_MAINTENANCE_TOPIC_ARN'),
            'AWS_SNS_SECURITY_TOPIC_ARN' => env('AWS_SNS_SECURITY_TOPIC_ARN'),
            'AWS_SNS_FEATURES_TOPIC_ARN' => env('AWS_SNS_FEATURES_TOPIC_ARN'),
            'AWS_SNS_EMAIL_TOPIC_ARN' => env('AWS_SNS_EMAIL_TOPIC_ARN'),
            'AWS_SNS_SMS_TOPIC_ARN' => env('AWS_SNS_SMS_TOPIC_ARN'),
        ];

        return view('admin.notifications.configuration', compact('config', 'envVars'))
            ->with('active', 'notifications');
    }

    /**
     * Test SNS connection
     */
    public function testConnection()
    {
        $user = Auth::user();
        if ($user->hasRole('subadmin') && !$user->can('notification-management')) {
            return response()->json(['success' => false, 'message' => 'Permission denied']);
        }

        try {
            $topics = $this->pushNotificationService->listTopics();
            
            return response()->json([
                'success' => true,
                'message' => 'SNS connection successful!',
                'topics_count' => count($topics)
            ]);
        } catch (Exception $e) {
            Log::error('SNS connection test failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'SNS connection failed: ' . $e->getMessage()
            ]);
        }
    }
} 