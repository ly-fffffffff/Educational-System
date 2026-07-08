<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * 权限表
 * 存储所有可用权限，如 "班级管理"、"学生管理"
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 80)->unique();   // 权限代号，如 class.manage
            $table->string('name', 80);              // 权限名称，如 班级管理
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
