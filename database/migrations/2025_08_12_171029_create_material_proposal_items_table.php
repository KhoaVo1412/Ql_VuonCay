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
        Schema::create('material_proposal_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('material_proposal_id')
                ->constrained('material_proposals')
                ->cascadeOnDelete();

            $table->foreignId('warehouseID')->nullable()
                ->constrained('warehouses')
                ->nullOnDelete();

            // Đổi 'products' thành 'materials' nếu bạn dùng bảng materials
            $table->foreignId('productID')
                ->constrained('products')
                ->cascadeOnDelete();

            $table->decimal('materialQuantity', 12, 2);
            $table->foreignId('unitID')
                ->constrained('units')
                ->cascadeOnDelete();

            $table->text('note')->nullable();
            $table->string('status')->default('Chờ duyệt');

            $table->timestamps();

            $table->index(['material_proposal_id', 'productID']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_proposal_items');
    }
};
