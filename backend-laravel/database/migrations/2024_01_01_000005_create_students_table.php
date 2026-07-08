<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * 学生表
 * class_id 是外键，关联到班级表
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);                              // 姓名
            $table->string('student_no', 50)->unique();              // 学号，唯一
            $table->string('gender', 10);                            // 性别：男/女
            $table->string('phone', 30)->default('');               // 联系电话
            $table->integer('class_id')->default(0);                 // 所属班级ID
            $table->string('status', 20)->default('active');         // 状态：active/inactive
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
