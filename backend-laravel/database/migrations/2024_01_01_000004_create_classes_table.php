<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * 班级表
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);                             // 班级名称，如"一班"
            $table->string('grade', 50);                             // 年级，如"一年级"
            $table->string('teacher_name', 50)->default('');         // 班主任姓名
            $table->integer('student_count')->default(0);            // 学生人数
            $table->string('status', 20)->default('active');         // 状态：active/inactive
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
