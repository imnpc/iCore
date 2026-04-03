<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * 用户模型工厂。
 *
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * 工厂当前复用的密码哈希。
     */
    protected static ?string $password;

    /**
     * 定义模型的默认状态。
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'mobile' => fake()->unique()->numerify('1##########'),
            'status' => fake()->numberBetween(0, 1),
            'parent_id' => 0,
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * 将邮箱标记为未验证状态。
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
