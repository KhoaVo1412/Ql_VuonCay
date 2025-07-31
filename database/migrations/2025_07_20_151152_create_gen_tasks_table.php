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
        Schema::create('gen_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('workerID');
            $table->string('workID', 10);
            $table->date('workDate');
            $table->string('TaskpropID', 10)->nullable();
            $table->string('plotID', 10)->nullable();
            $table->boolean('active')->default(true);
            $table->enum('type', ['Khai thác', 'Chăm sóc']);
            $table->string('productID', 10)->nullable();
            $table->boolean('workStatus')->default(false);
            $table->string('description', 100)->nullable();
            $table->enum('priority', ['cao', 'thấp']);

            $table->foreign('workerID')->references('id')->on('workers')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gen_tasks');
    }
};
