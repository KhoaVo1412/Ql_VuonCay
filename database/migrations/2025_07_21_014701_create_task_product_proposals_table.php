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
        Schema::create('task_product_proposals', function (Blueprint $table) {
            $table->id();
            $table->date('proposalDate')->nullable();
            $table->date('approvalDate')->nullable();
            $table->enum('is_exported', ['Chưa xuất', 'Đã xuất', 'Đã trả'])->default('Chưa xuất');
            $table->unsignedBigInteger('taskID');
            $table->unsignedBigInteger('treamentID');
            $table->unsignedBigInteger('sessionID');
            $table->enum('request_status', ['Đã duyệt', 'Từ chối', 'Chờ duyệt'])->default('Chờ duyệt');
            $table->string('status')->default(true);
            $table->timestamps();

            $table->foreign('taskID')->references('id')->on('gen_tasks')->onDelete('cascade');
            // $table->foreign('treamentID')->references('id')->on('treatments')->onDelete('cascade');
            $table->foreign('sessionID')->references('id')->on('treatment_sessions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_product_proposals');
    }
};
