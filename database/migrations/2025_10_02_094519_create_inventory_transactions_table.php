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
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('productID');
            $table->unsignedBigInteger('warehouseID');
            $table->unsignedBigInteger('unitID')->nullable();

            $table->enum('type', ['import', 'export', 'adjust'])->index();
            $table->decimal('quantity_changed', 18, 6); // âm khi export
            $table->decimal('balance_after', 18, 6);

            $table->nullableMorphs('reference'); // reference_type, reference_id
            $table->string('code')->nullable();
            $table->text('note')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index(['productID', 'warehouseID', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
