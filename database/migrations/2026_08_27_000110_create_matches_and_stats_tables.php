<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_season_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('match_type', 30)->index();
            $table->dateTime('played_at')->index();
            $table->foreignId('venue_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('home_team_id')->constrained('teams')->restrictOnDelete();
            $table->foreignId('away_team_id')->constrained('teams')->restrictOnDelete();
            $table->string('status', 30)->default('scheduled')->index();
            $table->unsignedSmallInteger('home_score')->nullable();
            $table->unsignedSmallInteger('away_score')->nullable();
            $table->string('ticket_url')->nullable();
            $table->foreignId('report_article_id')->nullable()->constrained('articles')->nullOnDelete();
            $table->string('source')->nullable();
            $table->string('external_id')->nullable();
            $table->timestamps();
            $table->unique(['source', 'external_id']);
            $table->index(['status', 'played_at']);
            $table->index(['competition_season_id', 'status']);
        });

        Schema::create('match_player_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('matches')->cascadeOnDelete();
            $table->foreignId('player_id')->constrained()->restrictOnDelete();
            $table->foreignId('team_id')->constrained()->restrictOnDelete();
            $table->boolean('played')->default(true);
            $table->unsignedSmallInteger('goals')->default(0);
            $table->unsignedSmallInteger('assists')->default(0);
            $table->smallInteger('plus_minus')->default(0);
            $table->timestamps();
            $table->unique(['match_id', 'player_id', 'team_id']);
            $table->index(['player_id', 'team_id']);
        });

        Schema::create('competition_standings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_season_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->constrained()->restrictOnDelete();
            $table->unsignedSmallInteger('wins')->default(0);
            $table->unsignedSmallInteger('losses')->default(0);
            $table->smallInteger('points')->default(0);
            $table->timestamps();
            $table->unique(['competition_season_id', 'team_id']);
            $table->index(['competition_season_id', 'points']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competition_standings');
        Schema::dropIfExists('match_player_stats');
        Schema::dropIfExists('matches');
    }
};
