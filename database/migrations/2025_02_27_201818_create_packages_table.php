<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('packages', function (Blueprint $table) {
      $table->id();
      $table->string('reference')->nullable();
      $table->foreignId('user_id')->constrained('users');
      $table->foreignId('origin_location_id')->constrained('locations');
      $table->foreignId('destination_location_id')->nullable()->constrained('locations');
      $table->foreignId('addresses_id')->nullable()->constrained('addresses');
      $table->string('status');
      $table->foreignId('weight_id')->constrained('weight_classes');
      $table->foreignId('delivery_method_id')->constrained('delivery_method');
      $table->string('dimension');
      $table->string('weight_price')->default(0);
      $table->float('weight')->nullable();
      $table->string('delivery_price')->default(0);
      $table->string('name');
      $table->string('lastName');
      $table->string('receiverEmail');
      $table->string('receiver_phone_number');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('packages');
  }
};
