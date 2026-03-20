<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
    {
        Schema::create('job_post_facilities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_post_id');
            $table->unsignedBigInteger('facility_id');
            $table->timestamps();

            $table->foreign('job_post_id')->references('id')->on('job_posts')->onDelete('cascade');
            $table->foreign('facility_id')->references('id')->on('facilities')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_post_facilities');
    }

};
