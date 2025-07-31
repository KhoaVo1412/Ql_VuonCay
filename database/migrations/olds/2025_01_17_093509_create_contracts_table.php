<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id(); // Tạo cột 'id' (auto-increment primary key)
            $table->string('contract_code'); // Mã hợp đồng
            $table->foreignId('contract_type_id')->constrained('contract_types'); // Loại hợp đồng (foreign key)
            $table->foreignId('customer_id')->constrained('customers'); // Khách hàng (foreign key)
            $table->string('original_contract_number'); // Hợp đồng gốc số
            $table->string('delivery_month'); // Tháng giao hàng
            $table->decimal('quantity', 15, 2); // Khối lượng
            // $table->foreignId('order_export_id')->constrained('order_exports'); // Lệnh xuất hàng (foreign key)
            $table->integer('contract_days'); // Số ngày hợp đồng
            $table->string('product_type_name'); // Tên chủng loại sản phẩm
            $table->date('delivery_date'); // Ngày giao hàng
            $table->string('packaging_type'); // Dạng đóng gói
            $table->date('container_closing_date'); // Ngày đóng container
            $table->string('market'); // Thị trường
            $table->string('production_or_trade_unit'); // Đơn vị sản xuất thương mại
            $table->boolean('third_party_sale'); // Bán cho bên thứ 3
            $table->timestamps(); // Cột 'created_at' và 'updated_at'
        });
    }

    public function down()
    {
        Schema::dropIfExists('contracts');
    }
};