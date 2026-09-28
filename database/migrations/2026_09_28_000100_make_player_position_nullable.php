<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // clean up bad data from manual imports before relaxing the constraint
        DB::table('players')->where('position', '')->update(['position' => null]);

        Schema::table('players', function (Blueprint $table) {
            $table->string('position', 30)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->string('position', 30)->nullable(false)->change();
        });
    }
};
