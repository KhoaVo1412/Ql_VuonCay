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
        Schema::create('product_decomposes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('productID');
            $table->unsignedBigInteger('decomposeID');
            $table->unsignedBigInteger('productDecomposeID');

            $table->unsignedBigInteger('unitDecomposeID');
            $table->unsignedBigInteger('productUnitID');

            $table->integer('quantityDecompose');
            $table->integer('quantityProduct');
            $table->timestamps();

            $table->foreign('productID')->references('id')->on('products');
            $table->foreign('decomposeID')->references('id')->on('decomposes');
            $table->foreign('productDecomposeID')->references('id')->on('products');
            $table->foreign('unitDecomposeID')->references('id')->on('unit_of_measures');
            $table->foreign('productUnitID')->references('id')->on('unit_of_measures');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_decomposes');
    }
};
