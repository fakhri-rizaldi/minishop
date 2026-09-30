<template>
  <main class="min-h-screen py-8 px-4 max-w-[1200px] mx-auto">
    <!-- Header -->
    <header class="mb-8 flex flex-wrap items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl md:text-4xl font-normal font-display text-[var(--color-primary)] tracking-tight">
          Keranjang
        </h1>
        <p class="text-sm text-[var(--color-text-secondary)] mt-1">
          Periksa produk pilihan Anda sebelum melanjutkan ke checkout.
        </p>
      </div>

      <!-- Clear Cart Button (if not empty) -->
      <button
        v-if="!cartStore.isEmpty"
        type="button"
        class="text-xs text-[var(--color-text-secondary)] hover:text-[var(--color-danger)] transition-colors cursor-pointer py-1 px-2 rounded"
        @click="showClearConfirm = true"
      >
        Kosongkan Keranjang
      </button>
    </header>

    <!-- 1. Empty State (REQ-CART-09) -->
    <div v-if="cartStore.isEmpty" class="py-12">
      <EmptyState
        title="Keranjang Masih Kosong"
        message="Anda belum menambahkan produk apa pun ke dalam keranjang belanja."
        action-text="Lihat Katalog"
        action-link="/#katalog"
      />
    </div>

    <!-- 2. Cart Content (List + Summary) -->
    <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
      <!-- Left: Line Items List -->
      <section class="lg:col-span-8 space-y-4" aria-label="Daftar barang di keranjang">
        <CartLine
          v-for="item in cartStore.items"
          :key="item.id"
          :item="item"
          @update-quantity="handleQuantityChange"
          @remove="handleRemoveItem"
        />
      </section>

      <!-- Right: Order Summary Sticky Panel -->
      <aside class="lg:col-span-4" aria-label="Ringkasan pembayaran">
        <CartSummary
          :total-items="cartStore.totalItems"
          :total-price="cartStore.totalPrice"
        />
      </aside>
    </div>

    <!-- Clear Cart Confirmation Dialog -->
    <ConfirmDialog
      :is-open="showClearConfirm"
      title="Kosongkan Keranjang?"
      message="Semua produk yang ada di keranjang Anda akan dihapus."
      confirm-text="Ya, Kosongkan"
      cancel-text="Batal"
      :is-danger="true"
      @confirm="confirmClearCart"
      @cancel="showClearConfirm = false"
    />
  </main>
</template>

<script setup>
import { ref } from 'vue'
import { useCartStore } from '@/stores/cart'
import { useToastStore } from '@/stores/toast'
import CartLine from '@/components/CartLine.vue'
import CartSummary from '@/components/CartSummary.vue'
import EmptyState from '@/components/EmptyState.vue'
import ConfirmDialog from '@/components/ConfirmDialog.vue'

const cartStore = useCartStore()
const toastStore = useToastStore()
const showClearConfirm = ref(false)

function handleQuantityChange({ id, quantity }) {
  const result = cartStore.setQuantity(id, quantity)
  if (result.message) {
    toastStore.show(result.message, result.clamped ? 'error' : 'success')
  }
}

function handleRemoveItem(id) {
  const item = cartStore.items.find((i) => i.id === id)
  const name = item ? item.name : 'Produk'
  cartStore.removeItem(id)
  toastStore.show(`${name} dihapus dari keranjang.`, 'success')
}

function confirmClearCart() {
  cartStore.clearCart()
  showClearConfirm.value = false
  toastStore.show('Keranjang berhasil dikosongkan.', 'success')
}
</script>
