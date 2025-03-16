<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('package_movements', function (Blueprint $table) {
      $table->id();
      $table->foreignId('package_id')->constrained('packages');
      $table->foreignId('from_location_id')->constrained('locations');
      $table->foreignId('to_location_id')->constrained('locations');
      $table->foreignId('handled_by_courier_id')->nullable()->constrained('couriers');
      $table->foreignId('vehicle_id')->nullable()->constrained('vehicles');
      $table->dateTime('departure_time')->nullable();
      $table->dateTime('arrival_time')->nullable();
      $table->dateTime('check_in_time')->nullable();
      $table->dateTime('check_out_time')->nullable();
      $table->foreignId('node_id')->constrained('locations');
      $table->foreignId('next_hop')->nullable()->constrained('package_movements');
      $table->foreignId('router_edge_id')->nullable()->constrained('router_edges');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('package_movements');
  }
};
