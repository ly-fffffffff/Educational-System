<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * 角色表
 * 存储系统角色，如"管理员"、"老师"
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();   // 角色代号，如 admin, teacher
            $table->string('name', 50);              // 角色名称，如 管理员、老师
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
