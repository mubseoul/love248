<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionLevelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update existing plans to have proper levels or create new ones
        $plans = [
            [
                'subscription_name' => 'Free Plan',
                'subscription_price' => '0.00',
                'days' => 30,
                'subscription_level' => 1,
                'details' => '<ul><li>Access to public content only</li><li>Browse public streams</li><li>Basic profile features</li></ul>',
                'status' => 1,
            ],
            [
                'subscription_name' => 'Premium',
                'subscription_price' => '9.99',
                'days' => 30,
                'subscription_level' => 2,
                'details' => '<ul><li>All Free features</li><li>Access to private rooms</li><li>Create proposals for private streams</li><li>Upload and manage media gallery</li><li>Priority customer support</li></ul>',
                'status' => 1,
            ],
            [
                'subscription_name' => 'Boosted',
                'subscription_price' => '19.99',
                'days' => 30,
                'subscription_level' => 3,
                'details' => '<ul><li>All Premium features</li><li>Profile highlighting ⭐</li><li>Search priority ranking</li><li>VIP badge</li><li>Exclusive content access</li><li>Direct creator messaging</li></ul>',
                'status' => 1,
            ]
        ];

        foreach ($plans as $planData) {
            SubscriptionPlan::updateOrCreate(
                ['subscription_name' => $planData['subscription_name']],
                $planData
            );
        }

        $this->command->info('Subscription plans with levels have been created/updated successfully!');
    }
}
