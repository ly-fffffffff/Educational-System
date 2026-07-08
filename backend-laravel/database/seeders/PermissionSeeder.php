<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

/**
 * 填充权限数据
 */
class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        Permission::query()->delete();

        Permission::insert([
            ['code' => 'user.manage', 'name' => '账号管理'],
            ['code' => 'role.manage', 'name' => '角色权限'],
            ['code' => 'class.manage', 'name' => '班级管理'],
            ['code' => 'student.manage', 'name' => '学生管理'],
            ['code' => 'teacher.manage', 'name' => '教师管理'],
        ]);
    }
}
