<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * 角色-权限中间表（多对多关系）
 * 一个角色可以有多个权限，一个权限可以属于多个角色
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->integer('role_id');
            $table->integer('permission_id');
            // 确保一个角色不能重复关联同一个权限
            $table->unique(['role_id', 'permission_id'], 'uniq_role_permission');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
    }
};
