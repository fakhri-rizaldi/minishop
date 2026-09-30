<template>
  <div class="p-8 text-center bg-[var(--color-surface)] border border-[var(--color-surface-raised)] rounded-[var(--radius-card)] my-6">
    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-[var(--color-surface-raised)] text-[var(--color-accent)] mb-4">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
      </svg>
    </div>
    <h3 class="text-lg font-bold font-display text-[var(--color-text)] mb-1">
      {{ title }}
    </h3>
    <p v-if="message" class="text-sm text-[var(--color-text-secondary)] max-w-md mx-auto mb-6">
      {{ message }}
    </p>
    <div v-if="actionText">
      <router-link
        v-if="actionLink"
        :to="actionLink"
        class="inline-flex items-center px-4 py-2 bg-[var(--color-primary)] text-[var(--color-bg)] font-semibold rounded-[var(--radius-control)] hover:bg-[var(--color-accent-hover)] active:bg-[var(--color-accent)] transition-colors text-sm"
      >
        {{ actionText }}
      </router-link>
      <button
        v-else
        type="button"
        @click="$emit('action')"
        class="inline-flex items-center px-4 py-2 bg-[var(--color-primary)] text-[var(--color-bg)] font-semibold rounded-[var(--radius-control)] hover:bg-[var(--color-accent-hover)] active:bg-[var(--color-accent)] transition-colors text-sm cursor-pointer"
      >
        {{ actionText }}
      </button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    default: 'Tidak ada data',
  },
  message: {
    type: String,
    default: '',
  },
  actionText: {
    type: String,
    default: '',
  },
  actionLink: {
    type: [String, Object],
    default: null,
  },
})

defineEmits(['action'])
</script>
