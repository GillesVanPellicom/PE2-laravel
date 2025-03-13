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
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('location_type', ['Pickup Point', 'Parcel Locker', 'Distribution Center', 'Airport', 'Private Individu']);
            $table->foreignId('addresses_id')->constrained('addresses');
            $table->string('contact_number');
            $table->string('opening_hours')->nullable();
            $table->boolean('is_active');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 10, 8)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
