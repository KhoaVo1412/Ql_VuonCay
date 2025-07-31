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
        Schema::create('product_proposal_pickings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('propID');
            $table->unsignedBigInteger('pickingID');
            $table->integer('quantity');
            $table->string('status');

            $table->timestamps();
            $table->foreign('propID')->references('id')->on('task_product_proposal_products');
            $table->foreign('pickingID')->references('id')->on('pickings');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_proposal_pickings');
    }
};
