<?php

// ============================================
// FILE: database/seeders/TestEstateSeeder.php
// ============================================

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Estate;
use App\Models\Property;
use Illuminate\Support\Str;

class TestEstateSeeder extends Seeder
{
    public function run(): void
    {
        $estateAdmin = User::where('email', 'chairman@testestate.com')->first();
        $landlord = User::where('email', 'landlord@test.com')->first();

        if (!$estateAdmin || !$landlord) {
            echo "❌ Please run AdminUserSeeder first!\n";
            return;
        }

        // Create Test Estate
        $estate = Estate::create([
            'name' => 'Sunrise Gardens Estate',
            'uci' => 'EST-' . strtoupper(Str::random(8)),
            'address' => '123 Estate Road, Lekki Phase 1',
            'city' => 'Lagos',
            'state' => 'Lagos',
            'country' => 'Nigeria',
            'admin_id' => $estateAdmin->id,
            'subscription_status' => 'active',
            'subscription_starts_at' => now(),
            'subscription_expires_at' => now()->addYear(),
            'monthly_fee' => 50000.00,
            'is_active' => true,
            'description' => 'A modern residential estate with excellent amenities',
            'amenities' => json_encode([
                'Security 24/7',
                'Swimming Pool',
                'Gym',
                'Playground',
                'Shopping Complex',
                'Backup Generator'
            ]),
        ]);

        // Update users with estate_id
        $estateAdmin->update(['estate_id' => $estate->id]);
        $landlord->update(['estate_id' => $estate->id]);

        // Create Properties
        $properties = [
            [
                'estate_id' => $estate->id,
                'landlord_id' => $landlord->id,
                'property_number' => 'Block A, Flat 1',
                'street' => 'First Avenue',
                'property_type' => 'apartment',
                'bedrooms' => 3,
                'bathrooms' => 2,
                'rent_amount' => 1500000.00,
                'rent_period' => 'annually',
                'status' => 'vacant',
                'is_listed' => true,
                'description' => 'Spacious 3-bedroom apartment with modern finishes',
                'size_sqm' => 120.5,
                'features' => json_encode(['AC', 'Furnished', 'Balcony', 'Kitchen Cabinets']),
                'floor_number' => 1,
                'available_from' => now(),
            ],
            [
                'estate_id' => $estate->id,
                'landlord_id' => $landlord->id,
                'property_number' => 'Block A, Flat 2',
                'street' => 'First Avenue',
                'property_type' => 'apartment',
                'bedrooms' => 2,
                'bathrooms' => 2,
                'rent_amount' => 1200000.00,
                'rent_period' => 'annually',
                'status' => 'occupied',
                'is_listed' => false,
                'description' => 'Comfortable 2-bedroom apartment',
                'size_sqm' => 95.0,
                'features' => json_encode(['AC', 'Wardrobe', 'Kitchen Cabinets']),
                'floor_number' => 2,
                'available_from' => now()->addMonths(11),
            ],
            [
                'estate_id' => $estate->id,
                'landlord_id' => $landlord->id,
                'property_number' => 'Block B, Flat 5',
                'street' => 'Second Avenue',
                'property_type' => 'duplex',
                'bedrooms' => 4,
                'bathrooms' => 3,
                'rent_amount' => 2500000.00,
                'rent_period' => 'annually',
                'status' => 'vacant',
                'is_listed' => true,
                'description' => 'Luxury 4-bedroom duplex with study room',
                'size_sqm' => 180.0,
                'features' => json_encode(['AC', 'Fully Furnished', 'Study Room', 'Guest Toilet', 'Large Kitchen']),
                'floor_number' => null,
                'available_from' => now(),
            ],
        ];

        foreach ($properties as $propertyData) {
            Property::create($propertyData);
        }

        echo "✅ Test estate 'Sunrise Gardens Estate' created with 3 properties!\n";
    }
}
