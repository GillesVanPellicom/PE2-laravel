<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('employee_in_teams', function (Blueprint $table) {
        $table->id();
        $table->foreignId('employee_id')->constrained()->onDelete('cascade');
        $table->foreignId('team_id')->constrained()->onDelete('cascade');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_in_teams');
    }
};
