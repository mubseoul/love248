<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define permissions data
        $permissions = [
            ['id' => 1, 'name' => 'role-list', 'guard_name' => 'web'],
            ['id' => 2, 'name' => 'role-create', 'guard_name' => 'web'],
            ['id' => 3, 'name' => 'role-edit', 'guard_name' => 'web'],
            ['id' => 4, 'name' => 'role-delete', 'guard_name' => 'web'],
            ['id' => 5, 'name' => 'user-list', 'guard_name' => 'web'],
            ['id' => 6, 'name' => 'user-update', 'guard_name' => 'web'],
            ['id' => 7, 'name' => 'streamer-list', 'guard_name' => 'web'],
            ['id' => 8, 'name' => 'streamer-update', 'guard_name' => 'web'],
            ['id' => 9, 'name' => 'subscription-plan-sell-list', 'guard_name' => 'web'],
            ['id' => 10, 'name' => 'subscription-plan-sell-delete', 'guard_name' => 'web'],
            ['id' => 11, 'name' => 'subscription-plan-list', 'guard_name' => 'web'],
            ['id' => 12, 'name' => 'subscription-plan-create', 'guard_name' => 'web'],
            ['id' => 13, 'name' => 'subscription-plan-edit', 'guard_name' => 'web'],
            ['id' => 14, 'name' => 'subscription-plan-delete', 'guard_name' => 'web'],
            ['id' => 15, 'name' => 'videos-list', 'guard_name' => 'web'],
            ['id' => 16, 'name' => 'videos-edit', 'guard_name' => 'web'],
            ['id' => 17, 'name' => 'videos-delete', 'guard_name' => 'web'],
            ['id' => 18, 'name' => 'commission-list', 'guard_name' => 'web'],
            ['id' => 19, 'name' => 'streamer-catgory-list', 'guard_name' => 'web'],
            ['id' => 20, 'name' => 'streamer-catgory-create', 'guard_name' => 'web'],
            ['id' => 21, 'name' => 'streamer-catgory-edit', 'guard_name' => 'web'],
            ['id' => 22, 'name' => 'streamer-catgory-delete', 'guard_name' => 'web'],
            ['id' => 23, 'name' => 'video-catgory-list', 'guard_name' => 'web'],
            ['id' => 24, 'name' => 'video-catgory-create', 'guard_name' => 'web'],
            ['id' => 25, 'name' => 'video-catgory-edit', 'guard_name' => 'web'],
            ['id' => 26, 'name' => 'video-catgory-delete', 'guard_name' => 'web'],
            ['id' => 27, 'name' => 'stream-earning-list', 'guard_name' => 'web'],
            ['id' => 28, 'name' => 'video-sales-list', 'guard_name' => 'web'],
            ['id' => 29, 'name' => 'gallery-list', 'guard_name' => 'web'],
            ['id' => 30, 'name' => 'gallery-delete', 'guard_name' => 'web'],
            ['id' => 31, 'name' => 'gallery-sales-list', 'guard_name' => 'web'],
            ['id' => 32, 'name' => 'pages-manger-list', 'guard_name' => 'web'],
            ['id' => 33, 'name' => 'pages-manger-create', 'guard_name' => 'web'],
            ['id' => 34, 'name' => 'pages-manger-edit', 'guard_name' => 'web'],
            ['id' => 35, 'name' => 'pages-manger-delete', 'guard_name' => 'web'],
            ['id' => 36, 'name' => 'send-mails-list', 'guard_name' => 'web'],
            ['id' => 37, 'name' => 'send-mails-create', 'guard_name' => 'web'],
            ['id' => 38, 'name' => 'send-mails-edit', 'guard_name' => 'web'],
            ['id' => 39, 'name' => 'send-mails-delete', 'guard_name' => 'web'],
            ['id' => 40, 'name' => 'mail-configuration', 'guard_name' => 'web'],
            ['id' => 41, 'name' => 'cloud-storage', 'guard_name' => 'web'],
            ['id' => 42, 'name' => 'report-users', 'guard_name' => 'web'],
            ['id' => 43, 'name' => 'report-content', 'guard_name' => 'web'],
            ['id' => 44, 'name' => 'config-login', 'guard_name' => 'web'],
            ['id' => 45, 'name' => 'app-config', 'guard_name' => 'web'],
            ['id' => 46, 'name' => 'report-stream', 'guard_name' => 'web'],
            ['id' => 47, 'name' => 'stream-management-list', 'guard_name' => 'web'],
            ['id' => 48, 'name' => 'stream-management-view', 'guard_name' => 'web'],
            ['id' => 49, 'name' => 'stream-management-cancel', 'guard_name' => 'web'],
            ['id' => 50, 'name' => 'stream-management-interrupt', 'guard_name' => 'web'],
            ['id' => 51, 'name' => 'notification-management', 'guard_name' => 'web']
        ];

        // Insert or update permissions
        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['id' => $permission['id']],
                array_merge($permission, [
                    'created_at' => now(),
                    'updated_at' => now()
                ])
            );
        }

        // Define roles data
        $roles = [
            ['id' => 1, 'name' => 'admin', 'guard_name' => 'web'],
            ['id' => 2, 'name' => 'subadmin', 'guard_name' => 'web']
        ];

        // Insert or update roles
        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['id' => $role['id']],
                array_merge($role, [
                    'created_at' => now(),
                    'updated_at' => now()
                ])
            );
        }

        // Clear existing role permissions to avoid duplicates
        DB::table('role_has_permissions')->where('role_id', 1)->delete();
        DB::table('role_has_permissions')->where('role_id', 2)->delete();

        // Insert role_has_permissions for admin role (all permissions)
        $adminPermissions = [];
        for ($i = 1; $i <= 51; $i++) {
            $adminPermissions[] = ['permission_id' => $i, 'role_id' => 1];
        }
        DB::table('role_has_permissions')->insert($adminPermissions);

        // Insert role_has_permissions for subadmin role (limited permissions)
        DB::table('role_has_permissions')->insert([
            ['permission_id' => 5, 'role_id' => 2],   // user-list
            ['permission_id' => 6, 'role_id' => 2],   // user-update
            ['permission_id' => 20, 'role_id' => 2],  // streamer-catgory-create
            ['permission_id' => 34, 'role_id' => 2],  // pages-manger-edit
            ['permission_id' => 37, 'role_id' => 2],  // send-mails-create
            ['permission_id' => 42, 'role_id' => 2],  // report-users
            ['permission_id' => 43, 'role_id' => 2],  // report-content
            ['permission_id' => 46, 'role_id' => 2]   // report-stream
        ]);

        echo "Roles and permissions have been created/updated successfully!\n";
    }
}
