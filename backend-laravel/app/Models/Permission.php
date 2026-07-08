<?php

namespace App\Models;

/**
 * 权限模型
 */
class Permission extends \Illuminate\Database\Eloquent\Model
{
    public $timestamps = false;

    protected $fillable = ['code', 'name'];

    /*
     * 一个权限可以被多个角色拥有
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions', 'permission_id', 'role_id');
    }
}
