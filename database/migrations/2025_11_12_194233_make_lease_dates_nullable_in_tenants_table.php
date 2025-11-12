<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            // Make lease dates nullable
            $table->date('lease_start_date')->nullable()->change();
            $table->date('lease_end_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            // Revert back to non-nullable
            $table->date('lease_start_date')->nullable(false)->change();
            $table->date('lease_end_date')->nullable(false)->change();
        });
    }
};
