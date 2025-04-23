<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('router_edges', function (Blueprint $table) {
      $table->id();
      $table->string('origin_node');
      $table->string('destination_node');
      $table->double('weight');
      $table->boolean('isUniDirectional')->default(false);
      $table->datetime('validFrom');
      $table->datetime('validTo');
      $table->timestamps();

      $table->foreign('origin_node')->references('id')->on('router_nodes');
      $table->foreign('destination_node')->references('id')->on('router_nodes');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('route_edges');
  }
};