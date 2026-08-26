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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->char('lang_locale', 16);
            $table->unsignedBigInteger('user_id')->default(0)->nullable();
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->tinyInteger('active')->default(0);
            $table->dateTime('publish_time')->nullable();
            $table->timestamps();

            $table->foreign('lang_locale')->references('locale')->on('languages');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unique(['slug', 'lang_locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
