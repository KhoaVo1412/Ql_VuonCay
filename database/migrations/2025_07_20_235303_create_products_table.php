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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            $table->unsignedBigInteger('categoryID');
            $table->unsignedBigInteger('unitID');

            $table->string('type')->nullable();

            $table->string('status')->default(true);
            $table->timestamps();

            $table->foreign('categoryID')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('unitID')->references('id')->on('unit_of_measures')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
