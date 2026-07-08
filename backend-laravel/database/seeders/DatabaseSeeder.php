<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * 数据库填充总调度
 *
 * 执行顺序很重要！必须先有角色、权限，才能创建关联和用户。
 *
 * 运行方式：
 * - php artisan db:seed                    执行所有 Seeder
 * - php artisan migrate:fresh --seed       重建所有表 + 填充数据（开发常用）
 * - php artisan migrate --seed             迁移 + 填充
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. 先创建基础数据（角色、权限）
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
        ]);

        // 2. 创建角色-权限关联（多对多中间表）
        $this->call(RolePermissionSeeder::class);

        // 3. 创建用户（依赖角色）
        $this->call(UserSeeder::class);

        // 4. 创建演示数据
        $this->call(ClassSeeder::class);
        $this->call(StudentSeeder::class);
        $this->call(TeacherSeeder::class);
    }
}
