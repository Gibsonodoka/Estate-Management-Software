<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            // Estate Management
            'manage estates',
            'view estates',
            'create estates',
            'edit estates',
            'delete estates',

            // Property Management
            'manage properties',
            'view properties',
            'create properties',
            'edit properties',
            'delete properties',

            // User Management
            'manage users',
            'view users',
            'create users',
            'edit users',
            'delete users',

            // Tenant Management
            'manage tenants',
            'view tenants',
            'create tenants',
            'edit tenants',
            'delete tenants',

            // Payments
            'manage payments',
            'view payments',
            'create payments',
            'approve payments',

            // Announcements
            'manage announcements',
            'view announcements',
            'create announcements',

            // Maintenance
            'manage maintenance',
            'view maintenance',
            'create maintenance',

            // Security
            'manage visitors',
            'check in visitors',
            'check out visitors',

            // Messaging
            'send messages',
            'view messages',

            // Reports
            'view reports',
            'generate reports',

            // Agents
            'manage agents',
            'verify agents',
            'view agent earnings',

            // Listings
            'manage listings',
            'create listings',
            'feature listings',
        ];

        // Create permissions that don't already exist
        foreach ($permissions as $permission) {
            $permissionExists = Permission::where('name', $permission)->exists();

            if (!$permissionExists) {
                $this->command->info("Creating permission: {$permission}");
                Permission::create(['name' => $permission]);
            } else {
                $this->command->info("Permission already exists: {$permission}");
            }
        }

        // Define roles and their permissions
        $rolePermissions = [
            'site_admin' => Permission::all()->pluck('name')->toArray(),

            'estate_admin' => [
                'view estates', 'edit estates',
                'manage properties', 'view properties', 'create properties', 'edit properties', 'delete properties',
                'manage users', 'view users', 'create users', 'edit users',
                'manage tenants', 'view tenants', 'create tenants', 'edit tenants', 'delete tenants',
                'manage payments', 'view payments', 'create payments', 'approve payments',
                'manage announcements', 'view announcements', 'create announcements',
                'manage maintenance', 'view maintenance',
                'send messages', 'view messages',
                'view reports', 'generate reports',
            ],

            'landlord' => [
                'view properties', 'edit properties',
                'view tenants', 'create tenants', 'edit tenants',
                'view payments',
                'view announcements',
                'view maintenance', 'manage maintenance',
                'send messages', 'view messages',
            ],

            'tenant' => [
                'view properties',
                'view payments',
                'view announcements',
                'create maintenance', 'view maintenance',
                'send messages', 'view messages',
            ],

            'security' => [
                'manage visitors',
                'check in visitors',
                'check out visitors',
                'view users',
            ],

            'agent' => [
                'create listings',
                'manage listings',
                'view agent earnings',
            ],

            'moderator' => [
                'view estates',
                'view properties',
                'view users',
                'view announcements',
                'view reports',
            ],

            'user' => [
                'view announcements',
            ],
        ];

        // Create roles that don't already exist and assign permissions
        foreach ($rolePermissions as $roleName => $rolePerms) {
            $roleExists = Role::where('name', $roleName)->exists();

            if (!$roleExists) {
                $this->command->info("Creating role: {$roleName}");
                $role = Role::create(['name' => $roleName]);
                $role->givePermissionTo($rolePerms);
            } else {
                $this->command->info("Role already exists: {$roleName}");
                $role = Role::where('name', $roleName)->first();

                // Update existing role's permissions
                $this->command->info("Syncing permissions for: {$roleName}");
                $role->syncPermissions($rolePerms);
            }
        }

        $this->command->info("Roles and permissions seeded successfully!");
    }
}
