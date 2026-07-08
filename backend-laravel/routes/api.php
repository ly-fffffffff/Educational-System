<?php

use Illuminate\Support\Facades\Route;

/*
 * ========================================
 * API 路由
 * ========================================
 *
 * Laravel 概念：
 * - routes/api.php 中的路由自动带 /api 前缀
 * - auth:sanctum 中间件 = Sanctum Token 认证（保护路由需要登录）
 * - permission:xxx 中间件 = 自定义权限检查
 * - Route::apiResource() = 自动生成 RESTful 增删改查路由
 */

// ===== 不需要登录的接口 =====
Route::prefix('auth')->group(function () {
    Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);
});

// ===== 需要登录的接口 =====
Route::middleware('auth:sanctum')->group(function () {

    // 认证相关
    Route::post('/auth/logout', [\App\Http\Controllers\AuthController::class, 'logout']);
    Route::get('/auth/me', [\App\Http\Controllers\AuthController::class, 'me']);

    // 控制台统计
    Route::get('/dashboard/stats', [\App\Http\Controllers\DashboardController::class, 'stats']);

    // 班级管理 — 增删改需要 class.manage 权限
    Route::get('/classes', [\App\Http\Controllers\ClassController::class, 'index']);
    Route::get('/classes/{id}', [\App\Http\Controllers\ClassController::class, 'show']);
    Route::post('/classes', [\App\Http\Controllers\ClassController::class, 'store'])
        ->middleware('permission:class.manage');
    Route::put('/classes/{id}', [\App\Http\Controllers\ClassController::class, 'update'])
        ->middleware('permission:class.manage');
    Route::delete('/classes/{id}', [\App\Http\Controllers\ClassController::class, 'destroy'])
        ->middleware('permission:class.manage');

    // 学生管理 — 增删改需要 student.manage 权限
    Route::get('/students', [\App\Http\Controllers\StudentController::class, 'index']);
    Route::get('/students/{id}', [\App\Http\Controllers\StudentController::class, 'show']);
    Route::post('/students', [\App\Http\Controllers\StudentController::class, 'store'])
        ->middleware('permission:student.manage');
    Route::put('/students/{id}', [\App\Http\Controllers\StudentController::class, 'update'])
        ->middleware('permission:student.manage');
    Route::post('/students/{id}/transfer', [\App\Http\Controllers\StudentController::class, 'transfer'])
        ->middleware('permission:student.manage');
    Route::delete('/students/{id}', [\App\Http\Controllers\StudentController::class, 'destroy'])
        ->middleware('permission:student.manage');

    // 教师管理 — 增删改需要 teacher.manage 权限
    Route::get('/teachers', [\App\Http\Controllers\TeacherController::class, 'index']);
    Route::get('/teachers/{id}', [\App\Http\Controllers\TeacherController::class, 'show']);
    Route::post('/teachers', [\App\Http\Controllers\TeacherController::class, 'store'])
        ->middleware('permission:teacher.manage');
    Route::put('/teachers/{id}', [\App\Http\Controllers\TeacherController::class, 'update'])
        ->middleware('permission:teacher.manage');
    Route::delete('/teachers/{id}', [\App\Http\Controllers\TeacherController::class, 'destroy'])
        ->middleware('permission:teacher.manage');

    // 用户管理 — 需要 user.manage 权限
    Route::get('/users', [\App\Http\Controllers\UserController::class, 'index'])
        ->middleware('permission:user.manage');
    Route::post('/users', [\App\Http\Controllers\UserController::class, 'store'])
        ->middleware('permission:user.manage');
    Route::put('/users/{id}', [\App\Http\Controllers\UserController::class, 'update'])
        ->middleware('permission:user.manage');
    Route::delete('/users/{id}', [\App\Http\Controllers\UserController::class, 'destroy'])
        ->middleware('permission:user.manage');
    Route::get('/users/roles', [\App\Http\Controllers\UserController::class, 'roles']);

    // 角色权限管理 — 需要 role.manage 权限
    Route::get('/roles', [\App\Http\Controllers\RoleController::class, 'index'])
        ->middleware('permission:role.manage');
    Route::get('/roles/{id}/permissions', [\App\Http\Controllers\RoleController::class, 'permissions'])
        ->middleware('permission:role.manage');
    Route::put('/roles/{id}/permissions', [\App\Http\Controllers\RoleController::class, 'updatePermissions'])
        ->middleware('permission:role.manage');

    // 权限列表 — 需要 role.manage 权限
    Route::get('/permissions', [\App\Http\Controllers\PermissionController::class, 'index'])
        ->middleware('permission:role.manage');
});

// 健康检查（无需认证）
Route::get('/health', function () {
    return response()->json(['code' => 0, 'message' => 'ok', 'data' => ['ok' => true]]);
});
