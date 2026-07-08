import { createRouter, createWebHistory } from 'vue-router'
import { useUserStore } from '../stores/user'
import LoginView from '../views/auth/LoginView.vue'
import LayoutView from '../layouts/AppLayout.vue'
import DashboardView from '../views/dashboard/DashboardView.vue'
import ClassView from '../views/classes/ClassView.vue'
import StudentView from '../views/students/StudentView.vue'
import TeacherView from '../views/teachers/TeacherView.vue'
import UserView from '../views/users/UserView.vue'
import RoleView from '../views/roles/RoleView.vue'

const routes = [
  { path: '/login', component: LoginView },
  {
    path: '/',
    component: LayoutView,
    children: [
      { path: '', redirect: '/dashboard' },
      { path: 'dashboard', component: DashboardView },
      { path: 'classes', component: ClassView },
      { path: 'students', component: StudentView },
      { path: 'teachers', component: TeacherView },
      { path: 'users', component: UserView },
      { path: 'roles', component: RoleView }
    ]
  }
]

const router = createRouter({ history: createWebHistory(), routes })

router.beforeEach(async (to) => {
  const userStore = useUserStore()
  if (!userStore.ready) {
    await userStore.fetchMe()
  }
  if (to.path !== '/login' && !userStore.isLoggedIn) {
    return '/login'
  }
  if (to.path === '/login' && userStore.isLoggedIn) {
    return '/dashboard'
  }
})

export default router