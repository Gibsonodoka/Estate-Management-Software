<?php

// ============================================
// FILE: database/migrations/xxxx_add_custom_fields_to_users_table.php
// Replace xxxx with your timestamp
// ============================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Check if columns don't exist before adding
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['user', 'landlord', 'tenant', 'estate_admin', 'security', 'agent', 'site_admin', 'moderator'])
                    ->default('user')->after('password');
            }
            if (!Schema::hasColumn('users', 'uci')) {
                $table->string('uci')->unique()->nullable()->after('role');
            }
            if (!Schema::hasColumn('users', 'estate_id')) {
                $table->unsignedBigInteger('estate_id')->nullable()->after('uci');
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('estate_id');
            }
            if (!Schema::hasColumn('users', 'is_verified')) {
                $table->boolean('is_verified')->default(false)->after('is_active');
            }
            if (!Schema::hasColumn('users', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('is_verified');
            }
            if (!Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone', 'role', 'uci', 'estate_id',
                'is_active', 'is_verified', 'verified_at', 'deleted_at'
            ]);
        });
    }
};
