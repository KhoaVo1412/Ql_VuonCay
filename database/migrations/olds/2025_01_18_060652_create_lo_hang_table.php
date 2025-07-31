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
        Schema::create('batches', function (Blueprint $table) {
            $table->id(); // Auto-increment primary key
            $table->string('batch_code'); // Batch code
            $table->string('factory'); // Factory
            $table->date('production_date'); // Production date
            $table->string('status'); 
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('batches');
    }
};