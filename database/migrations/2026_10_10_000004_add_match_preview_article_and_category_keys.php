<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const MATCH_ARTICLE_CATEGORIES = [
        'preview' => 'Preview',
        'report' => 'Report',
    ];

    public function up(): void
    {
        Schema::table('article_categories', function (Blueprint $table) {
            $table->string('system_key')->nullable()->unique();
        });

        foreach (self::MATCH_ARTICLE_CATEGORIES as $key => $name) {
            $category = DB::table('article_categories')
                ->whereRaw('LOWER(name) = ?', [strtolower($name)])
                ->orderBy('id')
                ->first(['id']);

            $categoryId = $category?->id ?? DB::table('article_categories')->insertGetId([
                'name' => $name,
                'is_active' => true,
                'is_filterable' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('article_categories')
                ->where('id', $categoryId)
                ->update(['system_key' => $key, 'updated_at' => now()]);
        }

        Schema::table('matches', function (Blueprint $table) {
            $table->foreignId('preview_article_id')->nullable()->constrained('articles')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropConstrainedForeignId('preview_article_id');
        });

        Schema::table('article_categories', function (Blueprint $table) {
            $table->dropUnique(['system_key']);
            $table->dropColumn('system_key');
        });
    }
};
