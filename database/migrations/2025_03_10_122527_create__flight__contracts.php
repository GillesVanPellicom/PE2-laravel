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
        Schema::create('_flight__contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flight_id')->constrained('flights');
            $table->foreignId('airline_id')->constrained('airlines');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->float('max_capacity');
            $table->float('price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('_flight__contracts');
    }
};
