<template>
  <div
    aria-live="polite"
    class="fixed bottom-4 right-4 z-50 flex flex-col gap-2 max-w-sm w-full pointer-events-none"
  >
    <transition-group
      enter-active-class="transition duration-150 ease-out"
      enter-from-class="opacity-0 translate-y-2"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-for="toast in toastStore.toasts"
        :key="toast.id"
        class="pointer-events-auto flex items-center justify-between p-3.5 bg-[var(--color-surface)] border border-[var(--color-surface-raised)] rounded-[var(--radius-control)] shadow-lg text-sm text-[var(--color-text)]"
      >
        <div class="flex items-center gap-2.5">
          <!-- Icon indicator -->
          <span
            v-if="toast.type === 'success'"
            class="w-2 h-2 rounded-full bg-[var(--color-primary)] shrink-0"
          ></span>
          <span
            v-else-if="toast.type === 'danger'"
            class="w-2 h-2 rounded-full bg-[var(--color-danger)] shrink-0"
          ></span>
          <span
            v-else-if="toast.type === 'warning'"
            class="w-2 h-2 rounded-full bg-[var(--color-warning)] shrink-0"
          ></span>
          <span
            v-else
            class="w-2 h-2 rounded-full bg-[var(--color-accent)] shrink-0"
          ></span>

          <span>{{ toast.message }}</span>
        </div>

        <button
          type="button"
          @click="toastStore.remove(toast.id)"
          class="ml-3 text-[var(--color-text-secondary)] hover:text-[var(--color-text)] cursor-pointer"
          aria-label="Tutup notifikasi"
        >
          &times;
        </button>
      </div>
    </transition-group>
  </div>
</template>

<script setup>
import { useToastStore } from '@/stores/toast'

const toastStore = useToastStore()
</script>
