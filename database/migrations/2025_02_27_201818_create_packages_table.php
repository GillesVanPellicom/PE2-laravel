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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->string('origin_location_id');
            $table->string('current_location_id')->nullable();
            $table->string('destination_location_id');
            $table->foreignId('addresses_id')->nullable()->constrained('addresses');
            $table->string('status');
            $table->integer('times_delivered')->default(0);
            $table->foreignId('weight_id')->constrained('weight_classes');
            $table->foreignId('delivery_method_id')->constrained('delivery_method');
            $table->string('dimension');
            $table->string('weight_price')->default(0);
            $table->boolean('requires_signature')->default(false);
            $table->float('weight')->nullable();
            $table->boolean('paid')->default(false);
            $table->string('delivery_price')->default(0);
            $table->string('name');
            $table->string('lastName');
            $table->string('receiverEmail');
            $table->string('receiver_phone_number')->nullable();
            $table->string('sender_firstname')->nullable();
            $table->string('sender_lastname')->nullable();
            $table->string('sender_phone_number')->nullable();
            $table->string('sender_email')->nullable();
            $table->string('assigned_flight')->nullable();
            $table->string('safe_location')->nullable();
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
