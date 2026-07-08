<template>
  <div class="page-wrap">
    <el-row :gutter="16">
      <el-col :span="10">
        <el-card>
          <template #header>角色列表</template>
          <el-table :data="list" border highlight-current-row @current-change="selectRole">
            <el-table-column prop="code" label="角色编码" />
            <el-table-column prop="name" label="角色名称" />
          </el-table>
        </el-card>
      </el-col>
      <el-col :span="14">
        <el-card>
          <template #header>角色权限编辑</template>
          <div style="margin-bottom: 12px">当前角色：<strong>{{ currentRole?.name || '请选择角色' }}</strong></div>
          <div style="margin-bottom: 12px; display:flex; gap: 8px; flex-wrap: wrap">
            <el-button size="small" @click="selectAll">全选</el-button>
            <el-button size="small" @click="clearAll">清空</el-button>
          </div>
          <el-checkbox-group v-model="selectedPermissionIds">
            <div style="display:flex; flex-wrap: wrap; gap: 12px">
              <el-checkbox v-for="item in permissions" :key="item.id" :label="item.id">{{ item.name }}</el-checkbox>
            </div>
          </el-checkbox-group>
          <div style="margin-top: 16px">
            <el-button type="primary" :disabled="!currentRole" :loading="saving" @click="savePermissions">保存权限</el-button>
          </div>
        </el-card>
      </el-col>
    </el-row>
  </div>
</template>
<script setup>
import { onMounted, ref } from 'vue'
import { ElMessage } from 'element-plus'
import { listPermissionsApi, listRolePermissionsApi, listRolesApi, updateRolePermissionsApi } from '../../api'

const list = ref([])
const permissions = ref([])
const currentRole = ref(null)
const selectedPermissionIds = ref([])
const saving = ref(false)

const loadData = async () => {
  const [rolesRes, permissionsRes] = await Promise.all([listRolesApi(), listPermissionsApi()])
  list.value = rolesRes.data
  permissions.value = permissionsRes.data
}

const selectRole = async (row) => {
  if (!row) {
    currentRole.value = null
    selectedPermissionIds.value = []
    return
  }
  currentRole.value = row
  const res = await listRolePermissionsApi(row.id)
  selectedPermissionIds.value = res.data.map((item) => item.id)
}

const selectAll = () => {
  selectedPermissionIds.value = permissions.value.map((item) => item.id)
}

const clearAll = () => {
  selectedPermissionIds.value = []
}

const savePermissions = async () => {
  if (!currentRole.value) return
  saving.value = true
  try {
    await updateRolePermissionsApi(currentRole.value.id, { permission_ids: selectedPermissionIds.value })
    ElMessage.success('权限已保存')
  } finally {
    saving.value = false
  }
}

onMounted(loadData)
</script>
