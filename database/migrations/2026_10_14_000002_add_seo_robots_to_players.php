<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->string('seo_robots', 30)->default('index, follow');
        });
    }

    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropColumn('seo_robots');
        });
    }
};
