<?php

namespace Tests\Feature\Filament;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class UserResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_user_resource_list_page(): void
    {
        $this->get('/admin/users')
            ->assertRedirect('/admin/login');
    }

    public function test_admin_with_view_any_user_permission_can_access_user_resource_list_page(): void
    {
        $admin = Admin::query()->create([
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'status' => 1,
        ]);
        Permission::findOrCreate('viewAny_User', 'admin');
        $admin->givePermissionTo('viewAny_User');

        $this->actingAs($admin, 'admin')
            ->get('/admin/users')
            ->assertOk();
    }
}
