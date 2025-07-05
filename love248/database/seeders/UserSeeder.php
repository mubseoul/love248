<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'id' => 129,
                'username' => 'Twitcher Admin',
                'name' => 'TheAdmin',
                'email' => 'trsrufino@gmail.com',
                'skin_tone' => null,
                'dob' => null,
                'email_verified_at' => null,
                'password' => '$2y$10$L5WG71fq.QPMAXX.ILTCCuSL1.ZaqVYZ13V.u.FnJ1DZ2j778.U4a',
                'profile_picture' => null,
                'cover_picture' => null,
                'headline' => null,
                'about' => null,
                'tokens' => 7.50,
                'is_streamer' => 'no',
                'is_streamer_verified' => 'no',
                'streamer_verification_sent' => 'no',
                'live_status' => 'offline',
                'popularity' => 0,
                'is_admin' => 'yes',
                'is_supper_admin' => 'yes',
                'ip' => '103.84.165.38',
                'remember_token' => null,
                'stripe_payment_method_id' => null,
                'stripe_customer_id' => null,
                'message_video' => null,
                'whatsapp_number' => null,
                'created_at' => '2024-10-16 17:55:52',
                'updated_at' => '2025-05-23 15:47:47'
            ],
            [
                'id' => 176,
                'username' => 'streamer',
                'name' => 'streamer',
                'email' => 'streamer@gmail.com',
                'skin_tone' => 'medium skin',
                'dob' => '2000-10-02',
                'email_verified_at' => null,
                'password' => '$2y$10$Xi9DmZjVobywFwRQdc0ome/Bev1ZGAzsvnXnub9a5gaH2x/nUTaaO',
                'profile_picture' => 'profilePics/176-680c3384cee01.jpeg',
                'cover_picture' => null,
                'headline' => 'I m test streamer',
                'about' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown prin',
                'tokens' => 208.50,
                'is_streamer' => 'yes',
                'is_streamer_verified' => 'yes',
                'streamer_verification_sent' => 'yes',
                'live_status' => 'online',
                'popularity' => 629,
                'is_admin' => 'no',
                'is_supper_admin' => 'no',
                'ip' => '127.0.0.1',
                'remember_token' => null,
                'stripe_payment_method_id' => null,
                'stripe_customer_id' => null,
                'message_video' => 'users/176/message/6817c6a70399a.mp4',
                'whatsapp_number' => null,
                'created_at' => '2025-04-24 19:18:46',
                'updated_at' => '2025-06-15 19:18:32'
            ],
            [
                'id' => 177,
                'username' => 'normaluser',
                'name' => 'Dr User',
                'email' => 'normaluser@gmail.com',
                'skin_tone' => 'medium skin',
                'dob' => '1995-05-09',
                'email_verified_at' => null,
                'password' => '$2y$10$MaK6pgIuB6o6yKXOjdA5Yuj/Gxth.RvMCWNafgH/lVvI/jrg3rauC',
                'profile_picture' => null,
                'cover_picture' => null,
                'headline' => null,
                'about' => null,
                'tokens' => 612.00,
                'is_streamer' => 'no',
                'is_streamer_verified' => 'no',
                'streamer_verification_sent' => 'no',
                'live_status' => 'offline',
                'popularity' => 0,
                'is_admin' => 'no',
                'is_supper_admin' => 'no',
                'ip' => '127.0.0.1',
                'remember_token' => null,
                'stripe_payment_method_id' => 'pm_1RLWB7JN9OqqM6ftd6e7ZPnf',
                'stripe_customer_id' => 'cus_SG2F7MOMUmSpRa',
                'message_video' => null,
                'whatsapp_number' => null,
                'created_at' => '2025-04-30 15:22:45',
                'updated_at' => '2025-06-11 17:12:35'
            ],
            [
                'id' => 178,
                'username' => 'thiago',
                'name' => 'thiago',
                'email' => 'thiago@gmail.com',
                'skin_tone' => 'medium skin',
                'dob' => '1995-02-02',
                'email_verified_at' => null,
                'password' => '$2y$10$rIP28GT63tvkwkLrHLjM8OTyisYgmZgeZWXXbLafwDJL0OLP..GCO',
                'profile_picture' => null,
                'cover_picture' => null,
                'headline' => null,
                'about' => null,
                'tokens' => 0.00,
                'is_streamer' => 'no',
                'is_streamer_verified' => 'no',
                'streamer_verification_sent' => 'no',
                'live_status' => 'offline',
                'popularity' => 0,
                'is_admin' => 'no',
                'is_supper_admin' => 'no',
                'ip' => '127.0.0.1',
                'remember_token' => null,
                'stripe_payment_method_id' => null,
                'stripe_customer_id' => null,
                'message_video' => null,
                'whatsapp_number' => null,
                'created_at' => '2025-06-04 05:36:03',
                'updated_at' => '2025-06-04 05:36:03'
            ],
            [
                'id' => 179,
                'username' => 'thiagostreamer',
                'name' => 'thiago streamer',
                'email' => 'newskrill05@gmail.com',
                'skin_tone' => 'black skin',
                'dob' => '2000-12-02',
                'email_verified_at' => null,
                'password' => '$2y$10$JDkdgFZgNeaQogKR6UixQu3RwMXK7KzDEuQSZy6vBIHVPMO95w06i',
                'profile_picture' => null,
                'cover_picture' => null,
                'headline' => null,
                'about' => null,
                'tokens' => 0.00,
                'is_streamer' => 'yes',
                'is_streamer_verified' => 'yes',
                'streamer_verification_sent' => 'yes',
                'live_status' => 'offline',
                'popularity' => 2,
                'is_admin' => 'no',
                'is_supper_admin' => 'no',
                'ip' => '127.0.0.1',
                'remember_token' => null,
                'stripe_payment_method_id' => null,
                'stripe_customer_id' => null,
                'message_video' => null,
                'whatsapp_number' => null,
                'created_at' => '2025-06-04 07:03:27',
                'updated_at' => '2025-06-04 07:05:17'
            ]
        ]);

        // Assign admin role to the admin user
        DB::table('model_has_roles')->insert([
            'role_id' => 1,
            'model_type' => 'App\\Models\\User',
            'model_id' => 129
        ]);
    }
}
