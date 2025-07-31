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
        Schema::create('plants', function (Blueprint $table) {
            $table->id();
            $table->string('plantCode')->nullable();
            $table->unsignedBigInteger('plotID');
            $table->unsignedBigInteger('varietyID');
            $table->string('RF_id', 10)->nullable();
            $table->year('year')->nullable();
            $table->string('status');
            $table->timestamps();

            // Khóa ngoại
            $table->foreign('plotID')->references('id')->on('plots')->onDelete('cascade');
            $table->foreign('varietyID')->references('id')->on('varieties')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plants');
    }
};
