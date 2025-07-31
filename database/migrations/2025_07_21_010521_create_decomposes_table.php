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
        Schema::create('decomposes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->nullable();
            $table->date('date')->nullable();

            $table->unsignedBigInteger('warehouseID');
            $table->unsignedBigInteger('userID')->nullable();

            $table->enum('active', ['Hoàn thành', 'Chưa hoàn thành'])->default('Chưa hoàn thành');

            $table->string('status');
            $table->timestamps();

            $table->foreign('warehouseID')->references('id')->on('ware_houses')->onDelete('cascade');
            $table->foreign('userID')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('decomposes');
    }
};
