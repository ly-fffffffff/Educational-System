<?php

namespace App\Models;

/**
 * 学生模型
 */
class Student extends \Illuminate\Database\Eloquent\Model
{
    protected $fillable = [
        'name',
        'student_no',
        'gender',
        'phone',
        'class_id',
        'status',
    ];

    /*
     * 学生属于一个班级
     * 示例：$student->class->name 获取学生所在班级名称
     */
    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }
}
