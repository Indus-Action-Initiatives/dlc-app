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
        Schema::create('worker_educations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('worker_id');

            $table->string('education_level', 100);
            $table->string('institution', 150)->nullable();
            $table->string('passing_year', 4)->nullable();
            $table->string('certificate_path', 255)->nullable();

            $table->timestamps();

            // Foreign key
            $table->foreign('worker_id')->references('id')->on('workers')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worker_educations');
    }
};
