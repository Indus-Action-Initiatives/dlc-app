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
            Schema::create('worker_profiles', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('worker_id')->nullable();
                $table->string('name',100)->nullable();
                $table->string('rate',20)->nullable();
                $table->integer('age')->nullable();
                $table->json('skill_id')->nullable();
                $table->string('experience')->nullable();
                $table->string('work_type')->nullable();
                $table->string('location')->nullable();
                $table->string('availability')->nullable();
                $table->string('eshram')->nullable();
                $table->string('docType')->nullable()->after('pin_code');
                $table->string('docNumber')->nullable()->after('docType');
                $table->string('pdf')->nullable()->after('docNumber');
                $table->string('language')->nullable();
                $table->timestamps();
                $table->foreign('worker_id')->references('id')->on('workers')->onDelete('cascade');
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worker_profile');
    }
};
