<template>
  <Teleport to="body">
    <div
      v-if="activeOpen"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-[var(--color-overlay)]"
      role="dialog"
      aria-modal="true"
      :aria-labelledby="titleId"
      @keydown.esc="handleCancel"
    >
      <div
        ref="dialogRef"
        class="w-full max-w-md bg-[var(--color-surface)] border border-[var(--color-surface-raised)] rounded-[var(--radius-card)] p-6 shadow-xl"
      >
        <h3 :id="titleId" class="text-lg font-bold font-display text-[var(--color-text)] mb-2">
          {{ title }}
        </h3>
        <p class="text-sm text-[var(--color-text-secondary)] mb-6 leading-relaxed">
          {{ message }}
        </p>
        <div class="flex items-center justify-end gap-3">
          <button
            ref="cancelBtnRef"
            type="button"
            @click="handleCancel"
            class="px-4 py-2 border border-[var(--color-border-strong)] bg-[var(--color-surface-raised)] text-[var(--color-text)] font-semibold rounded-[var(--radius-control)] hover:border-[var(--color-text-secondary)] transition-colors text-sm cursor-pointer"
          >
            {{ cancelText }}
          </button>
          <button
            type="button"
            @click="handleConfirm"
            :class="[
              'px-4 py-2 font-semibold rounded-[var(--radius-control)] transition-colors text-sm cursor-pointer',
              activeDanger
                ? 'bg-[var(--color-danger)] text-[var(--color-bg)] hover:opacity-90'
                : 'bg-[var(--color-primary)] text-[var(--color-bg)] hover:bg-[var(--color-accent-hover)]'
            ]"
          >
            {{ confirmText }}
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch, nextTick } from 'vue'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  show: {
    type: Boolean,
    default: false,
  },
  title: {
    type: String,
    default: 'Konfirmasi Aksi',
  },
  message: {
    type: String,
    default: 'Apakah Anda yakin ingin melanjutkan?',
  },
  confirmText: {
    type: String,
    default: 'Konfirmasi',
  },
  cancelText: {
    type: String,
    default: 'Batal',
  },
  isDanger: {
    type: Boolean,
    default: false,
  },
  danger: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['confirm', 'cancel', 'update:isOpen', 'update:show'])

const activeOpen = computed(() => props.isOpen || props.show)
const activeDanger = computed(() => props.isDanger || props.danger)

const titleId = `dialog-title-${Math.random().toString(36).slice(2, 9)}`
const cancelBtnRef = ref(null)

watch(
  activeOpen,
  (val) => {
    if (val) {
      nextTick(() => {
        cancelBtnRef.value?.focus()
      })
    }
  }
)

function handleCancel() {
  emit('cancel')
  emit('update:isOpen', false)
  emit('update:show', false)
}

function handleConfirm() {
  emit('confirm')
  emit('update:isOpen', false)
  emit('update:show', false)
}
</script>
