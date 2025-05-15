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
        Schema::create('courier_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId("courier")->constrained("employees");
            $table->string('start_location')->nullable();
            $table->string('current_location')->nullable();
            $table->string('end_location')->nullable();
            $table->timestamps();
          });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courier_routes');
    }
};
