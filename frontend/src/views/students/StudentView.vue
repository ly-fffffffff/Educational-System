<template>
  <div class="page-wrap">
    <div class="toolbar">
      <el-input v-model="filters.keyword" placeholder="搜索学生姓名/学号/电话" clearable style="width: 300px" @keyup.enter="loadData" />
      <div>
        <el-select v-model="filters.class_id" placeholder="按班级筛选" clearable style="width: 180px; margin-right: 8px">
          <el-option v-for="item in classOptions" :key="item.id" :label="item.name" :value="item.id" />
        </el-select>
        <el-button type="primary" @click="openDialog()">新增学生</el-button>
        <el-button @click="loadData">刷新</el-button>
      </div>
    </div>

    <el-table :data="list" border stripe>
      <el-table-column prop="name" label="姓名" />
      <el-table-column prop="student_no" label="学号" />
      <el-table-column prop="gender" label="性别" width="80" />
      <el-table-column prop="phone" label="电话" />
      <el-table-column label="所属班级">
        <template #default="scope">{{ className(scope.row.class_id) }}</template>
      </el-table-column>
      <el-table-column label="状态" width="100">
        <template #default="scope">
          <el-tag :type="scope.row.status === 'active' ? 'success' : 'info'">{{ scope.row.status === 'active' ? '启用' : '停用' }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column label="操作" width="280" fixed="right">
        <template #default="scope">
          <el-button link type="primary" @click="showDetail(scope.row.id)">详情</el-button>
          <el-button link type="warning" @click="openTransfer(scope.row)">转班</el-button>
          <el-button link type="primary" @click="openDialog(scope.row)">编辑</el-button>
          <el-button link type="danger" @click="removeRow(scope.row.id)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>

    <div style="margin-top: 16px; display:flex; justify-content:flex-end">
      <el-pagination
        v-model:current-page="page"
        v-model:page-size="pageSize"
        :total="total"
        layout="prev, pager, next, sizes, total"
        :page-sizes="[10, 20, 50]"
        @current-change="loadData"
        @size-change="loadData"
      />
    </div>

    <el-dialog v-model="visible" :title="dialogTitle" width="560px">
      <el-form :model="form" label-width="100px">
        <el-form-item label="姓名"><el-input v-model="form.name" /></el-form-item>
        <el-form-item label="学号"><el-input v-model="form.student_no" /></el-form-item>
        <el-form-item label="性别">
          <el-select v-model="form.gender">
            <el-option label="男" value="男" />
            <el-option label="女" value="女" />
          </el-select>
        </el-form-item>
        <el-form-item label="电话"><el-input v-model="form.phone" /></el-form-item>
        <el-form-item label="所属班级">
          <el-select v-model="form.class_id" filterable>
            <el-option v-for="item in classOptions" :key="item.id" :label="item.name" :value="item.id" />
          </el-select>
        </el-form-item>
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

    <el-drawer v-model="detailVisible" title="学生详情" size="520px">
      <el-descriptions :column="2" border>
        <el-descriptions-item label="姓名">{{ detail.name }}</el-descriptions-item>
        <el-descriptions-item label="学号">{{ detail.student_no }}</el-descriptions-item>
        <el-descriptions-item label="性别">{{ detail.gender }}</el-descriptions-item>
        <el-descriptions-item label="电话">{{ detail.phone }}</el-descriptions-item>
        <el-descriptions-item label="状态">{{ detail.status === 'active' ? '启用' : '停用' }}</el-descriptions-item>
        <el-descriptions-item label="所属班级">{{ detail.class?.name || className(detail.class_id) }}</el-descriptions-item>
        <el-descriptions-item label="班主任">{{ detail.class?.teacher_name || '-' }}</el-descriptions-item>
        <el-descriptions-item label="班级人数">{{ detail.class?.student_count ?? '-' }}</el-descriptions-item>
      </el-descriptions>

      <el-divider>所属班级信息</el-divider>
      <el-descriptions :column="2" border>
        <el-descriptions-item label="年级">{{ detail.class?.grade || '-' }}</el-descriptions-item>
        <el-descriptions-item label="班级状态">{{ detail.class?.status === 'active' ? '启用' : '停用' }}</el-descriptions-item>
      </el-descriptions>
    </el-drawer>

    <el-dialog v-model="transferVisible" title="学生转班" width="420px">
      <el-form :model="transferForm" label-width="100px">
        <el-form-item label="当前学生">{{ transferTarget.name }}</el-form-item>
        <el-form-item label="新班级">
          <el-select v-model="transferForm.class_id" filterable style="width: 100%">
            <el-option v-for="item in classOptions" :key="item.id" :label="item.name" :value="item.id" />
          </el-select>
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="transferVisible = false">取消</el-button>
        <el-button type="primary" @click="confirmTransfer">确认转班</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { deleteStudentApi, getStudentDetailApi, listClassesApi, listStudentsApi, saveStudentApi, transferStudentApi, updateStudentApi } from '../../api'

const list = ref([])
const total = ref(0)
const page = ref(1)
const pageSize = ref(10)
const filters = reactive({ keyword: '', class_id: '' })
const visible = ref(false)
const currentId = ref(null)
const dialogTitle = ref('新增学生')
const classOptions = ref([])
const form = reactive({ name: '', student_no: '', gender: '男', phone: '', class_id: 0, status: 'active' })
const detailVisible = ref(false)
const detail = reactive({ name: '', student_no: '', gender: '', phone: '', class_id: 0, status: 'active', class: null })
const transferVisible = ref(false)
const transferTarget = reactive({ id: null, name: '' })
const transferForm = reactive({ class_id: 0 })

const className = (id) => {
  const item = classOptions.value.find((row) => Number(row.id) === Number(id))
  return item ? item.name : '-'
}

const loadClassOptions = async () => {
  const res = await listClassesApi({ page: 1, pageSize: 100, keyword: '' })
  classOptions.value = res.data.list
}

const loadData = async () => {
  const res = await listStudentsApi({ page: page.value, pageSize: pageSize.value, keyword: filters.keyword, class_id: filters.class_id })
  list.value = res.data.list
  total.value = res.data.total
}

const openDialog = (row) => {
  currentId.value = row?.id || null
  dialogTitle.value = currentId.value ? '编辑学生' : '新增学生'
  Object.assign(form, row || { name: '', student_no: '', gender: '男', phone: '', class_id: classOptions.value[0]?.id || 0, status: 'active' })
  visible.value = true
}

const showDetail = async (id) => {
  const res = await getStudentDetailApi(id)
  Object.assign(detail, res.data)
  detailVisible.value = true
}

const openTransfer = (row) => {
  transferTarget.id = row.id
  transferTarget.name = row.name
  transferForm.class_id = row.class_id
  transferVisible.value = true
}

const confirmTransfer = async () => {
  if (!transferForm.class_id) {
    ElMessage.warning('请选择新班级')
    return
  }
  await transferStudentApi(transferTarget.id, transferForm)
  ElMessage.success('转班成功')
  transferVisible.value = false
  await loadData()
}

const save = async () => {
  const payload = { name: form.name, student_no: form.student_no, gender: form.gender, phone: form.phone, class_id: form.class_id, status: form.status }
  if (currentId.value) {
    await updateStudentApi(currentId.value, payload)
  } else {
    await saveStudentApi(payload)
  }
  ElMessage.success('保存成功')
  visible.value = false
  await loadData()
}

const removeRow = async (id) => {
  await ElMessageBox.confirm('确认删除这条学生记录吗？', '提示')
  await deleteStudentApi(id)
  ElMessage.success('删除成功')
  await loadData()
}

onMounted(async () => {
  await loadClassOptions()
  await loadData()
})
</script>
