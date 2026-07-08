<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * 认证控制器 — 处理登录、登出、获取当前用户
 *
 * Laravel 概念：
 * - Hash::check() = 验证明文密码与 bcrypt 哈希是否匹配（原来用 password_verify()）
 * - $user->createToken() = Sanctum 创建 Token，返回纯文本 token
 * - auth()->user() = 获取当前已登录用户
 */
class AuthController extends BaseController
{
    /**
     * 登录
     * POST /api/auth/login
     */
    public function login(Request $request)
    {
        // 1. 验证必填字段
        $data = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'username 不能为空',
            'password.required' => 'password 不能为空',
        ]);

        // 2. 查找用户（通过 username 字段）
        $user = User::where('username', $data['username'])->first();

        // 3. 验证密码
        if (!$user || !Hash::check($data['password'], $user->password)) {
            return $this->error('用户名或密码错误', 401);
        }

        // 4. 创建 Sanctum Token（返回的 plainTextToken 只有这一次能拿到，请保存好）
        $token = $user->createToken('api-token')->plainTextToken;

        // 5. 返回用户信息 + token
        return $this->success([
            'id' => $user->id,
            'username' => $user->username,
            'name' => $user->name,
            'role_id' => $user->role_id,
            'role_code' => $user->role->code ?? '',
            'role_name' => $user->role->name ?? '',
            'permissions' => $user->getPermissionCodes(),
            'token' => $token,  // ★ Sanctum Token，前端需要存起来
        ]);
    }

    /**
     * 登出
     * POST /api/auth/logout
     */
    public function logout(Request $request)
    {
        // 删除当前使用的 Token（使 Token 失效）
        $request->user()->currentAccessToken()->delete();

        return $this->success(true, '已退出登录');
    }

    /**
     * 获取当前登录用户信息
     * GET /api/auth/me
     */
    public function me(Request $request)
    {
        $user = $request->user()->load('role');  // load() = 延迟加载关联关系

        return $this->success([
            'id' => $user->id,
            'username' => $user->username,
            'name' => $user->name,
            'role_id' => $user->role_id,
            'role_code' => $user->role->code ?? '',
            'role_name' => $user->role->name ?? '',
            'permissions' => $user->getPermissionCodes(),
        ]);
    }
}
