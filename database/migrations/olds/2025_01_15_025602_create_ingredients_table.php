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
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained('farm');
            $table->foreignId('type_of_pus_id')->constrained('typeofpus');
            $table->string('vehicle_number');
            $table->string('batch')->nullable();
            $table->date('received_date');
            $table->string('receiving_factory');
            $table->date('harvesting_date')->nullable();
            $table->date('end_harvest_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
