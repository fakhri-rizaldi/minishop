<template>
  <nav class="w-full overflow-x-auto no-scrollbar py-1" aria-label="Filter kategori produk">
    <div class="flex items-center gap-2 min-w-max">
      <!-- Chip 'Semua' -->
      <button
        type="button"
        :class="[
          'px-4 py-2 text-sm font-medium rounded-[var(--radius-pill)] transition-colors cursor-pointer border',
          !modelValue
            ? 'bg-[var(--color-surface-raised)] text-[var(--color-primary)] border-[var(--color-primary)]'
            : 'bg-transparent text-[var(--color-text)] border-[var(--color-border-strong)] hover:border-[var(--color-primary)] hover:text-[var(--color-primary)]',
        ]"
        :aria-pressed="!modelValue"
        @click="$emit('update:modelValue', '')"
      >
        Semua
      </button>

      <!-- Category Chips -->
      <button
        v-for="cat in categoriesList"
        :key="cat.id"
        type="button"
        :class="[
          'px-4 py-2 text-sm font-medium rounded-[var(--radius-pill)] transition-colors cursor-pointer border',
          modelValue === cat.slug
            ? 'bg-[var(--color-surface-raised)] text-[var(--color-primary)] border-[var(--color-primary)]'
            : 'bg-transparent text-[var(--color-text)] border-[var(--color-border-strong)] hover:border-[var(--color-primary)] hover:text-[var(--color-primary)]',
        ]"
        :aria-pressed="modelValue === cat.slug"
        @click="$emit('update:modelValue', cat.slug)"
      >
        {{ cat.name }}
      </button>

      <!-- Skeleton loading chips if categories are loading -->
      <template v-if="loading && categoriesList.length === 0">
        <div
          v-for="i in 4"
          :key="i"
          class="h-9 w-20 rounded-[var(--radius-pill)] bg-[var(--color-surface-raised)] animate-pulse"
        />
      </template>
    </div>
  </nav>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { catalogApi } from '@/api/catalog'

const props = defineProps({
  modelValue: {
    type: String,
    default: '',
  },
  categories: {
    type: Array,
    default: null,
  },
})

defineEmits(['update:modelValue'])

const internalCategories = ref([])
const loading = ref(false)

const categoriesList = computed(() => {
  return props.categories || internalCategories.value
})

onMounted(async () => {
  if (!props.categories) {
    try {
      loading.value = true
      const res = await catalogApi.getCategories()
      internalCategories.value = res.data || []
    } catch (err) {
      console.error('Gagal memuat kategori:', err)
    } finally {
      loading.value = false
    }
  }
})
</script>
