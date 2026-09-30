<template>
  <main class="min-h-screen py-8 px-4 max-w-[1200px] mx-auto">
    <!-- 1. Loading State -->
    <div v-if="loading" class="animate-pulse">
      <!-- Breadcrumb Skeleton -->
      <div class="h-4 w-48 bg-[var(--color-surface-raised)] rounded mb-8"></div>

      <div class="grid grid-cols-1 md:grid-cols-12 gap-8 lg:gap-12">
        <!-- Image Skeleton -->
        <div class="md:col-span-5 aspect-square bg-[var(--color-surface-raised)] rounded-[var(--radius-card)]"></div>

        <!-- Info Skeleton -->
        <div class="md:col-span-7 flex flex-col gap-4">
          <div class="h-4 w-24 bg-[var(--color-surface-raised)] rounded"></div>
          <div class="h-8 w-3/4 bg-[var(--color-surface-raised)] rounded"></div>
          <div class="h-8 w-40 bg-[var(--color-surface-raised)] rounded my-2"></div>
          <div class="h-20 w-full bg-[var(--color-surface-raised)] rounded"></div>
          <div class="h-11 w-64 bg-[var(--color-surface-raised)] rounded mt-4"></div>
        </div>
      </div>
    </div>

    <!-- 2. 404 Not Found State (REQ-CAT-10) -->
    <div v-else-if="notFound" class="py-12">
      <EmptyState
        title="Produk Tidak Ditemukan"
        message="Produk yang Anda cari tidak tersedia, salah tautan, atau sudah tidak dijual lagi."
        action-text="Kembali ke Katalog"
        action-link="/#katalog"
      />
    </div>

    <!-- 3. Error State -->
    <div v-else-if="errorMessage" class="py-12">
      <ErrorState
        title="Gagal Memuat Detail Produk"
        :message="errorMessage"
        retry-text="Coba lagi"
        @retry="loadProduct"
      />
    </div>

    <!-- 4. Product Detail Content -->
    <div v-else-if="product">
      <!-- Breadcrumb Navigation (ui.md §5.4) -->
      <nav class="flex items-center gap-2 text-sm text-[var(--color-text-secondary)] mb-6" aria-label="Breadcrumb">
        <router-link to="/#katalog" class="hover:text-[var(--color-primary)] transition-colors">
          Katalog
        </router-link>
        <span class="opacity-50">›</span>
        <span class="text-[var(--color-text)] font-medium truncate max-w-[200px] md:max-w-md">
          {{ product.name }}
        </span>
      </nav>

      <!-- Main Detail Layout (2 Columns on Desktop) -->
      <div class="grid grid-cols-1 md:grid-cols-12 gap-8 lg:gap-12">
        <!-- Left Column: Large Image with 1:1 Aspect Ratio (REQ-CAT-12) -->
        <div class="md:col-span-5">
          <div class="relative w-full aspect-square bg-[var(--color-surface)] border border-[var(--color-surface-raised)] rounded-[var(--radius-card)] overflow-hidden">
            <img
              v-if="product.image_url && !imageError"
              :src="product.image_url"
              :alt="product.name"
              class="w-full h-full object-cover"
              @error="imageError = true"
            />
            <!-- Fallback Placeholder -->
            <div
              v-else
              class="w-full h-full flex flex-col items-center justify-center p-8 text-[var(--color-text-secondary)] bg-[var(--color-surface)]"
            >
              <svg class="w-16 h-16 mb-2 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <span class="text-sm font-medium opacity-70">MiniShop</span>
            </div>
          </div>
        </div>

        <!-- Right Column: Product Information & Purchase Actions -->
        <div class="md:col-span-7 flex flex-col justify-start">
          <!-- Category -->
          <span
            v-if="product.category"
            class="text-sm text-[var(--color-text-secondary)] font-medium mb-1"
          >
            {{ product.category.name }}
          </span>

          <!-- Product Name -->
          <h1 class="text-2xl md:text-3xl lg:text-4xl font-normal font-display text-[var(--color-text)] tracking-tight mb-4">
            {{ product.name }}
          </h1>

          <!-- Price & Stock Badge Row (ui.md §4.3 tabular-nums) -->
          <div class="flex items-center gap-4 mb-6 pb-6 border-b border-[var(--color-surface-raised)]">
            <span class="text-2xl md:text-3xl font-bold font-sans text-[var(--color-primary)] tabular-nums">
              {{ formattedPrice }}
            </span>
            <StockBadge :stock="product.stock" />
          </div>

          <!-- Description (max-width 65ch per ui.md §4.3) -->
          <div class="mb-8">
            <h2 class="sr-only">Deskripsi Produk</h2>
            <p class="text-base text-[var(--color-text)] leading-relaxed max-w-[65ch] whitespace-pre-line">
              {{ product.description || 'Tidak ada deskripsi untuk produk ini.' }}
            </p>
          </div>

          <!-- Purchase Controls (Quantity Stepper + Add to Cart Button) -->
          <div class="mt-auto pt-6 border-t border-[var(--color-surface-raised)] flex flex-wrap items-center gap-4">
            <!-- Stepper -->
            <div class="flex items-center gap-2">
              <label for="quantity-select" class="text-sm font-medium text-[var(--color-text-secondary)]">
                Jumlah:
              </label>
              <QuantityStepper
                v-model="quantity"
                :min="1"
                :max="product.stock"
                :disabled="product.stock <= 0"
              />
            </div>

            <!-- Add to Cart Button -->
            <button
              type="button"
              :disabled="product.stock <= 0"
              :class="[
                'flex-1 min-w-[200px] h-11 px-6 text-sm font-semibold rounded-[var(--radius-control)] transition-colors flex items-center justify-center gap-2 cursor-pointer',
                product.stock > 0
                  ? 'bg-[var(--color-primary)] text-[var(--color-bg)] hover:bg-[var(--color-accent-hover)] active:bg-[var(--color-accent)]'
                  : 'bg-transparent text-[var(--color-text-muted)] border border-[var(--color-surface-raised)] cursor-not-allowed opacity-50',
              ]"
              @click="handleAddToCart"
            >
              <svg
                v-if="product.stock > 0"
                class="w-4 h-4"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                aria-hidden="true"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
              </svg>
              <span>{{ product.stock > 0 ? 'Tambah ke keranjang' : 'Stok habis' }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { catalogApi } from '@/api/catalog'
import { formatRupiah } from '@/utils/format'
import { useCartStore } from '@/stores/cart'
import { useToastStore } from '@/stores/toast'
import { useCartFeedbackStore } from '@/stores/feedback'
import StockBadge from '@/components/StockBadge.vue'
import QuantityStepper from '@/components/QuantityStepper.vue'
import EmptyState from '@/components/EmptyState.vue'
import ErrorState from '@/components/ErrorState.vue'

const route = useRoute()
const cartStore = useCartStore()
const toastStore = useToastStore()
const feedbackStore = useCartFeedbackStore()

const product = ref(null)
const quantity = ref(1)
const loading = ref(true)
const notFound = ref(false)
const errorMessage = ref(null)
const imageError = ref(false)

const formattedPrice = computed(() => {
  return product.value ? formatRupiah(product.value.price) : ''
})

async function loadProduct() {
  const id = route.params.id
  if (!id) {
    notFound.value = true
    loading.value = false
    return
  }

  loading.value = true
  notFound.value = false
  errorMessage.value = null
  imageError.value = false
  quantity.value = 1

  try {
    const res = await catalogApi.getProduct(id)
    product.value = res.data
  } catch (err) {
    if (err.response?.status === 404) {
      notFound.value = true
    } else {
      errorMessage.value = err.response?.data?.message || 'Gagal memuat detail produk.'
    }
  } finally {
    loading.value = false
  }
}

function handleAddToCart() {
  if (!product.value || product.value.stock <= 0) return

  const qty = quantity.value
  const currentInCart = cartStore.getItemQuantity(product.value.id)

  if (currentInCart + qty > product.value.stock) {
    const remaining = product.value.stock - currentInCart
    if (remaining <= 0) {
      toastStore.show(`Semua stok (${product.value.stock}) sudah ada di keranjang Anda.`, 'error')
    } else {
      toastStore.show(`Hanya tersisa ${remaining} item lagi yang dapat ditambahkan.`, 'error')
    }
    return
  }

  const result = cartStore.addItem(product.value, qty)
  if (result.success) {
    feedbackStore.trigger(qty)
  } else {
    toastStore.show(result.message || 'Jumlah melebihi stok yang tersedia.', 'error')
  }
}

watch(() => route.params.id, (newId) => {
  if (newId) loadProduct()
})

onMounted(() => {
  loadProduct()
})
</script>
