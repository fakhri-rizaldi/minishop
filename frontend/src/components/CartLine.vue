<template>
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 bg-[var(--color-surface)] border border-[var(--color-surface-raised)] rounded-[var(--radius-card)] transition-colors">
    <!-- Left: Thumbnail & Info -->
    <div class="flex items-center gap-4 flex-1 min-w-0">
      <!-- Thumbnail -->
      <router-link
        :to="`/products/${item.id}`"
        class="w-16 h-16 sm:w-20 sm:h-20 shrink-0 aspect-square bg-[var(--color-surface-raised)] rounded-[var(--radius-media)] overflow-hidden block"
        :aria-label="`Lihat detail ${item.name}`"
      >
        <img
          v-if="item.image_url && !imageError"
          :src="item.image_url"
          :alt="item.name"
          class="w-full h-full object-cover"
          @error="imageError = true"
        />
        <div v-else class="w-full h-full flex items-center justify-center text-[var(--color-text-secondary)]">
          <svg class="w-6 h-6 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
        </div>
      </router-link>

      <!-- Details -->
      <div class="flex-1 min-w-0">
        <router-link
          :to="`/products/${item.id}`"
          class="text-base font-semibold text-[var(--color-text)] hover:text-[var(--color-primary)] transition-colors line-clamp-1 block mb-1"
        >
          {{ item.name }}
        </router-link>
        <div class="text-sm text-[var(--color-text-secondary)] tabular-nums">
          {{ formatRupiah(item.price) }} / item
        </div>
        <div v-if="item.stock <= 5" class="mt-1 text-xs text-[var(--color-warning)]">
          Sisa stok: {{ item.stock }}
        </div>
      </div>
    </div>

    <!-- Right: Stepper, Subtotal, Remove Button -->
    <div class="flex items-center justify-between sm:justify-end gap-4 sm:gap-6 border-t sm:border-t-0 border-[var(--color-surface-raised)] pt-3 sm:pt-0">
      <!-- Stepper -->
      <QuantityStepper
        :model-value="item.quantity"
        :min="1"
        :max="item.stock"
        @update:model-value="handleQuantityChange"
      />

      <!-- Subtotal -->
      <div class="text-right min-w-[100px]">
        <span class="text-xs text-[var(--color-text-secondary)] sm:hidden block">Subtotal</span>
        <span class="text-base font-bold font-sans text-[var(--color-primary)] tabular-nums">
          {{ formatRupiah(item.price * item.quantity) }}
        </span>
      </div>

      <!-- Delete Button -->
      <button
        type="button"
        class="p-2 text-[var(--color-text-secondary)] hover:text-[var(--color-danger)] rounded transition-colors cursor-pointer"
        :aria-label="`Hapus ${item.name} dari keranjang`"
        @click="$emit('remove', item.id)"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
        </svg>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { formatRupiah } from '@/utils/format'
import QuantityStepper from '@/components/QuantityStepper.vue'
import { useToastStore } from '@/stores/toast'

const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
})

const emit = defineEmits(['update-quantity', 'remove'])

const imageError = ref(false)
const toastStore = useToastStore()

function handleQuantityChange(newQty) {
  emit('update-quantity', { id: props.item.id, quantity: newQty })
}
</script>
