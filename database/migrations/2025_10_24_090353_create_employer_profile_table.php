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
        Schema::create('employer_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employer_id')->nullable();
            $table->string('emp_type')->nullable();
            $table->string('name',100)->nullable();
            $table->string('avg_worker')->nullable();
            $table->string('work_type')->nullable();
            $table->string('location')->nullable();
            $table->string('availability')->nullable();
            $table->string('eshram')->nullable();
            $table->string('aadhar')->nullable();
            $table->string('docType')->nullable()->after('pin_code');
            $table->string('docNumber')->nullable()->after('docType');
            $table->string('pdf')->nullable()->after('docNumber');
            $table->string('bocw')->nullable();
            $table->string('language')->nullable();
            $table->timestamps();

            $table->foreign('employer_id')->references('id')->on('employers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employer_profiles');
    }
};
