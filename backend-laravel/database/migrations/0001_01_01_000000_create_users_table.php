<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * 用户表迁移
 *
 * Laravel 概念：
 * - Migration = 数据库的"版本控制"，可以前进(up)和回滚(down)
 * - Schema::create() 创建表，Schema::table() 修改已有表
 * - $table->id() = BIGINT 自增主键
 * - $table->string('name', 50) = VARCHAR(50)
 * - $table->timestamps() = 自动创建 created_at 和 updated_at 两个字段
 */
return new class extends Migration
{
    public function up(): void
    {
        // 用户表：存储所有登录账号（管理员、老师等）
        Schema::create('users', function (Blueprint $table) {
            $table->id();                                          // 主键 ID，自增
            $table->string('username', 50)->unique();              // 登录用户名，唯一
            $table->string('name', 50);                            // 显示名称
            $table->string('password', 255);                       // 密码哈希（bcrypt）
            $table->integer('role_id')->default(1);                // 所属角色ID
            $table->string('status', 20)->default('enabled');     // 账号状态：enabled/disabled
            $table->timestamps();                                  // created_at, updated_at
        });

        // Session 表（Laravel 需要，用于 session 驱动）
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('sessions');
    }
};
