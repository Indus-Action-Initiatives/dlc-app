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
        Schema::create('employer_skills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employer_id');
            $table->unsignedBigInteger('skill_id');
            $table->timestamps();

             $table->foreign('employer_id')->references('id')->on('employers')->onDelete('cascade');
             $table->foreign('skill_id')->references('id')->on('skills')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employer_skills');
    }
};
