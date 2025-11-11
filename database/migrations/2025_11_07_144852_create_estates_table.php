<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('uci')->unique(); // Estate Unique Code Identifier
            $table->text('address');
            $table->string('city');
            $table->string('state');
            $table->string('country')->default('Nigeria');
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->enum('subscription_status', ['active', 'expired', 'trial', 'cancelled'])->default('trial');
            $table->date('subscription_starts_at')->nullable();
            $table->date('subscription_expires_at')->nullable();
            $table->decimal('monthly_fee', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->json('amenities')->nullable(); // parking, pool, gym, etc.
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estates');
    }
};
