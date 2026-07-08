import { defineStore } from 'pinia'
import { meApi, logoutApi } from '../api'

export const useUserStore = defineStore('user', {
  state: () => ({
    user: null,
    ready: false
  }),
  getters: {
    isLoggedIn: (state) => !!state.user,
    permissions: (state) => (state.user && state.user.permissions) ? state.user.permissions : []
  },
  actions: {
    async fetchMe() {
      try {
        const res = await meApi()
        this.user = res.data
      } catch {
        this.user = null
        localStorage.removeItem('token')
      } finally {
        this.ready = true
      }
    },
    async logout() {
      await logoutApi()
      this.user = null
      localStorage.removeItem('token')
    }
  }
})