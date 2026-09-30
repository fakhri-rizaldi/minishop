import { defineStore } from 'pinia'

export const useToastStore = defineStore('toast', {
  state: () => ({
    toasts: [],
  }),

  actions: {
    /**
     * Show a toast message
     * @param {string} message
     * @param {'info' | 'success' | 'warning' | 'danger'} type
     * @param {number} duration ms
     */
    show(message, type = 'success', duration = 3000) {
      const id = Date.now() + Math.random()
      this.toasts.push({ id, message, type })

      if (duration > 0) {
        setTimeout(() => {
          this.remove(id)
        }, duration)
      }
    },

    remove(id) {
      this.toasts = this.toasts.filter((t) => t.id !== id)
    },
  },
})
