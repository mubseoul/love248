<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create the notification-management permission
        $permission = Permission::firstOrCreate([
            'name' => 'notification-management',
            'guard_name' => 'web'
        ]);

        // Get the admin role
        $adminRole = Role::where('name', 'admin')->first();
        
        if ($adminRole) {
            // Assign the permission to admin role
            $adminRole->givePermissionTo('notification-management');
            
            // Also add to the role_has_permissions table directly for consistency with the seeder
            $maxPermissionId = DB::table('permissions')->max('id');
            $newPermissionId = $permission->id;
            
            // Update the RolesAndPermissionsSeeder array to include this permission
            DB::table('role_has_permissions')->updateOrInsert(
                ['permission_id' => $newPermissionId, 'role_id' => 1],
                ['permission_id' => $newPermissionId, 'role_id' => 1]
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove the permission
        Permission::where('name', 'notification-management')->delete();
    }
}; 