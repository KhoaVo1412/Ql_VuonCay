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
        Schema::create('customers', function (Blueprint $table) {
            $table->id(); // Tạo cột 'id' (auto-increment primary key)
            $table->string('company_name'); // Tên công ty
            $table->string('customer_type'); // Loại khách hàng
            $table->string('phone'); // Số điện thoại
            $table->string('email')->unique(); // Email (đảm bảo là duy nhất)
            $table->string('address'); // Địa chỉ
            $table->text('description')->nullable(); // Mô tả, có thể null
            $table->timestamps(); // Cột 'created_at' và 'updated_at'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};