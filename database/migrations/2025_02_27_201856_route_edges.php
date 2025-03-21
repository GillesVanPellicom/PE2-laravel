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
        Schema::create('route_edges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('origin_node')->constrained('locations');
            $table->foreignId('destination_node')->constrained('locations');
            $table->double('weight');
            $table->boolean('isUnidiretional')->default(false);
            $table->Datetime('validFrom');
            $table->Datetime('validTo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('route_edges');
    }
};
