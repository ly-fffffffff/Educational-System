<?php

namespace App\Models;

/**
 * 班级模型
 * 注意：类名为 Classes 而不是 Class，因为 class 是 PHP 关键字
 */
class Classes extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'classes';  // 指定表名（因为类名与表名不一致）

    protected $fillable = [
        'name',
        'grade',
        'teacher_name',
        'student_count',
        'status',
    ];

    /*
     * 一个班级拥有多个学生
     * 示例：$class->students 获取班级下所有学生
     */
    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }
}
