<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Events\BrowserNotificationEvent;
use Exception;

class SNSWebhookController extends Controller
{
    public function handle(Request $request)
    {
        try {
            $headers = $request->headers->all();
            $body = $request->getContent();
            
            Log::info('SNS Webhook received', [
                'headers' => $headers,
                'body' => $body,
            ]);

            // Parse SNS message
            $snsMessage = json_decode($body, true);
            
            if (!$snsMessage) {
                Log::error('Invalid SNS message format');
                return response('Invalid message format', 400);
            }

            // Handle SNS subscription confirmation
            if (isset($snsMessage['Type']) && $snsMessage['Type'] === 'SubscriptionConfirmation') {
                $confirmUrl = $snsMessage['SubscribeURL'];
                
                // Confirm the subscription
                $response = file_get_contents($confirmUrl);
                
                Log::info('SNS subscription confirmed', [
                    'subscribeUrl' => $confirmUrl,
                    'response' => $response,
                ]);
                
                return response('Subscription confirmed', 200);
            }

            // Handle actual notification
            if (isset($snsMessage['Type']) && $snsMessage['Type'] === 'Notification') {
                $message = $snsMessage['Message'];
                $subject = $snsMessage['Subject'] ?? 'Notification';
                
                Log::info('Processing SNS notification', [
                    'subject' => $subject,
                    'message' => $message,
                ]);

                // Create notification payload from SNS message
                $payload = [
                    'title' => $subject,
                    'body' => $message,
                    'icon' => '/images/default-profile-pic.png',
                    'badge' => '/images/default-profile-pic.png',
                    'timestamp' => now()->toISOString(),
                ];

                // Trigger browser notification via Pusher
                broadcast(new BrowserNotificationEvent($payload));
                
                Log::info('Browser notification triggered via SNS webhook', [
                    'title' => $payload['title'],
                    'body' => $payload['body'],
                ]);
                
                return response('Notification processed', 200);
            }

            return response('Message type not handled', 200);
            
        } catch (Exception $e) {
            Log::error('SNS webhook error', [
                'error' => $e->getMessage(),
                'body' => $request->getContent(),
            ]);
            
            return response('Webhook error', 500);
        }
    }
} 