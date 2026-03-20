<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employer_profiles', function (Blueprint $table) {
            $table->string('plot_no')->nullable()->after('id');
            $table->string('street_area_village')->nullable();
            $table->string('post_office')->nullable();
            $table->string('district')->nullable();
            $table->string('state')->nullable();
            $table->string('pin_code')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('employer_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'plot_no',
                'street_area_village',
                'post_office',
                'district',
                'state',
                'pin_code'
            ]);
        });
    }
};
