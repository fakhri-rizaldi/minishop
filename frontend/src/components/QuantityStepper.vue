<template>
  <div
    class="inline-flex items-center h-11 bg-[var(--color-bg)] border border-[var(--color-border-strong)] rounded-[var(--radius-control)] overflow-hidden"
    :class="{ 'opacity-50 cursor-not-allowed': disabled }"
  >
    <!-- Decrease Button -->
    <button
      type="button"
      :disabled="disabled || modelValue <= min"
      class="w-10 h-full flex items-center justify-center text-[var(--color-text)] hover:text-[var(--color-primary)] hover:bg-[var(--color-surface-raised)] disabled:opacity-30 disabled:hover:bg-transparent disabled:hover:text-[var(--color-text)] disabled:cursor-not-allowed transition-colors text-lg font-bold"
      aria-label="Kurangi jumlah"
      @click="updateValue(modelValue - 1)"
    >
      −
    </button>

    <!-- Quantity Input -->
    <input
      type="text"
      inputmode="numeric"
      pattern="[0-9]*"
      :value="modelValue"
      :disabled="disabled"
      class="w-12 h-full text-center bg-transparent text-sm font-semibold text-[var(--color-text)] focus:outline-none tabular-nums"
      aria-label="Jumlah item"
      @change="handleInputChange($event.target.value)"
      @keydown.up.prevent="!disabled && modelValue < max && updateValue(modelValue + 1)"
      @keydown.down.prevent="!disabled && modelValue > min && updateValue(modelValue - 1)"
    />

    <!-- Increase Button -->
    <button
      type="button"
      :disabled="disabled || modelValue >= max"
      class="w-10 h-full flex items-center justify-center text-[var(--color-text)] hover:text-[var(--color-primary)] hover:bg-[var(--color-surface-raised)] disabled:opacity-30 disabled:hover:bg-transparent disabled:hover:text-[var(--color-text)] disabled:cursor-not-allowed transition-colors text-lg font-bold"
      aria-label="Tambah jumlah"
      @click="updateValue(modelValue + 1)"
    >
      +
    </button>
  </div>
</template>

<script setup>
const props = defineProps({
  modelValue: {
    type: Number,
    default: 1,
  },
  min: {
    type: Number,
    default: 1,
  },
  max: {
    type: Number,
    default: 9999,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['update:modelValue', 'change'])

function updateValue(val) {
  const clamped = Math.max(props.min, Math.min(props.max, Math.floor(val) || props.min))
  emit('update:modelValue', clamped)
  emit('change', clamped)
}

function handleInputChange(rawVal) {
  const parsed = parseInt(rawVal, 10)
  if (isNaN(parsed) || parsed < props.min) {
    updateValue(props.min)
  } else if (parsed > props.max) {
    updateValue(props.max)
  } else {
    updateValue(parsed)
  }
}
</script>
