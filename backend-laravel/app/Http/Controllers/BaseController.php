<?php

namespace App\Http\Controllers;

/**
 * API 控制器基类
 *
 * 统一 API 响应格式，与原项目保持一致：
 * - 成功：{ code: 0, message: 'success', data: ... }
 * - 失败：{ code: 状态码, message: '...', data: null }
 */
abstract class BaseController extends \Illuminate\Routing\Controller
{
    /**
     * 成功响应
     */
    protected function success($data = null, string $message = 'success'): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'code' => 0,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * 失败响应
     */
    protected function error(string $message = 'error', int $code = 400, $data = null): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ], $code);
    }
}
