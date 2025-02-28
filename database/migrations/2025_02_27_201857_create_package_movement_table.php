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
        Schema::create('package_movements', function (Blueprint $table) {
            $table->id('movement_id');
            $table->foreignId('package_id')->constrained('packages');
            $table->foreignId('from_location_id')->constrained('locations');
            $table->foreignId('to_location_id')->constrained('locations');
            $table->foreignId('handled_by_courier_id')->constrained('couriers');
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles');
            $table->dateTime('departure_time');
            $table->dateTime('arrival_time')->nullable();
            $table->timestamps('check_in_time');
            $table->timestamps('check_out_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_movement');
    }
};
