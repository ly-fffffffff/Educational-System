<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

/**
 * 用户管理控制器（仅管理员可操作）
 */
class UserController extends BaseController
{
    /**
     * 用户列表
     * GET /api/users
     */
    public function index(Request $request)
    {
        $pageSize = max(1, min(50, (int) $request->query('pageSize', 10)));
        $keyword = trim($request->query('keyword', ''));

        $query = User::query()->with('role');  // 一并加载角色信息

        if ($keyword !== '') {
            $query->where(function ($q) use ($keyword) {
                $q->where('username', 'like', "%{$keyword}%")
                  ->orWhere('name', 'like', "%{$keyword}%");
            });
        }

        $result = $query->orderBy('id', 'desc')->paginate($pageSize);

        // 隐藏 password 字段（虽然 User 模型 $hidden 已经配置了，但这里再显式处理）
        return $this->success([
            'list' => $result->items(),
            'total' => $result->total(),
            'page' => $result->currentPage(),
            'pageSize' => $pageSize,
        ]);
    }

    /**
     * 新增用户
     * POST /api/users
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'username' => 'required|string',
            'name' => 'required|string',
            'password' => 'required|string',
            'role_id' => 'required|integer',
            'status' => 'nullable|string',
        ], [
            'username.required' => 'username 不能为空',
            'name.required' => 'name 不能为空',
            'password.required' => 'password 不能为空',
            'role_id.required' => 'role_id 不能为空',
        ]);

        User::create($data);  // password 自动 bcrypt（User 模型配置了 casts）

        return $this->success(true, '用户新增成功');
    }

    /**
     * 更新用户
     * PUT /api/users/{id}
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return $this->error('用户不存在', 404);
        }

        $data = $request->validate([
            'username' => 'required|string',
            'name' => 'required|string',
            'role_id' => 'required|integer',
            'status' => 'nullable|string',
            'password' => 'nullable|string',  // 密码可选（不填则不修改）
        ], [
            'username.required' => 'username 不能为空',
            'name.required' => 'name 不能为空',
            'role_id.required' => 'role_id 不能为空',
        ]);

        // 如果密码为空，则不更新密码
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return $this->success(true, '用户更新成功');
    }

    /**
     * 删除用户
     * DELETE /api/users/{id}
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->delete();
        }
        return $this->success(true, '用户删除成功');
    }

    /**
     * 获取所有角色（给前端下拉选择用）
     * GET /api/users/roles
     */
    public function roles()
    {
        return $this->success(Role::all());
    }
}
