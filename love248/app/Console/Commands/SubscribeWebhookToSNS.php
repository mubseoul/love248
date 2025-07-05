<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Aws\Sns\SnsClient;
use Aws\Exception\AwsException;

class SubscribeWebhookToSNS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sns:subscribe-webhook {--delete-existing : Delete existing subscriptions first}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Subscribe webhook endpoint to SNS topic for browser notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $topicArn = config('aws-sns.topics.general');
        $webhookUrl = 'https://dev.premiumwork.com.br/sns-webhook';

        if (!$topicArn) {
            $this->error('❌ No SNS topic configured');
            return 1;
        }

        try {
            $snsClient = new SnsClient([
                'credentials' => [
                    'key' => config('aws-sns.credentials.key'),
                    'secret' => config('aws-sns.credentials.secret'),
                ],
                'region' => config('aws-sns.region'),
                'version' => 'latest',
            ]);

            // Delete existing subscriptions if requested
            if ($this->option('delete-existing')) {
                $this->info('🗑️  Deleting existing subscriptions...');
                
                $result = $snsClient->listSubscriptionsByTopic([
                    'TopicArn' => $topicArn
                ]);

                foreach ($result['Subscriptions'] as $subscription) {
                    if ($subscription['Protocol'] === 'https' && 
                        strpos($subscription['Endpoint'], 'sns-webhook') !== false) {
                        
                        if ($subscription['SubscriptionArn'] !== 'PendingConfirmation') {
                            $snsClient->unsubscribe([
                                'SubscriptionArn' => $subscription['SubscriptionArn']
                            ]);
                            $this->info("✅ Deleted subscription: {$subscription['Endpoint']}");
                        } else {
                            $this->info("⏭️  Skipping pending subscription: {$subscription['Endpoint']}");
                        }
                    }
                }
            }

            $this->info("📡 Subscribing webhook to SNS topic...");
            $this->info("Topic: {$topicArn}");
            $this->info("Webhook: {$webhookUrl}");
            $this->newLine();

            // Subscribe webhook to the topic
            $result = $snsClient->subscribe([
                'TopicArn' => $topicArn,
                'Protocol' => 'https',
                'Endpoint' => $webhookUrl,
            ]);

            $subscriptionArn = $result['SubscriptionArn'];

            $this->info("✅ Webhook subscribed successfully!");
            $this->info("Subscription ARN: {$subscriptionArn}");
            $this->newLine();

            if ($subscriptionArn === 'pending confirmation') {
                $this->warn("⚠️  Subscription is pending confirmation");
                $this->info("💡 AWS will send a confirmation message to your webhook");
                $this->info("💡 Check your Laravel logs to see the confirmation attempt");
                $this->info("💡 Run 'php artisan sns:check-subscriptions' to verify status");
            } else {
                $this->info("🎉 Subscription is confirmed and ready!");
            }

            return 0;

        } catch (AwsException $e) {
            $this->error("❌ AWS Error: " . $e->getMessage());
            return 1;
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }
    }
} 