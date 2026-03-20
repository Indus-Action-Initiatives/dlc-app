<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_id')->constrained('employers')->onDelete('cascade');
            $table->string('title');
            $table->string('description')->nullable();
            $table->json('skills_id')->nullable();
            $table->string('qualification')->nullable();
            $table->string('rate')->nullable();
            $table->string('job_type')->nullable();
            $table->string('work_location')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('work_duration')->nullable();
            $table->string('address')->nullable();
            $table->string('state')->nullable();
            $table->string('district')->nullable();
            $table->string('pincode')->nullable();
            $table->string('lat')->nullable();
            $table->string('long')->nullable();
            $table->string('tools_required')->nullable()->after('status');
            $table->integer('required_people')->nullable()->after('tools_required');
            $table->string('work_time')->nullable()->after('required_people');
            $table->boolean('status')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_posts');
    }
};
