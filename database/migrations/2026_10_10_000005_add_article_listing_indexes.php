<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->index(['lang_locale', 'active', 'publish_time'], 'articles_listing_index');
        });

        Schema::table('article_category', function (Blueprint $table) {
            $table->index('article_category_id', 'article_category_category_index');
        });
    }

    public function down(): void
    {
        Schema::table('article_category', function (Blueprint $table) {
            $table->dropIndex('article_category_category_index');
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->dropIndex('articles_listing_index');
        });
    }
};
