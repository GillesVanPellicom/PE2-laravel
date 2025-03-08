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
        Schema::create('delivery_method', function (Blueprint $table) {
            $table->id();
<<<<<<< HEAD
            $table->string('code');
            $table->string('name');
            $table->string('description');
            $table->decimal('price', 8, 2);
            $table->boolean('requires_location')->default(true);
            $table->boolean('is_active')->default(true);
=======
            $table->string('name');
>>>>>>> 273ca822b4ef9c588497bf89ad65b8e2f40bd0e9
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_method');
    }
};
