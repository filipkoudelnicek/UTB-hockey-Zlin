<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_can_edit_their_own_account(): void
    {
        $root = User::factory()->create();
        $root->roles()->attach(Role::create([
            'name' => 'root',
            'is_administrator' => true,
        ]));

        $this->actingAs($root);

        $this->assertTrue($root->canManageUser($root));
    }

    public function test_non_administrator_can_edit_their_own_account(): void
    {
        $user = User::factory()->create();
        $user->roles()->attach(Role::create([
            'name' => 'Editor obsahu',
            'is_administrator' => false,
        ]));

        $this->actingAs($user);

        $this->assertTrue($user->canManageUser($user));
    }

    public function test_user_cannot_assign_themselves_a_higher_role(): void
    {
        $user = User::factory()->create();
        $editor = Role::create([
            'name' => 'Editor obsahu',
            'is_administrator' => false,
        ]);
        $root = Role::create([
            'name' => 'root',
            'is_administrator' => true,
        ]);
        $user->roles()->attach($editor);

        $this->assertTrue($user->canAssignRoleTo($user, $editor));
        $this->assertFalse($user->canAssignRoleTo($user, $root));
    }
}
