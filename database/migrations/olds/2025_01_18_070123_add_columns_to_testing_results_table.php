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
        Schema::table('testing_results', function (Blueprint $table) {
            $table->string('rank')->nullable()->after('batch_id'); 
            $table->date('ngay_gui_mau')->nullable()->after('rank');
            $table->date('ngay_kiem_nghiem')->nullable()->after('ngay_gui_mau');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('testing_results', function (Blueprint $table) {
            $table->dropColumn(['rank', 'ngay_gui_mau', 'ngay_kiem_nghiem']);
        });
    }
};