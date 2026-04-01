<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = fake('zh_CN');
        $password = Hash::make('password');

        $users = new Collection;
        for ($index = 0; $index < 20; $index++) {
            $users->push(User::query()->create([
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'mobile' => $faker->unique()->numerify('1##########'),
                'status' => $faker->boolean() ? 1 : 0,
                'parent_id' => 0,
                'avatar' => null,
                'email_verified_at' => now(),
                'password' => $password,
                'remember_token' => $faker->regexify('[A-Za-z0-9]{10}'),
            ]));
        }

        $userIds = $users->pluck('id')->all();
        foreach ($users as $user) {
            if ($faker->boolean(35)) {
                $availableParentIds = array_values(array_filter(
                    $userIds,
                    static fn (int $candidateId): bool => $candidateId !== $user->id
                ));

                if ($availableParentIds !== []) {
                    $user->forceFill([
                        'parent_id' => $faker->randomElement($availableParentIds),
                    ])->save();
                }
            }
        }
    }
}
