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
        Schema::create('worker_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('worker_id')->constrained('workers')->onDelete('cascade');
            $table->foreignId('employer_id')->nullable()->constrained('employers')->onDelete('set null');
             $table->string('employer_name')->nullable();
            $table->string('project_name')->nullable();
            $table->string('work_place')->nullable();
            $table->string('work_type')->nullable();
             $table->json('skill_id')->nullable();
            $table->text('task_description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('days_worked')->nullable();
            $table->decimal('daily_wage', 10, 2)->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'partial'])->default('pending');
            $table->text('remarks')->nullable();
            $table->integer('rating')->nullable();
            $table->timestamps();
            $table->index(['worker_id', 'employer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worker_histories');
    }
};
