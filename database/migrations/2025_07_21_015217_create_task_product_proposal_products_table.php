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
        Schema::create('task_product_proposal_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('productID');
            $table->unsignedBigInteger('taskproposalID');
            $table->unsignedBigInteger('sessionID');
            $table->timestamps();
            $table->foreign('productID')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('taskproposalID')->references('id')->on('task_product_proposals')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_product_proposal_products');
    }
};
