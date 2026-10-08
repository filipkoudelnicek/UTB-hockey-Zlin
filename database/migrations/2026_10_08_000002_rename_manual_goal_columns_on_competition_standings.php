<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('competition_standings', function (Blueprint $table) {
            $table->renameColumn('manual_goals_for', 'goals_for');
            $table->renameColumn('manual_goals_against', 'goals_against');
        });
    }

    public function down(): void
    {
        Schema::table('competition_standings', function (Blueprint $table) {
            $table->renameColumn('goals_for', 'manual_goals_for');
            $table->renameColumn('goals_against', 'manual_goals_against');
        });
    }
};
