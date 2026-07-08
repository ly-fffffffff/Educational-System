<template>
  <el-container class="layout-shell">
    <el-aside width="220px" class="sidebar">
      <div class="brand">班级管理系统</div>
      <el-menu router :default-active="$route.path" class="menu" background-color="#1f2d3d" text-color="#cbd5e1" active-text-color="#409eff">
        <el-menu-item index="/dashboard">控制台</el-menu-item>
        <el-menu-item index="/classes">班级管理</el-menu-item>
        <el-menu-item index="/students">学生管理</el-menu-item>
        <el-menu-item index="/teachers">教师管理</el-menu-item>
        <el-menu-item v-if="can('user.manage')" index="/users">账号管理</el-menu-item>
        <el-menu-item v-if="can('role.manage')" index="/roles">权限管理</el-menu-item>
      </el-menu>
    </el-aside>
    <el-container>
      <el-header class="topbar">
        <div>
          <div style="font-weight: 700">{{ userStore.user?.name || '未登录' }}</div>
          <div style="font-size: 12px; color: #6b7280">{{ userStore.user?.role_name || '' }}</div>
        </div>
        <el-button size="small" @click="logout">退出登录</el-button>
      </el-header>
      <el-main>
        <router-view />
      </el-main>
    </el-container>
  </el-container>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useUserStore } from '../stores/user'

const router = useRouter()
const userStore = useUserStore()
const can = (code) => (userStore.user && userStore.user.role_code === 'admin') || userStore.permissions.includes(code)
const logout = async () => {
  await userStore.logout()
  router.push('/login')
}
</script>
