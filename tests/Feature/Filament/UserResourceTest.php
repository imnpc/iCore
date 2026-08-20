<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\Users\UserResource;
use App\Models\Admin;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class UserResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_user_resource_list_page(): void
    {
        $this->get(UserResource::getUrl())
            ->assertRedirect('/admin/login');
    }

    public function test_admin_with_view_any_user_permission_can_access_user_resource_list_page(): void
    {
        $admin = new Admin([
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'status' => 1,
        ]);
        $admin->save();
        Permission::findOrCreate('viewAny_User', 'admin');
        $admin->givePermissionTo('viewAny_User');

        $authenticatableAdmin = $admin instanceof Authenticatable ? $admin : null;

        if ($authenticatableAdmin === null) {
            $this->fail('管理员模型必须实现认证接口。');
        }

        $this->actingAs($authenticatableAdmin, 'admin')
            ->get(UserResource::getUrl())
            ->assertOk();
    }

    public function test_filament_login_page_uses_the_requested_supported_locale(): void
    {
        $this->get('/admin/login?locale=en')
            ->assertOk()
            ->assertSee('Username');

        $this->get('/admin/login?locale=zh_CN')
            ->assertOk()
            ->assertSee('用户名');
    }
}
