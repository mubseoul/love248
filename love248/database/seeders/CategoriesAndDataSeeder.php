<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesAndDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insert categories
        DB::table('categories')->insert([
            ['id' => 29, 'category' => 'white', 'icon' => null],
            ['id' => 30, 'category' => 'land', 'icon' => null]
        ]);

        // Insert category_user relationships
        DB::table('category_user')->insert([
            ['id' => 108, 'category_id' => 29, 'user_id' => 176],
            ['id' => 109, 'category_id' => 30, 'user_id' => 179]
        ]);

        // Insert video categories
        DB::table('video_categories')->insert([
            ['id' => 1, 'category' => 'Fun'],
            ['id' => 3, 'category' => 'Sports'],
            ['id' => 4, 'category' => 'Personal'],
            ['id' => 5, 'category' => 'Other'],
            ['id' => 8, 'category' => 'Inspirational']
        ]);

        // Insert token packs
        DB::table('token_packs')->insert([
            ['id' => 1, 'name' => 'Starter', 'tokens' => 100, 'price' => 100],
            ['id' => 2, 'name' => 'Bronze', 'tokens' => 500, 'price' => 450],
            ['id' => 3, 'name' => 'Silver', 'tokens' => 750, 'price' => 500],
            ['id' => 4, 'name' => 'Gold', 'tokens' => 1000, 'price' => 850],
            ['id' => 5, 'name' => 'Platinum', 'tokens' => 5000, 'price' => 3900]
        ]);

        // Insert pages
        DB::table('pages')->insert([
            [
                'id' => 7,
                'page_title' => 'Terms of Service',
                'page_slug' => 'terms-of-service',
                'page_content' => '<p><strong><span style="font-size: 18pt;">Overview</span></strong></p>
<p>This website is operated by Your site name here. Throughout the site, the terms &ldquo;we&rdquo;, &ldquo;us&rdquo; and &ldquo;our&rdquo; refer to Your site name here. Your site name here offers this website, including all information, tools and services available from this site to you, the user, conditioned upon your acceptance of all terms, conditions, policies and notices stated here.</p>
<p>By visiting our site and/ or purchasing something from us, you engage in our &ldquo;Service&rdquo; and agree to be bound by the following terms and conditions (&ldquo;Terms of Service&rdquo;, &ldquo;Terms&rdquo;), including those additional terms and conditions and policies referenced herein and/or available by hyperlink. These Terms of Service apply&nbsp; to all users of the site, including without limitation users who are browsers, vendors, customers, merchants, and/ or contributors of content.</p>',
                'page_type' => null,
                'created_at' => '2022-11-16 11:10:31',
                'updated_at' => '2022-11-16 11:26:36'
            ],
            [
                'id' => 8,
                'page_title' => 'Privacy Policy',
                'page_slug' => 'privacy-policy',
                'page_content' => '<p>This Privacy Policy describes how your personal information is collected, used, and shared when you visit or make a purchase from https://your-domain.com (the \"Site\"). Continuing using this site means you agree to all of the mentions below.</p>
<p><strong><span style="font-size: 18pt;">Personal information we collect</span></strong></p>
<p>When you visit the Site, we automatically collect certain information about your device, including information about your web browser, IP address, time zone, and some of the cookies that are installed on your device. Additionally, as you browse the Site, we collect information about the individual web pages or products that you view, what websites or search terms referred you to the Site, and information about how you interact with the Site. We refer to this automatically-collected information as \"Device Information.\"</p>',
                'page_type' => null,
                'created_at' => '2022-11-17 12:19:41',
                'updated_at' => '2022-11-17 12:19:41'
            ]
        ]);

        // Insert user meta for streamer
        DB::table('user_meta')->insert([
            ['id' => 2, 'user_id' => 176, 'meta_key' => 'streaming_key', 'meta_value' => '176.mmdTrtQnqFdrDiuU']
        ]);
    }
}
