<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    private const DEFAULT_CATEGORIES = [
        'team' => 'A-tým',
        'club' => 'Klub',
        'interviews' => 'Rozhovory',
        'fans' => 'Fanoušci',
        'matches' => 'Zápasy',
    ];

    public function up(): void
    {
        Schema::create('article_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('article_category', function (Blueprint $table) {
            $table->foreignId('article_id')->constrained()->cascadeOnDelete();
            $table->foreignId('article_category_id')->constrained()->cascadeOnDelete();
            $table->primary(['article_id', 'article_category_id']);
        });

        foreach (self::DEFAULT_CATEGORIES as $slug => $name) {
            DB::table('article_categories')->insert([
                'name' => $name,
                'slug' => $slug,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $categoryIds = DB::table('article_categories')->pluck('id', 'slug');
        $legacyValues = DB::table('articles')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->pluck('category');

        foreach ($legacyValues as $value) {
            $slug = Str::slug($value);

            if ($slug === '') {
                continue;
            }

            if (! $categoryIds->has($slug)) {
                $name = self::DEFAULT_CATEGORIES[$value] ?? Str::of($value)->replace(['-', '_'], ' ')->title()->toString();
                $id = DB::table('article_categories')->insertGetId([
                    'name' => $name,
                    'slug' => $slug,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $categoryIds->put($slug, $id);
            }
        }

        foreach (DB::table('articles')->whereNotNull('category')->where('category', '!=', '')->get(['id', 'category']) as $article) {
            $slug = Str::slug($article->category);

            if ($categoryIds->has($slug)) {
                DB::table('article_category')->insert([
                    'article_id' => $article->id,
                    'article_category_id' => $categoryIds->get($slug),
                ]);
            }
        }

        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('category', 60)->default('team')->index();
        });

        foreach (DB::table('article_category')->orderBy('article_id')->get() as $assignment) {
            $category = DB::table('article_categories')->find($assignment->article_category_id);

            if ($category) {
                DB::table('articles')
                    ->where('id', $assignment->article_id)
                    ->update(['category' => $category->slug]);
            }
        }

        Schema::dropIfExists('article_category');
        Schema::dropIfExists('article_categories');
    }
};
