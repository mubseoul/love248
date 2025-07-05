<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MercadoAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('mercado_accounts')->insert([
            [
                'id' => 2,
                'user' => 176,
                'access_token' => 'TEST-3764253685425328-062121-216522203d99a3fed39e13c5ce05d8bb-1874911646',
                'expires_in' => '15552000',
                'scope' => 'offline_access read write',
                'user_id' => 1874911646,
                'refresh_token' => 'TG-6857559164d270000107e24a-1874911646',
                'public_key' => 'TEST-b9bd3db4-2dd0-446b-aff3-5229300e5179',
                'created_at' => '2025-06-22 01:00:01',
                'updated_at' => '2025-06-22 01:00:01'
            ]
        ]);
    }
} 