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
        Schema::table('inventory_transactions', function (Blueprint $table) {
            // productID -> products(id)
            $table->foreign('productID', 'fk_it_product')
                ->references('id')->on('products')
                ->onUpdate('cascade')->onDelete('restrict');

            // warehouseID -> ware_houses(id)
            $table->foreign('warehouseID', 'fk_it_warehouse')
                ->references('id')->on('ware_houses')
                ->onUpdate('cascade')->onDelete('restrict');

            // unitID -> unit_of_measures(id)
            $table->foreign('unitID', 'fk_it_unit')
                ->references('id')->on('unit_of_measures')
                ->onUpdate('cascade')->onDelete('set null');

            // created_by -> users(id)
            $table->foreign('created_by', 'fk_it_created_by')
                ->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->dropForeign('fk_it_product');
            $table->dropForeign('fk_it_warehouse');
            $table->dropForeign('fk_it_unit');
            $table->dropForeign('fk_it_created_by');
        });
    }
};
