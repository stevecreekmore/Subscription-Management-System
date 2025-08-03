<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            'view subscriptions',
            'create subscriptions',
            'edit subscriptions',
            'delete subscriptions',
            'view companies',
            'create companies',
            'edit companies',
            'delete companies',
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view applications',
            'create applications',
            'edit applications',
            'delete applications',
            'view subscription plans',
            'create subscription plans',
            'edit subscription plans',
            'delete subscription plans',
            'view discount rules',
            'create discount rules',
            'edit discount rules',
            'delete discount rules',
            'view learning dependencies',
            'create learning dependencies',
            'edit learning dependencies',
            'delete learning dependencies',
            'view admin dashboard',
            'manage system settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Assign permissions to roles
        $adminRole->givePermissionTo(Permission::all());
        
        $userRole->givePermissionTo([
            'view subscriptions',
            'create subscriptions',
            'edit subscriptions',
        ]);
    }
}
