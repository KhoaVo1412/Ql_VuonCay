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
        Schema::create('plots', function (Blueprint $table) {
            $table->id();
            $table->string('plotName');
            $table->string('plotArea');
            $table->unsignedBigInteger('gardenID');
            $table->integer('plantCount');
            $table->string('status');
            $table->timestamps();

            $table->foreign('gardenID')->references('id')->on('gardens')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plots');
    }
};
