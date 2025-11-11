<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitor_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estate_id')->constrained('estates')->onDelete('cascade');
            $table->foreignId('host_user_id')->constrained('users')->onDelete('cascade'); // Who they're visiting
            $table->foreignId('security_user_id')->constrained('users')->onDelete('cascade'); // Security personnel
            $table->string('visitor_name');
            $table->string('visitor_phone');
            $table->string('visitor_id_type')->nullable(); // License, Passport, etc.
            $table->string('visitor_id_number')->nullable();
            $table->string('vehicle_plate')->nullable();
            $table->enum('visit_purpose', ['personal', 'business', 'delivery', 'service', 'other'])->default('personal');
            $table->timestamp('check_in_time');
            $table->timestamp('check_out_time')->nullable();
            $table->enum('status', ['checked_in', 'checked_out', 'denied'])->default('checked_in');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitor_logs');
    }
};
