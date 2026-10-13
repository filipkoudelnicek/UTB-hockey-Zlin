<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pseudonymní návštěvníci: pouze HMAC náhodného ID z cookie, žádná IP ani fingerprint.
        Schema::create('analytics_visitors', function (Blueprint $table) {
            $table->char('visitor_hash', 64)->primary();
            $table->date('first_seen_date');
            $table->dateTime('last_seen_at');
            $table->index('last_seen_at');
        });

        Schema::create('analytics_pageviews', function (Blueprint $table) {
            $table->id();
            $table->char('visitor_hash', 64);
            $table->string('path', 255);
            $table->string('viewable_type', 20)->nullable();
            $table->unsignedBigInteger('viewable_id')->nullable();
            $table->date('view_date');
            $table->dateTime('viewed_at');
            $table->boolean('is_returning')->default(false);

            $table->index('visitor_hash');
            $table->index(['view_date', 'visitor_hash']);
            $table->index(['view_date', 'path', 'visitor_hash'], 'analytics_pageviews_date_path_visitor');
        });

        // Anonymní denní souhrny; zůstávají i po smazání surových dat.
        Schema::create('analytics_daily_pages', function (Blueprint $table) {
            $table->id();
            $table->date('view_date');
            $table->char('path_hash', 40);
            $table->string('path', 255);
            $table->string('viewable_type', 20)->nullable();
            $table->unsignedBigInteger('viewable_id')->nullable();
            $table->unsignedInteger('views')->default(0);

            $table->unique(['view_date', 'path_hash']);
            $table->index(['viewable_type', 'viewable_id', 'view_date'], 'analytics_daily_viewable');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_daily_pages');
        Schema::dropIfExists('analytics_pageviews');
        Schema::dropIfExists('analytics_visitors');
    }
};
