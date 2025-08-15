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
        Schema::table('task_product_proposal_products', function (Blueprint $table) {
            $table->foreign('warehouseID')->references('id')->on('ware_houses')->onDelete('cascade');
            $table->foreign('unitID')->references('id')->on('unit_of_measures')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_product_proposal_products', function (Blueprint $table) {
            //
        });
    }
};
