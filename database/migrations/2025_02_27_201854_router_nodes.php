<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('router_nodes', function (Blueprint $table) {
      $table->string('id')->primary();
      $table->foreignId('city_id')->nullable()->constrained('cities');
      $table->string('description');
      $table->enum('location_type', ['PICKUP_POINT', 'DISTRIBUTION_CENTER', 'AIRPORT']);
      $table->decimal('latDeg', 12, 8)->nullable();
      $table->decimal('lonDeg', 12, 8)->nullable();
      $table->boolean('isEntry')->default(false);
      $table->boolean('isExit')->default(false);
      $table->timestamps();

      // Computed columns
      $table->decimal('latRad', 12, 8)->storedAs('RADIANS(latDeg)');
      $table->decimal('lonRad', 12, 8)->storedAs('RADIANS(lonDeg)');
    });
  }

  public function down(): void {
    Schema::dropIfExists('router_nodes');
  }
};