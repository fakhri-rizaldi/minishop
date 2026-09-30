<template>
  <div class="p-8 text-center bg-[var(--color-surface)] border border-[var(--color-danger)]/30 rounded-[var(--radius-card)] my-6">
    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-[var(--color-danger)]/10 text-[var(--color-danger)] mb-4">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
      </svg>
    </div>
    <h3 class="text-lg font-bold font-display text-[var(--color-text)] mb-1">
      {{ title }}
    </h3>
    <p class="text-sm text-[var(--color-text-secondary)] max-w-md mx-auto mb-6">
      {{ message }}
    </p>
    <button
      type="button"
      @click="$emit('retry')"
      class="inline-flex items-center px-4 py-2 border border-[var(--color-border-strong)] bg-[var(--color-surface-raised)] text-[var(--color-text)] font-semibold rounded-[var(--radius-control)] hover:border-[var(--color-primary)] hover:text-[var(--color-primary)] transition-colors text-sm cursor-pointer"
    >
      {{ retryText }}
    </button>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    default: 'Terjadi Kesalahan',
  },
  message: {
    type: String,
    default: 'Gagal memuat data. Periksa koneksi internet Anda dan coba lagi.',
  },
  retryText: {
    type: String,
    default: 'Coba lagi',
  },
})

defineEmits(['retry'])
</script>
