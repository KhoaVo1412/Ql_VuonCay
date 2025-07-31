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
        Schema::create('gen_task_plant', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('taskID');
            $table->unsignedBigInteger('plantID');
            $table->timestamps();

            $table->foreign('taskID')->references('id')->on('gen_tasks')->onDelete('cascade');
            $table->foreign('plantID')->references('id')->on('plants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gen_task_plant');
    }
};
