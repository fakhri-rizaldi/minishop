import { defineStore } from 'pinia'
import { adminApi } from '@/api/admin'
import { storage } from '@/utils/storage'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    token: storage.get('minishop_admin_token', null),
    user: storage.get('minishop_admin_user', null),
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
  },

  actions: {
    async login(credentials) {
      const response = await adminApi.login(credentials)
      const token = response.data.token
      const user = response.data.user

      this.token = token
      this.user = user

      storage.set('minishop_admin_token', token)
      storage.set('minishop_admin_user', user)

      return response
    },

    async logout() {
      try {
        if (this.token) {
          await adminApi.logout()
        }
      } catch (e) {
        // Ignore network errors on logout
      } finally {
        this.clearSession()
      }
    },

    clearSession() {
      this.token = null
      this.user = null
      storage.remove('minishop_admin_token')
      storage.remove('minishop_admin_user')
    },
  },
})
