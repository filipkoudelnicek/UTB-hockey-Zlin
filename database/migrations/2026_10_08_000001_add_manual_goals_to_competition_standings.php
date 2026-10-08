<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('competition_standings', function (Blueprint $table) {
            $table->unsignedSmallInteger('manual_goals_for')->nullable();
            $table->unsignedSmallInteger('manual_goals_against')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('competition_standings', function (Blueprint $table) {
            $table->dropColumn(['manual_goals_for', 'manual_goals_against']);
        });
    }
};
