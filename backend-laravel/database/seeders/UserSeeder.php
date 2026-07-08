<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

/**
 * 填充默认用户
 * 管理员：admin / admin123
 * 老师：teacher1 / teacher123
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->delete();

        // User 模型的 password 字段配置了 'hashed' cast，所以可以直接传明文
        User::create([
            'username' => 'admin',
            'name' => '系统管理员',
            'password' => 'admin123',    // 自动 bcrypt 加密
            'role_id' => 1,               // 管理员角色
            'status' => 'enabled',
        ]);

        User::create([
            'username' => 'teacher1',
            'name' => '张老师',
            'password' => 'teacher123',  // 自动 bcrypt 加密
            'role_id' => 2,               // 老师角色
            'status' => 'enabled',
        ]);
    }
}
