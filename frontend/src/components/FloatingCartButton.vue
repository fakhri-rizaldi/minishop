<template>
  <Teleport to="body">
    <Transition name="floating-cart">
      <router-link
        v-if="shouldShow"
        to="/cart"
        :class="[
          'fixed bottom-6 right-6 z-40 flex items-center justify-center w-14 h-14 rounded-full bg-[var(--color-surface)] border-2 border-[var(--color-primary)] hover:border-[var(--color-accent-hover)] hover:scale-105 active:scale-95 shadow-[0_8px_30px_rgb(0,0,0,0.6)] transition-all duration-200 group cursor-pointer',
          { 'animate-cart-bounce': feedbackStore.bouncing },
        ]"
        :aria-label="`Buka keranjang belanja, ${cartStore.totalItems} item`"
      >
        <!-- Cart Icon -->
        <svg
          class="w-6 h-6 text-[var(--color-primary)] group-hover:text-[var(--color-accent-hover)] transition-colors"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
          aria-hidden="true"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
          />
        </svg>

        <!-- Red Badge with Total Items Count (REQ-CART-01, ui.md §6.10) -->
        <span
          class="absolute -top-1.5 -right-1.5 min-w-[22px] h-[22px] px-1 rounded-full bg-[var(--color-danger)] text-white text-xs font-bold font-sans flex items-center justify-center border-2 border-[var(--color-surface)] shadow-md tabular-nums"
        >
          {{ cartStore.totalItems }}
        </span>
      </router-link>
    </Transition>
  </Teleport>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { useCartStore } from '@/stores/cart'
import { useCartFeedbackStore } from '@/stores/feedback'

const route = useRoute()
const cartStore = useCartStore()
const feedbackStore = useCartFeedbackStore()

const shouldShow = computed(() => {
  // Hide on cart, checkout, and admin pages
  const path = route.path
  if (path === '/cart' || path === '/checkout' || path.startsWith('/admin')) {
    return false
  }
  return cartStore.totalItems > 0
})
</script>
