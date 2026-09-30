<template>
  <div class="relative w-full">
    <label :for="id" class="sr-only">
      {{ label }}
    </label>
    <div class="relative flex items-center">
      <span class="absolute left-3.5 text-[var(--color-text-secondary)] pointer-events-none flex items-center justify-center">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
      </span>
      <input
        :id="id"
        type="search"
        :value="modelValue"
        :placeholder="placeholder"
        autocomplete="off"
        class="w-full h-11 pl-10 pr-9 bg-[var(--color-bg)] border border-[var(--color-border-strong)] rounded-[var(--radius-control)] text-sm text-[var(--color-text)] placeholder:text-[var(--color-text-secondary)] focus:outline-none focus:border-[var(--color-primary)] transition-colors"
        @input="$emit('update:modelValue', $event.target.value)"
        @keydown.esc="$emit('update:modelValue', '')"
      />
      <button
        v-if="modelValue"
        type="button"
        class="absolute right-2.5 p-1 text-[var(--color-text-secondary)] hover:text-[var(--color-text)] rounded transition-colors"
        aria-label="Hapus kata kunci pencarian"
        @click="$emit('update:modelValue', '')"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  modelValue: {
    type: String,
    default: '',
  },
  placeholder: {
    type: String,
    default: 'Cari produk...',
  },
  label: {
    type: String,
    default: 'Cari produk berdasarkan nama',
  },
  id: {
    type: String,
    default: 'search-input',
  },
})

defineEmits(['update:modelValue'])
</script>
