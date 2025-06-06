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
        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('airplane_id')->constrained('airplanes');
            $table->foreignId('depart_location_id')->constrained('locations');
            $table->foreignId('arrive_location_id')->constrained('locations');
            $table->Time('departure_time');
            $table->float('time_flight_minutes'); 
            $table->enum('departure_day_of_week', [
                'Monday', 
                'Tuesday', 
                'Wednesday', 
                'Thursday', 
                'Friday', 
                'Saturday', 
                'Sunday'
            ]); 
            $table->date('departure_date');
            $table->string('status');
            $table->integer('delayed_minutes')->nullable();
            $table->boolean('isActive');
            $table->float('current_weight')->default(0); // Add this column to track current weight
            $table->string('gate');
            $table->foreignId('router_edge_id')->constrained('router_edges');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flights');
    }
};
