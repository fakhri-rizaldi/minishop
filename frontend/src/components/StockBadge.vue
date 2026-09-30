<template>
  <span
    :class="[
      'text-xs font-medium transition-colors tabular-nums',
      textClass
    ]"
  >
    {{ label }}
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  stock: {
    type: Number,
    required: true,
  },
})

const label = computed(() => {
  if (props.stock <= 0) return 'Habis'
  if (props.stock <= 5) return `Sisa ${props.stock}`
  return `Stok: ${props.stock}`
})

const textClass = computed(() => {
  if (props.stock <= 0) {
    return 'text-[var(--color-danger)] font-semibold'
  }
  if (props.stock <= 5) {
    return 'text-[var(--color-warning)] font-semibold'
  }
  return 'text-[var(--color-text-secondary)]'
})
</script>
