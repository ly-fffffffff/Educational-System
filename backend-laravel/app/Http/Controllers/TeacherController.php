<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;

/**
 * 教师控制器
 */
class TeacherController extends BaseController
{
    /**
     * 教师列表（分页 + 搜索）
     * GET /api/teachers
     */
    public function index(Request $request)
    {
        $pageSize = max(1, min(50, (int) $request->query('pageSize', 10)));
        $keyword = trim($request->query('keyword', ''));

        $query = Teacher::query();

        if ($keyword !== '') {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('teacher_no', 'like', "%{$keyword}%")
                  ->orWhere('phone', 'like', "%{$keyword}%");
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
     * 教师详情
     * GET /api/teachers/{id}
     */
    public function show($id)
    {
        $teacher = Teacher::find($id);
        if (!$teacher) {
            return $this->error('教师不存在', 404);
        }

        return $this->success($teacher);
    }

    /**
     * 新增教师
     * POST /api/teachers
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'teacher_no' => 'required|string',
            'status' => 'required|string',
            'phone' => 'nullable|string',
            'subject' => 'nullable|string',
        ], [
            'name.required' => 'name 不能为空',
            'teacher_no.required' => 'teacher_no 不能为空',
            'status.required' => 'status 不能为空',
        ]);

        $teacher = Teacher::create($data);

        return $this->success(['id' => $teacher->id], '新增成功');
    }

    /**
     * 更新教师
     * PUT /api/teachers/{id}
     */
    public function update(Request $request, $id)
    {
        $teacher = Teacher::find($id);
        if (!$teacher) {
            return $this->error('教师不存在', 404);
        }

        $data = $request->validate([
            'name' => 'required|string',
            'teacher_no' => 'required|string',
            'status' => 'required|string',
            'phone' => 'nullable|string',
            'subject' => 'nullable|string',
        ], [
            'name.required' => 'name 不能为空',
            'teacher_no.required' => 'teacher_no 不能为空',
            'status.required' => 'status 不能为空',
        ]);

        $teacher->update($data);

        return $this->success(true, '更新成功');
    }

    /**
     * 删除教师
     * DELETE /api/teachers/{id}
     */
    public function destroy($id)
    {
        $teacher = Teacher::find($id);
        if ($teacher) {
            $teacher->delete();
        }
        return $this->success(true, '删除成功');
    }
}
