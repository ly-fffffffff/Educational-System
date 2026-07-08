<template>
  <div class="page-wrap">
    <div class="toolbar">
      <el-input v-model="filters.keyword" placeholder="搜索班级名/年级/班主任" clearable style="width: 300px" @keyup.enter="loadData" />
      <div>
        <el-button type="primary" @click="openDialog()">新增班级</el-button>
        <el-button @click="loadData">刷新</el-button>
      </div>
    </div>

    <el-table :data="list" border stripe>
      <el-table-column prop="name" label="班级名称" />
      <el-table-column prop="grade" label="年级" />
      <el-table-column prop="teacher_name" label="班主任" />
      <el-table-column prop="student_count" label="人数" width="100" />
      <el-table-column label="状态" width="100">
        <template #default="scope">
          <el-tag :type="scope.row.status === 'active' ? 'success' : 'info'">{{ scope.row.status === 'active' ? '启用' : '停用' }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column label="操作" width="280" fixed="right">
        <template #default="scope">
          <el-button link type="primary" @click="showDetail(scope.row.id)">详情</el-button>
          <el-button link type="primary" @click="openDialog(scope.row)">编辑</el-button>
          <el-button link type="danger" @click="removeRow(scope.row.id)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>

    <div style="margin-top: 16px; display:flex; justify-content:flex-end">
      <el-pagination v-model:current-page="page" v-model:page-size="pageSize" :total="total" layout="prev, pager, next, sizes, total" :page-sizes="[10, 20, 50]" @current-change="loadData" @size-change="loadData" />
    </div>

    <el-dialog v-model="visible" :title="dialogTitle" width="520px">
      <el-form :model="form" label-width="100px">
        <el-form-item label="班级名称"><el-input v-model="form.name" /></el-form-item>
        <el-form-item label="年级"><el-input v-model="form.grade" /></el-form-item>
        <el-form-item label="班主任"><el-input v-model="form.teacher_name" /></el-form-item>
        <el-form-item label="状态">
          <el-select v-model="form.status">
            <el-option label="启用" value="active" />
            <el-option label="停用" value="disabled" />
          </el-select>
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="visible = false">取消</el-button>
        <el-button type="primary" @click="save">保存</el-button>
      </template>
    </el-dialog>

    <el-drawer v-model="detailVisible" title="班级详情" size="50%">
      <el-descriptions :column="2" border>
        <el-descriptions-item label="班级名称">{{ detail.name }}</el-descriptions-item>
        <el-descriptions-item label="年级">{{ detail.grade }}</el-descriptions-item>
        <el-descriptions-item label="班主任">{{ detail.teacher_name }}</el-descriptions-item>
        <el-descriptions-item label="学生人数">{{ detail.student_count }}</el-descriptions-item>
        <el-descriptions-item label="状态">{{ detail.status === 'active' ? '启用' : '停用' }}</el-descriptions-item>
      </el-descriptions>

      <el-divider>班级学生</el-divider>
      <el-table :data="detail.students || []" border stripe size="small">
        <el-table-column prop="name" label="姓名" />
        <el-table-column prop="student_no" label="学号" />
        <el-table-column prop="gender" label="性别" width="80" />
        <el-table-column prop="phone" label="电话" />
        <el-table-column label="状态" width="100">
          <template #default="scope">
            <el-tag :type="scope.row.status === 'active' ? 'success' : 'info'">{{ scope.row.status === 'active' ? '启用' : '停用' }}</el-tag>
          </template>
        </el-table-column>
      </el-table>
    </el-drawer>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { deleteClassApi, listClassesApi, saveClassApi, updateClassApi } from '../../api'
import http from '../../utils/http'

const list = ref([])
const total = ref(0)
const page = ref(1)
const pageSize = ref(10)
const filters = reactive({ keyword: '' })
const visible = ref(false)
const currentId = ref(null)
const dialogTitle = ref('新增班级')
const form = reactive({ name: '', grade: '', teacher_name: '', status: 'active' })
const detailVisible = ref(false)
const detail = reactive({ name: '', grade: '', teacher_name: '', student_count: 0, status: 'active', students: [] })

const loadData = async () => {
  const res = await listClassesApi({ page: page.value, pageSize: pageSize.value, keyword: filters.keyword })
  list.value = res.data.list
  total.value = res.data.total
}

const openDialog = (row) => {
  currentId.value = row?.id || null
  dialogTitle.value = currentId.value ? '编辑班级' : '新增班级'
  Object.assign(form, row || { name: '', grade: '', teacher_name: '', status: 'active' })
  visible.value = true
}

const save = async () => {
  const payload = { name: form.name, grade: form.grade, teacher_name: form.teacher_name, status: form.status }
  if (currentId.value) {
    await updateClassApi(currentId.value, payload)
  } else {
    await saveClassApi(payload)
  }
  ElMessage.success('保存成功')
  visible.value = false
  loadData()
}

const removeRow = async (id) => {
  await ElMessageBox.confirm('确认删除这条班级记录吗？', '提示')
  await deleteClassApi(id)
  ElMessage.success('删除成功')
  loadData()
}

const showDetail = async (id) => {
  const res = await http.get(`/classes/${id}`)
  Object.assign(detail, res.data)
  detailVisible.value = true
}

onMounted(loadData)
</script>