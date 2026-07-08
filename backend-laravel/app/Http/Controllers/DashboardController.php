<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;

/**
 * 控制台统计
 */
class DashboardController extends BaseController
{
    /**
     * 获取首页统计数据
     * GET /api/dashboard/stats
     */
    public function stats()
    {
        return $this->success([
            'classes' => Classes::count(),   // count() = SELECT COUNT(*)
            'students' => Student::count(),
            'teachers' => Teacher::count(),
            'users' => User::count(),
        ]);
    }
}
