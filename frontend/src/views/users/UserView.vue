<template>
  <div class="page-wrap">
    <div class="toolbar">
      <el-input v-model="keyword" placeholder="搜索账号/姓名" clearable style="width: 300px" @keyup.enter="loadData" />
      <div>
        <el-button type="primary" @click="openDialog()">新增账号</el-button>
        <el-button @click="loadData">刷新</el-button>
      </div>
    </div>

    <el-table :data="list" border stripe>
      <el-table-column prop="username" label="用户名" />
      <el-table-column prop="name" label="姓名" />
      <el-table-column prop="role_name" label="角色" width="120" />
      <el-table-column prop="status" label="状态" width="100">
        <template #default="scope">
          <el-tag :type="scope.row.status === 'enabled' ? 'success' : 'info'">{{ scope.row.status === 'enabled' ? '启用' : '停用' }}</el-tag>
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
        <el-form-item label="用户名"><el-input v-model="form.username" /></el-form-item>
        <el-form-item label="姓名"><el-input v-model="form.name" /></el-form-item>
        <el-form-item label="密码"><el-input v-model="form.password" type="password" :placeholder="currentId ? '留空表示不修改' : ''" /></el-form-item>
        <el-form-item label="角色ID"><el-input-number v-model="form.role_id" :min="1" :max="99" style="width: 100%" /></el-form-item>
        <el-form-item label="状态">
          <el-select v-model="form.status">
            <el-option label="启用" value="enabled" />
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
import { deleteUserApi, listUsersApi, saveUserApi, updateUserApi } from '../../api'

const list = ref([])
const total = ref(0)
const page = ref(1)
const pageSize = ref(10)
const keyword = ref('')
const visible = ref(false)
const currentId = ref(null)
const dialogTitle = ref('新增账号')
const form = reactive({ username: '', name: '', password: '', role_id: 1, status: 'enabled' })

const loadData = async () => {
  const res = await listUsersApi({ page: page.value, pageSize: pageSize.value, keyword: keyword.value })
  list.value = res.data.list
  total.value = res.data.total
}

const openDialog = (row) => {
  currentId.value = row?.id || null
  dialogTitle.value = currentId.value ? '编辑账号' : '新增账号'
  Object.assign(form, {
    username: row?.username || '',
    name: row?.name || '',
    password: '',
    role_id: row?.role_id || 1,
    status: row?.status || 'enabled'
  })
  visible.value = true
}

const save = async () => {
  const payload = { ...form }
  if (currentId.value && !payload.password) {
    delete payload.password
  }
  if (currentId.value) {
    await updateUserApi(currentId.value, payload)
  } else {
    await saveUserApi(payload)
  }
  ElMessage.success('保存成功')
  visible.value = false
  loadData()
}

const removeRow = async (id) => {
  await ElMessageBox.confirm('确认删除这条账号记录吗？', '提示')
  await deleteUserApi(id)
  ElMessage.success('删除成功')
  loadData()
}

onMounted(loadData)
</script>
