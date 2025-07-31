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
        Schema::table('batches', function (Blueprint $table) {
            // Thêm cột 'type' vào bảng batches
            $table->string('type')->nullable()->after('id'); // 'type' có thể null và thêm sau cột 'id'
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('batches', function (Blueprint $table) {
            // Nếu rollback, xóa cột 'type'
            $table->dropColumn('type');
        });
    }
};