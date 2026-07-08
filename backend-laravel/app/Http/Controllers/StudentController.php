<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

/**
 * 学生控制器
 */
class StudentController extends BaseController
{
    private function syncClassStudentCount($classId): void
    {
        if (!$classId) return;
        \App\Models\Classes::where('id', $classId)->update([
            'student_count' => Student::where('class_id', $classId)->count(),
        ]);
    }
    /**
     * 学生列表（分页 + 搜索 + 按班级筛选）
     * GET /api/students?page=1&pageSize=10&keyword=xxx&class_id=1
     */
    public function index(Request $request)
    {
        $pageSize = max(1, min(50, (int) $request->query('pageSize', 10)));
        $keyword = trim($request->query('keyword', ''));
        $classId = (int) $request->query('class_id', 0);

        $query = Student::query();

        // 关键词搜索
        if ($keyword !== '') {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('student_no', 'like', "%{$keyword}%")
                  ->orWhere('phone', 'like', "%{$keyword}%");
            });
        }

        // 按班级筛选
        if ($classId > 0) {
            $query->where('class_id', $classId);
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
     * 学生详情（含所属班级信息）
     * GET /api/students/{id}
     */
    public function show($id)
    {
        $student = Student::with('class')->find($id);

        if (!$student) {
            return $this->error('学生不存在', 404);
        }

        $data = $student->toArray();
        $data['class'] = $student->class ? [
            'id' => $student->class->id,
            'name' => $student->class->name,
            'grade' => $student->class->grade,
            'teacher_name' => $student->class->teacher_name,
            'student_count' => $student->class->students()->count(),
            'status' => $student->class->status,
        ] : null;

        return $this->success($data);
    }

    /**
     * 新增学生
     * POST /api/students
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'student_no' => 'required|string',
            'gender' => 'required|string',
            'status' => 'required|string',
            'phone' => 'nullable|string',
            'class_id' => 'nullable|integer',
        ], [
            'name.required' => 'name 不能为空',
            'student_no.required' => 'student_no 不能为空',
            'gender.required' => 'gender 不能为空',
            'status.required' => 'status 不能为空',
        ]);

        $student = Student::create($data);

        $this->syncClassStudentCount($student->class_id);

        return $this->success(['id' => $student->id], '新增成功');
    }

    /**
     * 更新学生
     * PUT /api/students/{id}
     */
    public function update(Request $request, $id)
    {
        $student = Student::find($id);
        if (!$student) {
            return $this->error('学生不存在', 404);
        }

        $data = $request->validate([
            'name' => 'required|string',
            'student_no' => 'required|string',
            'gender' => 'required|string',
            'status' => 'required|string',
            'phone' => 'nullable|string',
            'class_id' => 'nullable|integer',
        ], [
            'name.required' => 'name 不能为空',
            'student_no.required' => 'student_no 不能为空',
            'gender.required' => 'gender 不能为空',
            'status.required' => 'status 不能为空',
        ]);

        $oldClassId = $student->class_id;
        $student->update($data);

        $newClassId = $data['class_id'] ?? null;
        if ((int) $newClassId !== (int) $oldClassId) {
            $this->syncClassStudentCount($oldClassId);
            $this->syncClassStudentCount((int) $newClassId);
        }

        return $this->success(true, '更新成功');
    }

    /**
     * 学生转班
     * POST /api/students/{id}/transfer
     */
    public function transfer(Request $request, $id)
    {
        $student = Student::find($id);
        if (!$student) {
            return $this->error('学生不存在', 404);
        }

        $data = $request->validate([
            'class_id' => 'required|integer',
        ], [
            'class_id.required' => 'class_id 不能为空',
        ]);

        $oldClassId = $student->class_id;

        $student->update(['class_id' => $data['class_id']]);

        $this->syncClassStudentCount($oldClassId);
        $this->syncClassStudentCount($data['class_id']);

        return $this->success(true, '转班成功');
    }

    /**
     * 删除学生
     * DELETE /api/students/{id}
     */
    public function destroy($id)
    {
        $student = Student::find($id);
        if ($student) {
            $classId = $student->class_id;
            $student->delete();
            $this->syncClassStudentCount($classId);
        }
        return $this->success(true, '删除成功');
    }
}
