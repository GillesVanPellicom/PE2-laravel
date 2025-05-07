<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('stranded_packages', function (Blueprint $table) {
      $table->id();
      $table->foreignId('package_id')->constrained('packages')->onDelete('cascade');
      $table->boolean('is_resolved')->default(false);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('stranded_packages');
  }
};
