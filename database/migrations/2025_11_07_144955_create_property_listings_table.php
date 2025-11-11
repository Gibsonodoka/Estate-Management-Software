<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->nullable()->constrained('properties')->onDelete('cascade'); // If from estate
            $table->foreignId('agent_id')->nullable()->constrained('users')->onDelete('cascade'); // If listed by agent
            $table->string('title');
            $table->text('description');
            $table->string('address');
            $table->string('city');
            $table->string('state');
            $table->string('country')->default('Nigeria');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->enum('property_type', ['apartment', 'duplex', 'bungalow', 'flat', 'penthouse', 'studio', 'land', 'commercial']);
            $table->integer('bedrooms')->default(0);
            $table->integer('bathrooms')->default(0);
            $table->decimal('price', 12, 2);
            $table->enum('listing_type', ['rent', 'sale'])->default('rent');
            $table->enum('status', ['available', 'rented', 'sold', 'inactive'])->default('available');
            $table->json('features')->nullable();
            $table->json('images')->nullable(); // Array of image URLs
            $table->string('video_url')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->integer('views_count')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['city', 'state', 'listing_type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_listings');
    }
};
