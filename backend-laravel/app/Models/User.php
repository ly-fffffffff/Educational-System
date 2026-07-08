<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

/**
 * 用户模型（认证用）
 *
 * Laravel 概念：
 * - 继承 Authenticatable → 可以使用 Laravel 的认证功能（登录、登出等）
 * - 使用 HasApiTokens trait → 支持 Sanctum Token 认证（API token）
 * - $hidden = 隐藏字段，调用 toArray()/toJson() 时不会输出（保护密码等敏感信息）
 * - casts() = 类型转换，password 字段自动做 bcrypt 哈希
 */
class User extends Authenticatable
{
    use HasApiTokens;

    protected $fillable = [
        'username',
        'name',
        'password',
        'role_id',
        'status',
    ];

    protected $hidden = [
        'password',
    ];

    /*
     * 字段类型转换
     * password => 'hashed' 表示赋值时自动 bcrypt 加密（Laravel 10+ 特性）
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /*
     * 用户属于一个角色
     * 示例：$user->role->name 获取用户角色名称
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /*
     * 判断用户是否为管理员（admin 角色拥有所有权限）
     */
    public function isAdmin(): bool
    {
        return $this->role && $this->role->code === 'admin';
    }

    /*
     * 判断用户是否拥有某个权限
     * 示例：$user->can('student.manage')
     */
    public function hasPermission(string $code): bool
    {
        if ($this->isAdmin()) {
            return true;  // 管理员通吃
        }

        // 通过角色 → 权限关联查询
        return $this->role ? $this->role->permissions()->where('code', $code)->exists() : false;
    }

    /*
     * 获取用户的所有权限代号列表
     */
    public function getPermissionCodes(): array
    {
        if ($this->isAdmin()) {
            return Permission::pluck('code')->toArray();
        }

        return $this->role ? $this->role->permissions()->pluck('code')->toArray() : [];
    }
}
