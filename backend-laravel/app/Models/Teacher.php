<?php

namespace App\Models;

/**
 * 教师模型
 */
class Teacher extends \Illuminate\Database\Eloquent\Model
{
    protected $fillable = [
        'name',
        'teacher_no',
        'phone',
        'subject',
        'status',
    ];
}
