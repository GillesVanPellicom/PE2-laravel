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
        Schema::create('vacations', function (Blueprint $table) {
            $table->id('vacation_id');
            $table->foreignId('employee_id')->constrained('employees');
            $table->string('vacation_type');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('day_type', ['Whole Day', 'First Half', 'Second Half'])->default('Whole Day');
            $table->enum('approve_status', ['Pending', 'Approved', 'Rejected']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacations');
    }
};