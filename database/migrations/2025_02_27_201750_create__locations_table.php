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
            $table->enum('location_type', ['Pickup Point', 'Parcel Locker', 'Distribution Center', 'Airport']);
            $table->foreignId('addresses_id')->constrained('addresses');
            $table->string('contact_number');
            $table->string('opening_hours');
            $table->boolean('is_active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('_locations');
    }
};
