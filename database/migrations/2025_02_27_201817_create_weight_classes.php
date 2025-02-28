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
        Schema::create('weight_classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('weight_min', 8, 2);
            $table->decimal('weight_max', 8, 2);
            $table->decimal('price', 8, 2);
            $table->boolean('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weight_classes');
    }
};
