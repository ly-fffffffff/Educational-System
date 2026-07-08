<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * 教师表
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);                              // 姓名
            $table->string('teacher_no', 50)->unique();              // 教师编号，唯一
            $table->string('phone', 30)->default('');               // 联系电话
            $table->string('subject', 50)->default('');             // 任教科目
            $table->string('status', 20)->default('active');         // 状态：active/inactive
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
