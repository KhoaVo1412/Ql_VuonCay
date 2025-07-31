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
        Schema::create('plantingarea', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('farm_id');
            $table->integer('idmap')->nullable();
            $table->string('ma_lo')->nullable();
            $table->string('nha_sx')->nullable();
            $table->string('quoc_gia')->nullable();
            $table->string('plot')->nullable();
            $table->year('nam_trong')->nullable();
            $table->string('chi_tieu')->nullable();
            $table->decimal('dien_tich')->nullable();
            $table->string('tapping_y')->nullable();
            $table->string('repl_time')->nullable();
            $table->integer('find')->nullable();
            $table->string('webmap')->nullable();
            $table->string('gwf')->nullable();
            $table->string('xa')->nullable();
            $table->string('huyen')->nullable();
            $table->string('nguon_goc_lo')->nullable();
            $table->string('nguon_goc_dat')->nullable();
            $table->text('chu_thich')->nullable();
            $table->geometry('geo')->nullable();
            $table->timestamps();

            $table->foreign('farm_id')->references('id')->on('farm')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plantingarea');
    }
};
