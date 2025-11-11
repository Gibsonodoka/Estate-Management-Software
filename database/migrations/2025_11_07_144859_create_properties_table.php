<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estate_id')->constrained('estates')->onDelete('cascade');
            $table->foreignId('landlord_id')->constrained('users')->onDelete('cascade');
            $table->string('property_number'); // e.g., "Block A, Flat 5"
            $table->string('street')->nullable();
            $table->enum('property_type', ['apartment', 'duplex', 'bungalow', 'flat', 'penthouse', 'studio']);
            $table->integer('bedrooms');
            $table->integer('bathrooms');
            $table->decimal('rent_amount', 12, 2);
            $table->enum('rent_period', ['monthly', 'quarterly', 'bi-annually', 'annually'])->default('annually');
            $table->enum('status', ['occupied', 'vacant', 'maintenance', 'reserved'])->default('vacant');
            $table->boolean('is_listed')->default(false); // Listed on general platform
            $table->text('description')->nullable();
            $table->decimal('size_sqm', 8, 2)->nullable(); // Size in square meters
            $table->json('features')->nullable(); // AC, furnished, etc.
            $table->integer('floor_number')->nullable();
            $table->date('available_from')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Unique constraint for property number within an estate
            $table->unique(['estate_id', 'property_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
