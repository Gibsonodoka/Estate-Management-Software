<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('properties', function (Blueprint $table) {
            // Only add columns if they don't exist
            if (!Schema::hasColumn('properties', 'property_name')) {
                $table->string('property_name')->after('landlord_id');
            }
            if (!Schema::hasColumn('properties', 'units')) {
                $table->integer('units')->default(1)->after('property_type');
            }
            if (!Schema::hasColumn('properties', 'bedrooms_per_unit')) {
                $table->integer('bedrooms_per_unit')->nullable()->after('units');
            }
            if (!Schema::hasColumn('properties', 'bathrooms_per_unit')) {
                $table->integer('bathrooms_per_unit')->nullable()->after('bedrooms_per_unit');
            }
            if (!Schema::hasColumn('properties', 'size_unit')) {
                $table->string('size_unit')->default('sqm')->after('size_sqm');
            }
            if (!Schema::hasColumn('properties', 'street_name')) {
                $table->string('street_name')->nullable()->after('street');
            }
            if (!Schema::hasColumn('properties', 'street_number')) {
                $table->string('street_number')->nullable()->after('street_name');
            }
            if (!Schema::hasColumn('properties', 'rent_amount_per_unit')) {
                $table->decimal('rent_amount_per_unit', 12, 2)->after('street_number');
            }
            if (!Schema::hasColumn('properties', 'utilities_included')) {
                $table->json('utilities_included')->nullable()->after('description');
            }
        });

        // Rename columns only if not already renamed
        if (Schema::hasColumn('properties', 'property_number') && !Schema::hasColumn('properties', 'old_property_number')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->renameColumn('property_number', 'old_property_number');
            });
        }

        if (Schema::hasColumn('properties', 'bedrooms') && !Schema::hasColumn('properties', 'old_bedrooms')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->renameColumn('bedrooms', 'old_bedrooms');
            });
        }

        if (Schema::hasColumn('properties', 'bathrooms') && !Schema::hasColumn('properties', 'old_bathrooms')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->renameColumn('bathrooms', 'old_bathrooms');
            });
        }

        if (Schema::hasColumn('properties', 'rent_amount') && !Schema::hasColumn('properties', 'old_rent_amount')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->renameColumn('rent_amount', 'old_rent_amount');
            });
        }

        // Update enums
        DB::statement("ALTER TABLE properties MODIFY status ENUM('available','occupied','vacant','maintenance','reserved') DEFAULT 'available'");
        DB::statement("ALTER TABLE properties MODIFY rent_period ENUM('monthly','quarterly','bi-annually','annually') DEFAULT 'monthly'");
    }

    public function down()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn([
                'property_name',
                'units',
                'bedrooms_per_unit',
                'bathrooms_per_unit',
                'size_unit',
                'street_name',
                'street_number',
                'rent_amount_per_unit',
                'utilities_included'
            ]);
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->renameColumn('old_property_number', 'property_number');
            $table->renameColumn('old_bedrooms', 'bedrooms');
            $table->renameColumn('old_bathrooms', 'bathrooms');
            $table->renameColumn('old_rent_amount', 'rent_amount');
        });
    }
};
