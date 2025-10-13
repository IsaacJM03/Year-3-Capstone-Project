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
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donor_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('food_type');
            $table->decimal('quantity', 10, 2);
            $table->string('unit')->default('kg');
            $table->dateTime('expiry_date')->nullable();
            $table->text('pickup_address');
            $table->decimal('pickup_latitude', 10, 8);
            $table->decimal('pickup_longitude', 11, 8);
            $table->enum('status', ['available', 'claimed', 'completed', 'expired'])->default('available');
            $table->string('image_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
