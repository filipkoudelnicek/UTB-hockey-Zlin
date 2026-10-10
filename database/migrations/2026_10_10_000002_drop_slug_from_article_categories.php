<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('article_categories', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }

    public function down(): void
    {
        Schema::table('article_categories', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique();
        });

        $usedSlugs = [];

        foreach (DB::table('article_categories')->orderBy('id')->get(['id', 'name']) as $category) {
            $slug = Str::slug($category->name) ?: 'category';

            if (isset($usedSlugs[$slug])) {
                $slug .= '-'.$category->id;
            }

            $usedSlugs[$slug] = true;
            DB::table('article_categories')
                ->where('id', $category->id)
                ->update(['slug' => $slug]);
        }
    }
};
