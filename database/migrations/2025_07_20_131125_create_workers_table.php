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
        Schema::create('workers', function (Blueprint $table) {
            $table->id();
            $table->string('image');
            $table->string('code_name');
            $table->string('name');
            $table->date('bdate');
            $table->string('cccd', 12)->nullable();
            $table->string('address');
            $table->string('team');
            $table->string('teamname')->nullable();
            $table->integer('gender');
            $table->string('phone', 11);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workers');
    }
};
