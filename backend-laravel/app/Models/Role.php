<?php

namespace App\Models;

/**
 * 角色模型
 *
 * Laravel 概念：
 * - Eloquent Model = 每个数据库表对应一个 Model 类
 * - $fillable = 白名单，指定哪些字段可以"批量赋值"（防止用户恶意提交额外字段）
 * - 关联关系：Role 拥有多个 User（hasMany），Role 拥有多个 Permission（belongsToMany）
 */
class Role extends \Illuminate\Database\Eloquent\Model
{
    public $timestamps = false;  // 角色表没有 created_at/updated_at

    protected $fillable = ['code', 'name'];

    /*
     * 一个角色拥有多个用户
     * 示例：$role->users 获取该角色下的所有用户
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /*
     * 一个角色拥有多个权限（通过 role_permissions 中间表）
     * belongsToMany = 多对多关系
     * 示例：$role->permissions 获取该角色的所有权限
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions', 'role_id', 'permission_id');
    }
}
