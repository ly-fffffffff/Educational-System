<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Teacher;

/**
 * 填充演示教师数据
 */
class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        Teacher::query()->delete();

        Teacher::insert([
            ['name' => '张老师', 'teacher_no' => 'T1001', 'phone' => '13811110001', 'subject' => '数学', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => '李老师', 'teacher_no' => 'T1002', 'phone' => '13811110002', 'subject' => '英语', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
