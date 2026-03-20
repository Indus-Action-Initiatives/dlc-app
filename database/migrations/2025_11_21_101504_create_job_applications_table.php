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
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('worker_id');
            $table->unsignedBigInteger('job_post_id');
            $table->unsignedBigInteger('employer_id');
            $table->enum('status', ['pending','accepted','rejected'])->default('pending');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('worker_id')->references('id')->on('workers')->onDelete('cascade');
            $table->foreign('job_post_id')->references('id')->on('job_posts')->onDelete('cascade');
            $table->foreign('employer_id')->references('id')->on('employers')->onDelete('cascade');

            $table->unique(['worker_id','job_post_id']); // ek worker ek job par ek hi baar apply kar sake
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
