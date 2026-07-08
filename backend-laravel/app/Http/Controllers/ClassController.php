<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\Student;
use Illuminate\Http\Request;

/**
 * 班级控制器 — 班级的增删改查
 *
 * Laravel 概念：
 * - Classes::query() = 开始构建 Eloquent 查询
 * - ->paginate() = 分页，自动处理 page 参数
 * - ->withCount('students') = 统计关联数据数量，不需要手动 COUNT
 * - request()->validate() = Laravel 内置验证
 */
class ClassController extends BaseController
{
    /**
     * 班级列表（分页 + 搜索）
     * GET /api/classes?page=1&pageSize=10&keyword=xxx
     */
    public function index(Request $request)
    {
        $pageSize = max(1, min(50, (int) $request->query('pageSize', 10)));
        $keyword = trim($request->query('keyword', ''));

        // Eloquent 查询构建器
        $query = Classes::query();

        // 关键词搜索：在 name、grade、teacher_name 中模糊搜索
        if ($keyword !== '') {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('grade', 'like', "%{$keyword}%")
                  ->orWhere('teacher_name', 'like', "%{$keyword}%");
            });
        }

        $result = $query->orderBy('id', 'desc')->paginate($pageSize);

        return $this->success([
            'list' => $result->items(),
            'total' => $result->total(),
            'page' => $result->currentPage(),
            'pageSize' => $pageSize,
        ]);
    }

    /**
     * 班级详情（含学生列表）
     * GET /api/classes/{id}
     */
    public function show($id)
    {
        $class = Classes::with('students')->find($id);

        if (!$class) {
            return $this->error('班级不存在', 404);
        }

        return $this->success([
            'id' => $class->id,
            'name' => $class->name,
            'grade' => $class->grade,
            'teacher_name' => $class->teacher_name,
            'student_count' => $class->students->count(),
            'status' => $class->status,
            'created_at' => $class->created_at,
            'updated_at' => $class->updated_at,
            'students' => $class->students->map(function ($s) {
                return [
                    'id' => $s->id,
                    'name' => $s->name,
                    'student_no' => $s->student_no,
                    'gender' => $s->gender,
                    'phone' => $s->phone,
                    'status' => $s->status,
                ];
            }),
        ]);
    }

    /**
     * 新增班级
     * POST /api/classes
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'grade' => 'required|string',
            'status' => 'required|string',
            'teacher_name' => 'nullable|string',
        ], [
            'name.required' => 'name 不能为空',
            'grade.required' => 'grade 不能为空',
            'status.required' => 'status 不能为空',
        ]);

        $class = Classes::create([
            'name' => $data['name'],
            'grade' => $data['grade'],
            'teacher_name' => $data['teacher_name'] ?? '',
            'student_count' => 0,
            'status' => $data['status'],
        ]);

        return $this->success(['id' => $class->id], '新增成功');
    }

    /**
     * 更新班级
     * PUT /api/classes/{id}
     */
    public function update(Request $request, $id)
    {
        $class = Classes::find($id);
        if (!$class) {
            return $this->error('班级不存在', 404);
        }

        $data = $request->validate([
            'name' => 'required|string',
            'grade' => 'required|string',
            'status' => 'required|string',
            'teacher_name' => 'nullable|string',
        ], [
            'name.required' => 'name 不能为空',
            'grade.required' => 'grade 不能为空',
            'status.required' => 'status 不能为空',
        ]);

        $class->update($data);

        return $this->success(true, '更新成功');
    }

    /**
     * 删除班级
     * DELETE /api/classes/{id}
     */
    public function destroy($id)
    {
        $class = Classes::find($id);
        if ($class) {
            $class->delete();
        }
        return $this->success(true, '删除成功');
    }
}
