<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('agency_name')->nullable();
            $table->string('license_number')->nullable();
            $table->text('bio')->nullable();
            $table->string('office_address')->nullable();
            $table->string('office_phone')->nullable();
            $table->json('service_areas')->nullable(); // Cities/states they cover
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('total_ratings')->default(0);
            $table->integer('properties_listed')->default(0);
            $table->integer('properties_sold')->default(0);
            $table->decimal('total_earnings', 12, 2)->default(0);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->enum('verification_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('verification_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_profiles');
    }
};
