<?php

// ============================================
// FILE: database/seeders/RolesSeeder.php
// ============================================

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

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create Roles and Assign Permissions

        // Site Admin - Full access
        $siteAdmin = Role::create(['name' => 'site_admin']);
        $siteAdmin->givePermissionTo(Permission::all());

        // Estate Admin/Chairman
        $estateAdmin = Role::create(['name' => 'estate_admin']);
        $estateAdmin->givePermissionTo([
            'view estates', 'edit estates',
            'manage properties', 'view properties', 'create properties', 'edit properties', 'delete properties',
            'manage users', 'view users', 'create users', 'edit users',
            'manage tenants', 'view tenants', 'create tenants', 'edit tenants', 'delete tenants',
            'manage payments', 'view payments', 'create payments', 'approve payments',
            'manage announcements', 'view announcements', 'create announcements',
            'manage maintenance', 'view maintenance',
            'send messages', 'view messages',
            'view reports', 'generate reports',
        ]);

        // Landlord
        $landlord = Role::create(['name' => 'landlord']);
        $landlord->givePermissionTo([
            'view properties', 'edit properties',
            'view tenants', 'create tenants', 'edit tenants',
            'view payments',
            'view announcements',
            'view maintenance', 'manage maintenance',
            'send messages', 'view messages',
        ]);

        // Tenant
        $tenant = Role::create(['name' => 'tenant']);
        $tenant->givePermissionTo([
            'view properties',
            'view payments',
            'view announcements',
            'create maintenance', 'view maintenance',
            'send messages', 'view messages',
        ]);

        // Security
        $security = Role::create(['name' => 'security']);
        $security->givePermissionTo([
            'manage visitors',
            'check in visitors',
            'check out visitors',
            'view users',
        ]);

        // Agent
        $agent = Role::create(['name' => 'agent']);
        $agent->givePermissionTo([
            'create listings',
            'manage listings',
            'view agent earnings',
        ]);

        // Moderator
        $moderator = Role::create(['name' => 'moderator']);
        $moderator->givePermissionTo([
            'view estates',
            'view properties',
            'view users',
            'view announcements',
            'view reports',
        ]);

        // Regular User (just browsing platform)
        $user = Role::create(['name' => 'user']);
        $user->givePermissionTo([
            'view announcements',
        ]);
    }
}
