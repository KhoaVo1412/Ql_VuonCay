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
        Schema::create('invoice_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoiceID');
            $table->unsignedBigInteger('productID');

            $table->decimal('quantity', 10, 2);
            $table->string('slice')->nullable();
            $table->enum('quality', ['A', 'B', 'C'])->default('A');
            $table->decimal('price', 10, 2);
            $table->string('status');

            $table->timestamps();

            $table->foreign('invoiceID')->references('id')->on('invoices')->onDelete('cascade');
            $table->foreign('productID')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_products');
    }
};
