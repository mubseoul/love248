<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Aws\Sns\SnsClient;
use Aws\Exception\AwsException;

class ConfirmSNSSubscription extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sns:check-subscriptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and list all SNS topic subscriptions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $topicArn = config('aws-sns.topics.general');

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

            $this->info("📡 Checking subscriptions for topic: {$topicArn}");
            $this->newLine();

            // List all subscriptions for the topic
            $result = $snsClient->listSubscriptionsByTopic([
                'TopicArn' => $topicArn
            ]);

            $subscriptions = $result['Subscriptions'];

            if (empty($subscriptions)) {
                $this->warn('⚠️  No subscriptions found for this topic');
                return 0;
            }

            $this->info('📋 Found ' . count($subscriptions) . ' subscription(s):');
            $this->newLine();

            foreach ($subscriptions as $index => $subscription) {
                $this->info("#" . ($index + 1) . ":");
                $this->info("  Protocol: {$subscription['Protocol']}");
                $this->info("  Endpoint: {$subscription['Endpoint']}");
                $this->info("  Status: " . ($subscription['SubscriptionArn'] === 'PendingConfirmation' ? '🟡 Pending' : '✅ Confirmed'));
                $this->info("  ARN: {$subscription['SubscriptionArn']}");
                $this->newLine();
            }

            // Check for pending confirmations
            $pendingCount = 0;
            foreach ($subscriptions as $subscription) {
                if ($subscription['SubscriptionArn'] === 'PendingConfirmation') {
                    $pendingCount++;
                }
            }

            if ($pendingCount > 0) {
                $this->warn("⚠️  {$pendingCount} subscription(s) are pending confirmation");
                $this->info("💡 Troubleshooting steps:");
                $this->info("1. Check if your webhook URL is accessible: https://dev.premiumwork.com.br/sns-webhook");
                $this->info("2. Check Laravel logs for confirmation attempts");
                $this->info("3. Make sure your webhook returns 200 status for confirmation");
                $this->info("4. Try deleting and re-creating the subscription");
            } else {
                $this->info("✅ All subscriptions are confirmed!");
                $this->info("🎉 Your notifications should be working now!");
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