<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->string('full_slug')->nullable()->index();
            $table->char('lang_locale', 16);
            $table->string('type')->default('text');
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->tinyInteger('active')->default(0);
            $table->timestamps();

            $table->foreign('lang_locale')->references('locale')->on('languages');
            $table->unique(['slug', 'lang_locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
