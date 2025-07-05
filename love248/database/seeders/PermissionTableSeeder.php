<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
           'role-list',
           'role-create',
           'role-edit',
           'role-delete',
           'user-list',
           'user-update',
           'streamer-list',
           'streamer-update',
           'subscription-plan-sell-list',
           'subscription-plan-sell-delete',
           'subscription-plan-list',
           'subscription-plan-create',
           'subscription-plan-edit',
           'subscription-plan-delete',
           'videos-list',
           'videos-edit',
           'videos-delete',
           'commission-list',
           'streamer-catgory-list',
           'streamer-catgory-create',
           'streamer-catgory-edit',
           'streamer-catgory-delete',
           'video-catgory-list',
           'video-catgory-create',
           'video-catgory-edit',
           'video-catgory-delete',
           'stream-earning-list',
           'stream-management-list',
           'stream-management-view',
           'stream-management-cancel',
           'stream-management-interrupt',
           'video-sales-list',
           'gallery-list',
           'gallery-delete',
           'gallery-sales-list',
           'pages-manger-list',
           'pages-manger-create',
           'pages-manger-edit',
           'pages-manger-delete',
           'send-mails-list',
           'send-mails-create',
           'send-mails-edit',
           'send-mails-delete',
           'mail-configuration',
           'cloud-storage',
           'report-users',
           'report-content',
           'config-login',
           'app-config',
        ];

        

        foreach ($permissions as $permission) {
             Permission::create(['name' => $permission]);
        }
    }
}
