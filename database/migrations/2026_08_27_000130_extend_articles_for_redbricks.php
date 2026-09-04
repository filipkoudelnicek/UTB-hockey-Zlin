<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('excerpt', 500)->nullable()->after('title');
            $table->string('category', 60)->default('team')->after('excerpt')->index();
            $table->unsignedBigInteger('featured_media_id')->nullable()->after('category')->index();
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['excerpt', 'category', 'featured_media_id']);
        });
    }
};
