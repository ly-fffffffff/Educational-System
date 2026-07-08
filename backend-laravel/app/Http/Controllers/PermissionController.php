<?php

namespace App\Http\Controllers;

use App\Models\Permission;

/**
 * 权限控制器
 */
class PermissionController extends BaseController
{
    /**
     * 权限列表
     * GET /api/permissions
     */
    public function index()
    {
        return $this->success(Permission::orderBy('id')->get());
    }
}
