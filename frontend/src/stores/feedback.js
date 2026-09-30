import { defineStore } from 'pinia'

export const useCartFeedbackStore = defineStore('cartFeedback', {
  state: () => ({
    bouncing: false,
    timer: null,
  }),

  actions: {
    /**
     * Trigger bounce animation on the floating cart button
     */
    trigger() {
      this.bouncing = true

      if (this.timer) {
        clearTimeout(this.timer)
      }

      this.timer = setTimeout(() => {
        this.bouncing = false
      }, 400)
    },
  },
})
