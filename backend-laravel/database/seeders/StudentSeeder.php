<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Classes;

/**
 * 填充演示学生数据
 */
class StudentSeeder extends Seeder
{
    public function run(): void
    {
        Student::truncate();

        Student::insert([
            ['name' => '王小明', 'student_no' => 'S1001', 'gender' => '男', 'phone' => '13800000001', 'class_id' => 1, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => '李小红', 'student_no' => 'S1002', 'gender' => '女', 'phone' => '13800000002', 'class_id' => 1, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => '赵小强', 'student_no' => 'S2001', 'gender' => '男', 'phone' => '13800000003', 'class_id' => 2, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
        ]);

        Classes::where('id', 1)->update(['student_count' => Student::where('class_id', 1)->count()]);
        Classes::where('id', 2)->update(['student_count' => Student::where('class_id', 2)->count()]);
    }
}
