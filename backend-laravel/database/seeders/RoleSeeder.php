<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

/**
 * 填充角色数据
 */
class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::query()->delete();  // 清空旧数据

        Role::insert([
            ['code' => 'admin', 'name' => '管理员'],
            ['code' => 'teacher', 'name' => '老师'],
        ]);
    }
}
