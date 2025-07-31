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
        Schema::create('fallent_plants', function (Blueprint $table) {
            $table->id();
            $table->date('detectionDate');
            $table->string('cause', 250)->nullable();
            $table->unsignedBigInteger('plantID');
            $table->string('specificLocation', 250)->nullable();
            $table->enum('reportStatus', ['Chờ xác thực', 'Đã xác thực', 'Từ chối'])->default('Chờ xác thực');
            $table->string('treeCondition', 100)->nullable();
            $table->unsignedBigInteger('workerID');
            $table->string('status');
            $table->timestamps();

            $table->foreign('plantID')->references('id')->on('plants')->onDelete('cascade');
            $table->foreign('workerID')->references('id')->on('workers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fallent_plants');
    }
};
