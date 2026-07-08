<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

/**
 * 填充角色-权限关联
 *
 * 管理员(role_id=1)：拥有所有权限
 * 老师(role_id=2)：只有班级管理和学生管理权限
 */
class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::where('code', 'admin')->first();
        $teacher = Role::where('code', 'teacher')->first();

        // 管理员 → 所有权限
        $admin->permissions()->sync(Permission::pluck('id')->toArray());

        // 老师 → 班级管理 + 学生管理
        $teacherPermissions = Permission::whereIn('code', ['class.manage', 'student.manage'])->pluck('id')->toArray();
        $teacher->permissions()->sync($teacherPermissions);
    }
}
