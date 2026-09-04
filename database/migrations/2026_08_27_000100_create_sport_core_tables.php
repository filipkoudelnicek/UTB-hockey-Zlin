<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competitions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->string('slug')->unique();
            $table->unsignedBigInteger('logo_media_id')->nullable()->index();
            $table->string('source')->nullable();
            $table->string('external_id')->nullable();
            $table->timestamps();
            $table->unique(['source', 'external_id']);
        });

        Schema::create('competition_seasons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('status')->default('active')->index();
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->string('source')->nullable();
            $table->string('external_id')->nullable();
            $table->timestamps();
            $table->unique(['competition_id', 'name']);
            $table->unique(['source', 'external_id']);
        });

        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->string('slug')->unique();
            $table->unsignedBigInteger('logo_media_id')->nullable()->index();
            $table->string('primary_color', 20)->nullable();
            $table->string('secondary_color', 20)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->string('source')->nullable();
            $table->string('external_id')->nullable();
            $table->timestamps();
            $table->unique(['source', 'external_id']);
        });

        Schema::create('competition_season_team', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_season_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->constrained()->restrictOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['competition_season_id', 'team_id']);
        });

        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('slug')->unique();
            $table->unsignedBigInteger('portrait_media_id')->nullable()->index();
            $table->date('date_of_birth')->nullable();
            $table->unsignedSmallInteger('height')->nullable();
            $table->unsignedSmallInteger('weight')->nullable();
            $table->string('stick_side', 20)->nullable();
            $table->string('faculty')->nullable();
            $table->text('bio')->nullable();
            $table->string('profile_heading')->nullable();
            $table->text('quote')->nullable();
            $table->unsignedBigInteger('video_media_id')->nullable()->index();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->unsignedBigInteger('seo_og_media_id')->nullable()->index();
            $table->unsignedSmallInteger('jersey_number')->nullable();
            $table->string('position', 30);
            $table->string('captain_role', 20)->default('none');
            $table->boolean('is_active')->default(true)->index();
            $table->string('source')->nullable();
            $table->string('external_id')->nullable();
            $table->timestamps();
            $table->unique(['source', 'external_id']);
        });

        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('map_url')->nullable();
            $table->string('source')->nullable();
            $table->string('external_id')->nullable();
            $table->timestamps();
            $table->unique(['source', 'external_id']);
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->foreignId('home_venue_id')->nullable()->constrained('venues')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropConstrainedForeignId('home_venue_id');
        });
        Schema::dropIfExists('venues');
        Schema::dropIfExists('players');
        Schema::dropIfExists('competition_season_team');
        Schema::dropIfExists('teams');
        Schema::dropIfExists('competition_seasons');
        Schema::dropIfExists('competitions');
    }
};
