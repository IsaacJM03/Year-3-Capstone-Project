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
            $table->uuid('client_id')->unique()->comment('Client-generated UUID for offline idempotency (deduplication)');
            
            // Donor identification (polymorphic relationship approach)
            $table->unsignedBigInteger('donor_id');
            $table->string('donor_type')->comment('Model name: App\Models\User or App\Models\Organization');

            $table->string('title');
            $table->text('description')->nullable();
            
            $table->float('quantity_est_kg')->nullable()->comment('Estimated quantity in kilograms');
            $table->string('storage_condition')->nullable(); // e.g., 'refrigerated', 'ambient'
            
            $table->timestamp('pickup_from')->comment('ISO8601 UTC start time for pickup window');
            $table->timestamp('pickup_to')->comment('ISO8601 UTC end time for pickup window');

            $table->string('address');
            $table->decimal('lat', 10, 8);
            $table->decimal('lng', 11, 8);

            // Statuses from spec (Page 17)
            $table->enum('status', ['available', 'requested', 'reserved', 'cancelled', 'completed'])
                  ->default('available');
            $table->enum('visibility', ['public', 'private'])->default('public');

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

