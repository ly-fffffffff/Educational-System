<template>
  <div class="page-wrap">
    <div class="toolbar">
      <el-input v-model="keyword" placeholder="搜索教师姓名/工号/电话" clearable style="width: 300px" @keyup.enter="loadData" />
      <div>
        <el-button type="primary" @click="openDialog()">新增教师</el-button>
        <el-button @click="loadData">刷新</el-button>
      </div>
    </div>
    <el-table :data="list" border stripe>
      <el-table-column prop="name" label="姓名" />
      <el-table-column prop="teacher_no" label="工号" />
      <el-table-column prop="phone" label="电话" />
      <el-table-column prop="subject" label="学科" />
      <el-table-column label="状态" width="100">
        <template #default="scope">
          <el-tag :type="scope.row.status === 'active' ? 'success' : 'info'">{{ scope.row.status === 'active' ? '启用' : '停用' }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column label="操作" width="220" fixed="right">
        <template #default="scope">
          <el-button link type="primary" @click="openDialog(scope.row)">编辑</el-button>
          <el-button link type="danger" @click="removeRow(scope.row.id)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>
    <div style="margin-top: 16px; display:flex; justify-content:flex-end">
      <el-pagination v-model:current-page="page" v-model:page-size="pageSize" :total="total" layout="prev, pager, next, sizes, total" :page-sizes="[10, 20, 50]" @current-change="loadData" @size-change="loadData" />
    </div>

    <el-dialog v-model="visible" :title="dialogTitle" width="560px">
      <el-form :model="form" label-width="100px">
        <el-form-item label="姓名"><el-input v-model="form.name" /></el-form-item>
        <el-form-item label="工号"><el-input v-model="form.teacher_no" /></el-form-item>
        <el-form-item label="电话"><el-input v-model="form.phone" /></el-form-item>
        <el-form-item label="学科"><el-input v-model="form.subject" /></el-form-item>
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
  </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { deleteTeacherApi, listTeachersApi, saveTeacherApi, updateTeacherApi } from '../../api'

const list = ref([])
const total = ref(0)
const page = ref(1)
const pageSize = ref(10)
const keyword = ref('')
const visible = ref(false)
const currentId = ref(null)
const dialogTitle = ref('新增教师')
const form = reactive({ name: '', teacher_no: '', phone: '', subject: '', status: 'active' })

const loadData = async () => {
  const res = await listTeachersApi({ page: page.value, pageSize: pageSize.value, keyword: keyword.value })
  list.value = res.data.list
  total.value = res.data.total
}

const openDialog = (row) => {
  currentId.value = row?.id || null
  dialogTitle.value = currentId.value ? '编辑教师' : '新增教师'
  Object.assign(form, row || { name: '', teacher_no: '', phone: '', subject: '', status: 'active' })
  visible.value = true
}

const save = async () => {
  if (currentId.value) {
    await updateTeacherApi(currentId.value, form)
  } else {
    await saveTeacherApi(form)
  }
  ElMessage.success('保存成功')
  visible.value = false
  loadData()
}

const removeRow = async (id) => {
  await ElMessageBox.confirm('确认删除这条教师记录吗？', '提示')
  await deleteTeacherApi(id)
  ElMessage.success('删除成功')
  loadData()
}

onMounted(loadData)
</script>