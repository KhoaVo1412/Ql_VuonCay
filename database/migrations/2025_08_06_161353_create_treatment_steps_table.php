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
        Schema::create('treatment_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sessionID')->constrained('treatment_sessions')->onDelete('cascade');
            $table->foreignId('productID')->constrained('products')->onDelete('cascade');
            $table->string('dose');
            $table->date('execution_date');
            $table->text('instructions');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treatment_steps');
    }
};
