<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_type_id')
                ->nullable()
                ->constrained('page_types')
                ->nullOnDelete();
            $table->string('name')->unique()->comment('Pojmenovaná route, např. homepage, article.show');
            $table->string('path')->comment('Path vzor, např. /, /{slug}, /blog/{articleSlug}');
            $table->enum('method', ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'])->default('GET');
            $table->string('controller')->nullable()->comment('FQCN controlleru');
            $table->string('action')->nullable()->comment('Název metody controlleru');
            $table->string('template')->nullable()->comment('Volitelné přepsání Blade šablony');
            $table->string('lang_locale', 10)->nullable()->comment('NULL = výchozí jazyk');
            $table->boolean('is_active')->default(true);
            $table->boolean('auto_generate')->default(true)
                ->comment('True = registruj jen pokud existuje stránka daného typu');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_routes');
    }
};
