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
        Schema::create('material_proposals', function (Blueprint $table) {
            $table->id();
            $table->string('proposaName');
            $table->date('proposalDate')->nullable();
            $table->date('approvalDate')->nullable();

            $table->foreignId('diseaseplantID')
                ->constrained('disease_plants')
                ->cascadeOnDelete();

            $table->string('status');

            $table->foreignId('created_by')->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('reason')->nullable();

            $table->timestamps();

            $table->index(['diseaseplantID', 'status']);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_proposals');
    }
};
