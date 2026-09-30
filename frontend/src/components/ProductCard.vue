<template>
  <div
    class="group relative flex flex-col bg-[var(--color-surface)] border border-[var(--color-surface-raised)] hover:border-[var(--color-border-strong)] rounded-[var(--radius-card)] p-4 transition-all duration-200"
  >
    <!-- Clickable Area to Product Detail -->
    <router-link
      :to="`/products/${product.id}`"
      class="flex flex-col flex-1 focus:outline-none"
      :aria-label="`Lihat detail ${product.name}`"
    >
      <!-- Product Image with 1:1 Aspect Ratio & Fallback (REQ-CAT-12) -->
      <div class="relative w-full aspect-square bg-[var(--color-surface-raised)] rounded-[var(--radius-media)] overflow-hidden mb-3">
        <img
          v-if="product.image_url && !imageError"
          :src="product.image_url"
          :alt="product.name"
          loading="lazy"
          class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
          @error="imageError = true"
        />
        <!-- Image Fallback Placeholder -->
        <div
          v-else
          class="w-full h-full flex flex-col items-center justify-center p-4 text-[var(--color-text-secondary)] bg-[var(--color-surface-raised)]"
        >
          <svg class="w-10 h-10 mb-1 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          <span class="text-xs font-medium opacity-70">MiniShop</span>
        </div>
      </div>

      <!-- Category Name -->
      <span
        v-if="product.category"
        class="text-xs text-[var(--color-text-secondary)] font-medium mb-1 truncate"
      >
        {{ product.category.name }}
      </span>

      <!-- Product Name -->
      <h3 class="text-base font-semibold text-[var(--color-text)] group-hover:text-[var(--color-primary)] transition-colors line-clamp-2 mb-2 leading-snug">
        {{ product.name }}
      </h3>

      <!-- Price & Stock Badge Row -->
      <div class="flex items-center justify-between gap-2 mt-auto pt-2 mb-4">
        <PriceText :price="product.price" class="text-base font-bold text-[var(--color-primary)]" />
        <StockBadge :stock="product.stock" />
      </div>
    </router-link>

    <!-- Action Button -->
    <button
      type="button"
      :disabled="product.stock <= 0"
      :class="[
        'w-full h-10 px-4 text-sm font-semibold rounded-[var(--radius-control)] transition-colors flex items-center justify-center gap-2 cursor-pointer',
        product.stock > 0
          ? 'bg-[var(--color-primary)] text-[var(--color-bg)] hover:bg-[var(--color-accent-hover)] active:bg-[var(--color-accent)]'
          : 'bg-transparent text-[var(--color-text-muted)] border border-[var(--color-surface-raised)] cursor-not-allowed opacity-50',
      ]"
      :aria-label="product.stock > 0 ? `Tambah ${product.name} ke keranjang` : `${product.name} stok habis`"
      @click.stop="handleAddToCart"
    >
      <svg
        v-if="product.stock > 0"
        class="w-4 h-4"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
        aria-hidden="true"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
      </svg>
      <span>{{ product.stock > 0 ? 'Tambah ke keranjang' : 'Stok habis' }}</span>
    </button>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import PriceText from '@/components/PriceText.vue'
import StockBadge from '@/components/StockBadge.vue'
import { useCartStore } from '@/stores/cart'
import { useToastStore } from '@/stores/toast'
import { useCartFeedbackStore } from '@/stores/feedback'

const props = defineProps({
  product: {
    type: Object,
    required: true,
  },
})

const emit = defineEmits(['add-to-cart'])

const imageError = ref(false)
const cartStore = useCartStore()
const toastStore = useToastStore()
const feedbackStore = useCartFeedbackStore()

function handleAddToCart() {
  if (props.product.stock <= 0) return

  const result = cartStore.addItem(props.product, 1)
  if (result.success) {
    feedbackStore.trigger(1)
    emit('add-to-cart', props.product)
  } else {
    toastStore.show(result.message || 'Jumlah melebihi stok yang tersedia.', 'error')
  }
}
</script>
