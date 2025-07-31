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
        Schema::create('disease_plants', function (Blueprint $table) {
            $table->id();
            $table->date('detectionDate');

            $table->unsignedBigInteger('sessionID');
            $table->unsignedBigInteger('plantID');
            $table->unsignedBigInteger('diseaseID');
            $table->unsignedBigInteger('workerID')->nullable();

            $table->string('specific_location', 250)->nullable();
            $table->enum('reportStatus', ['Chờ xác thực', 'Đã xác thực', 'Từ chối'])->default('Chờ xác thực');
            $table->string('tree_condition')->nullable();
            $table->string('infection_level')->nullable();
            $table->string('treatmentResult')->nullable();
            $table->string('status');
            $table->timestamps();

            $table->foreign('sessionID')->references('id')->on('treatment_sessions')->onDelete('cascade');
            $table->foreign('plantID')->references('id')->on('plants')->onDelete('cascade');
            $table->foreign('diseaseID')->references('id')->on('diseases')->onDelete('cascade');
            $table->foreign('workerID')->references('id')->on('workers')->onDelete('set null');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disease_plant1s');
    }
};
