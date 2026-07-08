import http from '../utils/http'

export const loginApi = (data) => http.post('/auth/login', data)
export const logoutApi = () => http.post('/auth/logout')
export const meApi = () => http.get('/auth/me')
export const dashboardStatsApi = () => http.get('/dashboard/stats')

export const listClassesApi = (params) => http.get('/classes', { params })
export const saveClassApi = (data) => http.post('/classes', data)
export const updateClassApi = (id, data) => http.put(`/classes/${id}`, data)
export const deleteClassApi = (id) => http.delete(`/classes/${id}`)
export const getClassDetailApi = (id) => http.get(`/classes/${id}`)

export const listStudentsApi = (params) => http.get('/students', { params })
export const saveStudentApi = (data) => http.post('/students', data)
export const updateStudentApi = (id, data) => http.put(`/students/${id}`, data)
export const deleteStudentApi = (id) => http.delete(`/students/${id}`)
export const transferStudentApi = (id, data) => http.post(`/students/${id}/transfer`, data)
export const getStudentDetailApi = (id) => http.get(`/students/${id}`)

export const listTeachersApi = (params) => http.get('/teachers', { params })
export const saveTeacherApi = (data) => http.post('/teachers', data)
export const updateTeacherApi = (id, data) => http.put(`/teachers/${id}`, data)
export const deleteTeacherApi = (id) => http.delete(`/teachers/${id}`)

export const listUsersApi = (params) => http.get('/users', { params })
export const saveUserApi = (data) => http.post('/users', data)
export const updateUserApi = (id, data) => http.put(`/users/${id}`, data)
export const deleteUserApi = (id) => http.delete(`/users/${id}`)
export const listRolesApi = () => http.get('/roles')
export const listPermissionsApi = () => http.get('/permissions')
export const listRolePermissionsApi = (id) => http.get(`/roles/${id}/permissions`)
export const updateRolePermissionsApi = (id, data) => http.put(`/roles/${id}/permissions`, data)
