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
        Schema::create('pickings', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['Nhập', 'Xuất']);
            $table->unsignedBigInteger('warehouseID');
            $table->date('createDate')->nullable();
            $table->string('createName', 100)->nullable();

            $table->enum('active', ['Hoàn thành', 'Chưa hoàn thành'])->default('Chưa hoàn thành');
            $table->string('status');

            $table->unsignedBigInteger('decomposeID')->nullable();
            $table->unsignedBigInteger('invoiceID')->nullable();
            $table->unsignedBigInteger('taskID')->nullable();

            $table->string('desc')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('warehouseID')->references('id')->on('ware_houses')->onDelete('cascade');
            $table->foreign('decomposeID')->references('id')->on('decomposes')->onDelete('set null');
            $table->foreign('invoiceID')->references('id')->on('invoices')->onDelete('set null');
            $table->foreign('taskID')->references('id')->on('gen_tasks')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pickings');
    }
};
