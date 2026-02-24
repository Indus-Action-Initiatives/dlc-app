<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employer_profiles', function (Blueprint $table) {
            $table->string('profile_image')->nullable()->after('phone');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('profile_image');
        });
    }

    public function down(): void
    {
        Schema::table('employer_profiles', function (Blueprint $table) {
            $table->dropColumn(['profile_image', 'gender']);
        });
    }
};
