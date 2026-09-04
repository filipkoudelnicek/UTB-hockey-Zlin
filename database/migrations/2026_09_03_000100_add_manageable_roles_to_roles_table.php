<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_management', function (Blueprint $table): void {
            $table->foreignId('manager_role_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('manageable_role_id')->constrained('roles')->cascadeOnDelete();
            $table->primary(['manager_role_id', 'manageable_role_id']);
        });

        $adminRoleId = DB::table('roles')->where('name', 'admin')->value('id');
        $manageableRoleIds = DB::table('roles')
            ->where('name', '!=', 'admin')
            ->where('is_administrator', false)
            ->pluck('id');

        if ($adminRoleId && $manageableRoleIds->isNotEmpty()) {
            DB::table('role_management')->insert(
                $manageableRoleIds->map(fn (int $roleId): array => [
                    'manager_role_id' => $adminRoleId,
                    'manageable_role_id' => $roleId,
                ])->all(),
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('role_management');
    }
};