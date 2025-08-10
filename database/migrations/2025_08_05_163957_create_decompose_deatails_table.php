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
        Schema::create('decompose_deatails', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('decomposeID');
            $table->unsignedBigInteger('productID');
            $table->decimal('qty', 18, 2);
            $table->unsignedBigInteger('unitID');
            $table->string('type');
            $table->timestamps();

            $table->foreign('decomposeID')->references('id')->on('decomposes')->onDelete('cascade');
            $table->foreign('productID')->references('id')->on('products');
            $table->foreign('unitID')->references('id')->on('unit_of_measures');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('decompose_deatails');
    }
};
