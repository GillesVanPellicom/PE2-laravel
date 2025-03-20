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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id('salary_id');
            $table->foreignId('employee_id')->constrained('employees');
            $table->decimal('base_salary', 8, 2);
            $table->decimal('bonus', 8, 2)->nullable();
            $table->decimal('taxes', 8, 2);
            $table->decimal('net_salary', 8, 2);
            $table->date('payment_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
