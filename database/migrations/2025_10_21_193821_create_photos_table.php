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
        // 5b. Photos (for donations, pickups, etc.)
        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            // Polymorphic structure to link to any resource
            $table->unsignedBigInteger('resource_id');
            $table->string('resource_type'); // e.g., 'donation', 'user_avatar', 'pickup_proof'
            
            $table->string('url')->comment('URL to the main image');
            $table->string('thumbnail_url')->nullable();
            $table->timestamps();

            $table->index(['resource_id', 'resource_type']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photos');
    }
};

