<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Classes;

/**
 * 填充演示班级数据
 */
class ClassSeeder extends Seeder
{
    public function run(): void
    {
        Classes::truncate();

        Classes::insert([
            ['name' => '一班', 'grade' => '一年级', 'teacher_name' => '张老师', 'student_count' => 0, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => '二班', 'grade' => '二年级', 'teacher_name' => '李老师', 'student_count' => 0, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
