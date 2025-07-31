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
        Schema::create('order_exports', function (Blueprint $table) {
            $table->id();  // Primary key for the table
            $table->string('code')->unique();  // 'code' column for the order code
            $table->unsignedBigInteger('contract_id');  // Foreign key to the contracts table
            $table->timestamps();  // Created_at and updated_at columns

            // Foreign key constraint (assuming you have a 'contracts' table with an 'id' column)
            $table->foreign('contract_id')->references('id')->on('contracts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_exports');
    }
};