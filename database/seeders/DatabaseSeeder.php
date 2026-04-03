<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * 数据库总填充器。
 */
class DatabaseSeeder extends Seeder
{
    /**
     * 执行应用数据库填充。
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
        ]);
    }
}
