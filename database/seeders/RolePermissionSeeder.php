<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define base permissions for the admin panel
        $permissions = [
            'manage_users',
            'manage_applicants',
            'manage_team',
            'manage_jobs',     // categories, Sub-Categories, Job Posts
            'manage_operations', // rate card, venue, shifts, staff quotation, events, time shifting
            'manage_settings',
            'manage_roles',    // SUPER POWER: can create and edit roles/admins
        ];

        // Create or find permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Master Admin role and assign all permissions
        $masterAdminRole = Role::firstOrCreate(['name' => 'Master Admin']);
        $masterAdminRole->syncPermissions($permissions);

        // Optional: Create Sub Admin roles for demonstration
        $hrRole = Role::firstOrCreate(['name' => 'Manager']);
        $hrRole->syncPermissions(['manage_applicants', 'manage_team', 'manage_jobs']);

        // Find the main admin user (assuming ID 1 or the very first user with 'admin' role)
        // Adjust logic if needed based on the user's DB. We'll pick the first admin.
        $adminUsers = User::where('role', 'admin')->get();
        foreach ($adminUsers as $admin) {
            // Give Master Admin role to all existing admins so they don't lose access
            $admin->assignRole('Master Admin');
        }
    }
}
