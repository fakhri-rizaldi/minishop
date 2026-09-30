<template>
  <div class="p-6 bg-[var(--color-surface)] border border-[var(--color-surface-raised)] rounded-[var(--radius-card)] sticky top-24">
    <h2 class="text-xl font-normal font-display text-[var(--color-text)] mb-4">
      Ringkasan Belanja
    </h2>

    <div class="space-y-3 pb-4 border-b border-[var(--color-surface-raised)] text-sm">
      <!-- Total Items -->
      <div class="flex justify-between items-center text-[var(--color-text-secondary)]">
        <span>Total Barang</span>
        <span class="font-medium text-[var(--color-text)] tabular-nums">{{ totalItems }} item</span>
      </div>

      <!-- Shipping estimate (free/included) -->
      <div class="flex justify-between items-center text-[var(--color-text-secondary)]">
        <span>Biaya Pengiriman</span>
        <span class="text-[var(--color-accent)] font-medium">Gratis</span>
      </div>
    </div>

    <!-- Total Price -->
    <div class="py-4 flex justify-between items-baseline">
      <span class="text-base font-medium text-[var(--color-text)]">Total</span>
      <span class="text-2xl font-bold font-sans text-[var(--color-primary)] tabular-nums">
        {{ formatRupiah(totalPrice) }}
      </span>
    </div>

    <!-- Checkout Action Button -->
    <router-link
      to="/checkout"
      :class="[
        'w-full h-11 px-6 text-sm font-semibold rounded-[var(--radius-control)] transition-colors flex items-center justify-center gap-2 mt-2',
        totalItems > 0
          ? 'bg-[var(--color-primary)] text-[var(--color-bg)] hover:bg-[var(--color-accent-hover)] active:bg-[var(--color-accent)]'
          : 'bg-transparent text-[var(--color-text-muted)] border border-[var(--color-surface-raised)] pointer-events-none opacity-50',
      ]"
    >
      <span>Lanjut ke Checkout</span>
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
      </svg>
    </router-link>

    <!-- Secondary Link: Back to Catalog -->
    <router-link
      to="/#katalog"
      class="block text-center text-xs text-[var(--color-text-secondary)] hover:text-[var(--color-primary)] transition-colors mt-4"
    >
      ← Tambah barang lain
    </router-link>
  </div>
</template>

<script setup>
import { formatRupiah } from '@/utils/format'

defineProps({
  totalItems: {
    type: Number,
    required: true,
  },
  totalPrice: {
    type: Number,
    required: true,
  },
})
</script>
