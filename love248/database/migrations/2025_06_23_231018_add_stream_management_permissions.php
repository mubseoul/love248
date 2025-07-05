<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create the new stream management permissions
        $permissions = [
            'stream-management-list',
            'stream-management-view',
            'stream-management-cancel',
            'stream-management-interrupt'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        // Assign all new permissions to admin role
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove the permissions
        $permissions = [
            'stream-management-list',
            'stream-management-view',
            'stream-management-cancel',
            'stream-management-interrupt'
        ];

        foreach ($permissions as $permission) {
            Permission::where('name', $permission)->delete();
        }
    }
};
