<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

/**
 * 角色权限管理控制器
 */
class RoleController extends BaseController
{
    /**
     * 角色列表
     * GET /api/roles
     */
    public function index()
    {
        return $this->success(Role::all());
    }

    /**
     * 获取某角色的权限
     * GET /api/roles/{id}/permissions
     */
    public function permissions($id)
    {
        $role = Role::with('permissions')->find($id);
        if (!$role) {
            return $this->error('角色不存在', 404);
        }

        return $this->success($role->permissions);
    }

    /**
     * 更新角色权限
     * PUT /api/roles/{id}/permissions
     */
    public function updatePermissions(Request $request, $id)
    {
        $role = Role::find($id);
        if (!$role) {
            return $this->error('角色不存在', 404);
        }

        $permissionIds = $request->input('permission_ids', []);

        // sync() = 同步多对多关系：删除旧关联，添加新关联
        // 一步搞定：先清空 role_permissions 表中该角色的所有记录，再插入新的
        $role->permissions()->sync($permissionIds);

        return $this->success(true, '权限已更新');
    }
}
