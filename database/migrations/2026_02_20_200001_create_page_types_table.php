<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_types', function (Blueprint $table) {
            $table->id();
            $table->string('handle')->unique();
            $table->string('label');
            $table->string('template');        // např. pages.service-detail
            $table->string('schema_class')->nullable(); // FQCN třídy se schématem
            $table->string('controller')->nullable();    // FQCN controlleru
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_types');
    }
};
