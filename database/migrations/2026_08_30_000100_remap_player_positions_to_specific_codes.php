<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Remap legacy broad positions to the new specific position codes.
     * Defenders/forwards default to a left-side pairing; reassign exact
     * side/role per player afterwards in the admin.
     */
    public function up(): void
    {
        DB::table('players')->where('position', 'goalkeeper')->update(['position' => 'G']);
        DB::table('players')->where('position', 'defender')->update(['position' => 'LD']);
        DB::table('players')->where('position', 'forward')->update(['position' => 'C']);
    }

    public function down(): void
    {
        DB::table('players')->where('position', 'G')->update(['position' => 'goalkeeper']);
        DB::table('players')->whereIn('position', ['LD', 'RD'])->update(['position' => 'defender']);
        DB::table('players')->whereIn('position', ['C', 'LW', 'RW'])->update(['position' => 'forward']);
    }
};
