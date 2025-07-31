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
        Schema::create('testing_results', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('batch_id')->nullable(); 

            $table->foreign('batch_id')->references('id')->on('batches')->onDelete('cascade');

            // Các chỉ tiêu gắn tiền tố 'svr' (kiểu string)
            $table->string('svr_impurity')->nullable();
            $table->string('svr_ash')->nullable();
            $table->string('svr_volatile')->nullable();
            $table->string('svr_nitrogen')->nullable();
            $table->string('svr_po')->nullable();
            $table->string('svr_pri')->nullable();
            $table->string('svr_color')->nullable();
            $table->string('svr_vr')->nullable();

            // Các chỉ tiêu gắn tiền tố 'latex' (kiểu string)
            $table->string('latex_tsc')->nullable();
            $table->string('latex_drc')->nullable();
            $table->string('latex_nrs')->nullable();
            $table->string('latex_nh3')->nullable();
            $table->string('latex_mst')->nullable();
            $table->string('latex_vfa')->nullable();
            $table->string('latex_koh')->nullable();
            $table->string('latex_ph')->nullable();
            $table->string('latex_coagulant')->nullable();
            $table->string('latex_residue')->nullable();
            $table->string('latex_mg')->nullable();
            $table->string('latex_mn')->nullable();
            $table->string('latex_cu')->nullable();
            $table->string('latex_acid_boric')->nullable();
            $table->string('latex_surface_tension')->nullable();
            $table->string('latex_viscosity')->nullable();

            // Các chỉ tiêu gắn tiền tố 'rss' (kiểu string)
            $table->string('rss_impurity')->nullable();
            $table->string('rss_ash')->nullable();
            $table->string('rss_volatile')->nullable();
            $table->string('rss_nitrogen')->nullable();
            $table->string('rss_po')->nullable();
            $table->string('rss_pri')->nullable();
            $table->string('rss_vr')->nullable();
            $table->string('rss_aceton')->nullable();
            $table->string('rss_tensile_strength')->nullable();
            $table->string('rss_elongation')->nullable();
            $table->string('rss_vulcanization')->nullable();

            // Timestamps
            $table->timestamps(); // created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('testing_results');
    }
};