<template>
  <div>
    <h2 style="margin-bottom: 24px">控制台</h2>
    <el-row :gutter="20">
      <el-col :span="6">
        <el-card shadow="hover">
          <div style="text-align: center">
            <div style="font-size: 14px; color: #909399">班级数量</div>
            <div style="font-size: 32px; font-weight: 700; margin-top: 8px">{{ stats.classes }}</div>
          </div>
        </el-card>
      </el-col>
      <el-col :span="6">
        <el-card shadow="hover">
          <div style="text-align: center">
            <div style="font-size: 14px; color: #909399">学生数量</div>
            <div style="font-size: 32px; font-weight: 700; margin-top: 8px">{{ stats.students }}</div>
          </div>
        </el-card>
      </el-col>
      <el-col :span="6">
        <el-card shadow="hover">
          <div style="text-align: center">
            <div style="font-size: 14px; color: #909399">教师数量</div>
            <div style="font-size: 32px; font-weight: 700; margin-top: 8px">{{ stats.teachers }}</div>
          </div>
        </el-card>
      </el-col>
      <el-col :span="6">
        <el-card shadow="hover">
          <div style="text-align: center">
            <div style="font-size: 14px; color: #909399">用户数量</div>
            <div style="font-size: 32px; font-weight: 700; margin-top: 8px">{{ stats.users }}</div>
          </div>
        </el-card>
      </el-col>
    </el-row>
  </div>
</template>

<script setup>
import { reactive, onMounted } from 'vue'
import { dashboardStatsApi } from '../../api'

const stats = reactive({ classes: 0, students: 0, teachers: 0, users: 0 })

onMounted(async () => {
  try {
    const res = await dashboardStatsApi()
    Object.assign(stats, res.data)
  } catch {
    // ignore
  }
})
</script>
