<template>
  <nav
    v-if="lastPage > 1"
    class="flex items-center justify-center gap-1.5 py-6"
    aria-label="Navigasi halaman produk"
  >
    <!-- Previous Button -->
    <button
      type="button"
      :disabled="currentPage <= 1"
      class="inline-flex items-center justify-center min-w-[36px] h-9 px-2.5 rounded-[var(--radius-control)] border border-[var(--color-border-strong)] bg-transparent text-[var(--color-text)] hover:border-[var(--color-primary)] hover:text-[var(--color-primary)] disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:border-[var(--color-border-strong)] disabled:hover:text-[var(--color-text)] transition-colors text-sm font-medium"
      aria-label="Halaman sebelumnya"
      @click="$emit('change-page', currentPage - 1)"
    >
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
      </svg>
    </button>

    <!-- Page Numbers -->
    <button
      v-for="page in pages"
      :key="page"
      type="button"
      :disabled="page === '...'"
      :class="[
        'inline-flex items-center justify-center min-w-[36px] h-9 px-3 rounded-[var(--radius-control)] text-sm font-medium transition-colors border',
        page === currentPage
          ? 'bg-[var(--color-surface-raised)] text-[var(--color-primary)] border-[var(--color-primary)] font-bold'
          : page === '...'
            ? 'bg-transparent text-[var(--color-text-muted)] border-transparent cursor-default'
            : 'bg-transparent text-[var(--color-text)] border-[var(--color-border-strong)] hover:border-[var(--color-primary)] hover:text-[var(--color-primary)]',
      ]"
      :aria-current="page === currentPage ? 'page' : undefined"
      :aria-label="page !== '...' ? `Halaman ${page}` : undefined"
      @click="page !== '...' && $emit('change-page', page)"
    >
      {{ page }}
    </button>

    <!-- Next Button -->
    <button
      type="button"
      :disabled="currentPage >= lastPage"
      class="inline-flex items-center justify-center min-w-[36px] h-9 px-2.5 rounded-[var(--radius-control)] border border-[var(--color-border-strong)] bg-transparent text-[var(--color-text)] hover:border-[var(--color-primary)] hover:text-[var(--color-primary)] disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:border-[var(--color-border-strong)] disabled:hover:text-[var(--color-text)] transition-colors text-sm font-medium"
      aria-label="Halaman berikutnya"
      @click="$emit('change-page', currentPage + 1)"
    >
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
      </svg>
    </button>
  </nav>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  currentPage: {
    type: Number,
    required: true,
  },
  lastPage: {
    type: Number,
    required: true,
  },
  total: {
    type: Number,
    default: 0,
  },
})

defineEmits(['change-page'])

const pages = computed(() => {
  const current = props.currentPage
  const last = props.lastPage
  const delta = 1
  const range = []
  const rangeWithDots = []
  let l

  for (let i = 1; i <= last; i++) {
    if (i === 1 || i === last || (i >= current - delta && i <= current + delta)) {
      range.push(i)
    }
  }

  for (const i of range) {
    if (l) {
      if (i - l === 2) {
        rangeWithDots.push(l + 1)
      } else if (i - l !== 1) {
        rangeWithDots.push('...')
      }
    }
    rangeWithDots.push(i)
    l = i
  }

  return rangeWithDots
})
</script>
