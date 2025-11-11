<?php
// ============================================
// FILE: database/seeders/AdminUserSeeder.php
// ============================================

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Site Admin
        $siteAdmin = User::create([
            'name' => 'Site Administrator',
            'email' => 'admin@estateplatform.com',
            'phone' => '+2348012345678',
            'password' => Hash::make('password123'),
            'role' => 'site_admin',
            'uci' => 'SA-' . strtoupper(Str::random(8)),
            'is_active' => true,
            'is_verified' => true,
            'verified_at' => now(),
            'email_verified_at' => now(),
        ]);
        $siteAdmin->assignRole('site_admin');

        // Create Test Estate Admin
        $estateAdmin = User::create([
            'name' => 'Estate Chairman',
            'email' => 'chairman@testestate.com',
            'phone' => '+2348023456789',
            'password' => Hash::make('password123'),
            'role' => 'estate_admin',
            'uci' => 'EA-' . strtoupper(Str::random(8)),
            'is_active' => true,
            'is_verified' => true,
            'verified_at' => now(),
            'email_verified_at' => now(),
        ]);
        $estateAdmin->assignRole('estate_admin');

        // Create Test Landlord
        $landlord = User::create([
            'name' => 'John Landlord',
            'email' => 'landlord@test.com',
            'phone' => '+2348034567890',
            'password' => Hash::make('password123'),
            'role' => 'landlord',
            'uci' => 'LL-' . strtoupper(Str::random(8)),
            'is_active' => true,
            'is_verified' => true,
            'verified_at' => now(),
            'email_verified_at' => now(),
        ]);
        $landlord->assignRole('landlord');

        // Create Test Tenant
        $tenant = User::create([
            'name' => 'Jane Tenant',
            'email' => 'tenant@test.com',
            'phone' => '+2348045678901',
            'password' => Hash::make('password123'),
            'role' => 'tenant',
            'uci' => 'TN-' . strtoupper(Str::random(8)),
            'is_active' => true,
            'is_verified' => true,
            'verified_at' => now(),
            'email_verified_at' => now(),
        ]);
        $tenant->assignRole('tenant');

        // Create Test Security
        $security = User::create([
            'name' => 'Security Officer',
            'email' => 'security@test.com',
            'phone' => '+2348056789012',
            'password' => Hash::make('password123'),
            'role' => 'security',
            'uci' => 'SE-' . strtoupper(Str::random(8)),
            'is_active' => true,
            'is_verified' => true,
            'verified_at' => now(),
            'email_verified_at' => now(),
        ]);
        $security->assignRole('security');

        // Create Test Agent
        $agent = User::create([
            'name' => 'Real Estate Agent',
            'email' => 'agent@test.com',
            'phone' => '+2348067890123',
            'password' => Hash::make('password123'),
            'role' => 'agent',
            'uci' => 'AG-' . strtoupper(Str::random(8)),
            'is_active' => true,
            'is_verified' => true,
            'verified_at' => now(),
            'email_verified_at' => now(),
        ]);
        $agent->assignRole('agent');

        echo "✅ Test users created successfully!\n";
        echo "📧 Admin: admin@estateplatform.com\n";
        echo "📧 Chairman: chairman@testestate.com\n";
        echo "📧 Landlord: landlord@test.com\n";
        echo "📧 Tenant: tenant@test.com\n";
        echo "📧 Security: security@test.com\n";
        echo "📧 Agent: agent@test.com\n";
        echo "🔑 Password for all: password123\n";
    }
}
