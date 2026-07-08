<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * 权限检查中间件
 *
 * Laravel 概念：
 * - 中间件 = 请求的"安检门"，在请求进入 Controller 之前执行
 * - 可以检查用户是否登录、是否有权限等
 * - 使用方式：Route::middleware('permission:student.manage')->group(...)
 */
class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permissionCode)
    {
        $user = $request->user();

        // 未登录
        if (!$user) {
            return response()->json([
                'code' => 401,
                'message' => '请先登录',
                'data' => null,
            ], 401);
        }

        // 检查权限
        if (!$user->hasPermission($permissionCode)) {
            return response()->json([
                'code' => 403,
                'message' => '无权限访问',
                'data' => null,
            ], 403);
        }

        // 放行，继续执行下一个中间件或 Controller
        return $next($request);
    }
}
