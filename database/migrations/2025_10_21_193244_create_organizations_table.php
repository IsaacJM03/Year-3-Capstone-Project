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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('address');
            $table->decimal('lat', 10, 8);
            $table->decimal('lng', 11, 8);
            $table->string('contact_person')->nullable();
            $table->enum('type', ['receiver', 'donor_company'])->comment('Determines primary function');
            $table->string('verification_doc_url')->nullable()->comment('URL to the uploaded verification document');
            $table->boolean('verified')->default(false)->comment('Admin flag for organization approval');
            $table->text('csr_info')->nullable()->comment('Corporate Social Responsibility information for company donors');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};

