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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['donor_individual', 'donor_company', 'receiver', 'volunteer', 'admin'])
                  ->default('donor_individual');
            // Use unsignedBigInteger for foreign key if the referenced table may not exist yet.
            $table->unsignedBigInteger('organization_id')->nullable();
            $table->string('avatar_url')->nullable();
            $table->string('phone_number')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        // Optionally add FK after organizations table exists in a later migration:
        // Schema::table('users', function (Blueprint $table) {
        //     $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('set null');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

