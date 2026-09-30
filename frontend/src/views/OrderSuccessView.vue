<template>
  <main class="min-h-screen py-12 px-4 max-w-[800px] mx-auto">
    <!-- 1. Loading State -->
    <div v-if="loading" class="p-8 bg-[var(--color-surface)] border border-[var(--color-surface-raised)] rounded-[var(--radius-card)] animate-pulse space-y-6">
      <div class="h-8 w-48 bg-[var(--color-surface-raised)] rounded mx-auto"></div>
      <div class="h-4 w-64 bg-[var(--color-surface-raised)] rounded mx-auto"></div>
      <div class="h-24 bg-[var(--color-surface-raised)] rounded"></div>
      <div class="h-40 bg-[var(--color-surface-raised)] rounded"></div>
    </div>

    <!-- 2. 404 Not Found State (REQ-CO-12) -->
    <div v-else-if="notFound">
      <EmptyState
        title="Pesanan Tidak Ditemukan"
        message="Nomor pesanan yang Anda tuju tidak terdaftar di sistem kami atau tautan tidak valid."
        action-text="Kembali ke Katalog"
        action-link="/"
      />
    </div>

    <!-- 3. Error State -->
    <div v-else-if="errorMessage">
      <ErrorState
        title="Gagal Memuat Data Pesanan"
        :message="errorMessage"
        retry-text="Coba lagi"
        @retry="loadOrder"
      />
    </div>

    <!-- 4. Order Success Card (ui.md §5.7) -->
    <div
      v-else-if="order"
      class="bg-[var(--color-surface)] border border-[var(--color-surface-raised)] rounded-[var(--radius-card)] p-6 md:p-10"
    >
      <!-- Header / Icon -->
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-[var(--color-primary)]/10 text-[var(--color-primary)] mb-4">
          <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
          </svg>
        </div>
        <h1 class="text-2xl md:text-3xl font-normal font-display text-[var(--color-text)] tracking-tight">
          Pesanan Diterima
        </h1>
        <p class="text-sm text-[var(--color-text-secondary)] mt-1">
          Terima kasih telah berbelanja di MiniShop. Rincian pesanan Anda tercatat di bawah ini.
        </p>
      </div>

      <!-- Order Metadata Box -->
      <div class="p-4 bg-[var(--color-bg)] border border-[var(--color-surface-raised)] rounded-[var(--radius-control)] mb-8 grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
        <div>
          <div class="text-xs text-[var(--color-text-secondary)] mb-1">Nomor Pesanan</div>
          <div class="font-bold text-[var(--color-primary)] tabular-nums tracking-wide">
            {{ order.order_number }}
          </div>
        </div>
        <div>
          <div class="text-xs text-[var(--color-text-secondary)] mb-1">Nama Pemesan</div>
          <div class="font-medium text-[var(--color-text)] truncate">
            {{ order.customer_name }}
          </div>
        </div>
        <div>
          <div class="text-xs text-[var(--color-text-secondary)] mb-1">Email Pemesan</div>
          <div class="font-medium text-[var(--color-text)] truncate">
            {{ order.customer_email }}
          </div>
        </div>
      </div>

      <!-- Order Items Table -->
      <div class="mb-8">
        <h2 class="text-base font-semibold text-[var(--color-text)] mb-3">
          Daftar Produk
        </h2>
        <div class="border border-[var(--color-surface-raised)] rounded-[var(--radius-control)] overflow-hidden">
          <table class="w-full text-left text-sm">
            <thead class="bg-[var(--color-bg)] text-xs text-[var(--color-text-secondary)] border-b border-[var(--color-surface-raised)]">
              <tr>
                <th scope="col" class="py-3 px-4 font-medium">Produk</th>
                <th scope="col" class="py-3 px-4 font-medium text-center">Jumlah</th>
                <th scope="col" class="py-3 px-4 font-medium text-right">Harga</th>
                <th scope="col" class="py-3 px-4 font-medium text-right">Subtotal</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[var(--color-surface-raised)]">
              <tr v-for="item in order.items" :key="item.id" class="hover:bg-[var(--color-surface-raised)]/30 transition-colors">
                <td class="py-3.5 px-4 font-medium text-[var(--color-text)]">
                  {{ item.product_name || item.name }}
                </td>
                <td class="py-3.5 px-4 text-center tabular-nums text-[var(--color-text-secondary)]">
                  {{ item.quantity }}
                </td>
                <td class="py-3.5 px-4 text-right tabular-nums text-[var(--color-text-secondary)]">
                  {{ formatRupiah(item.unit_price ?? item.price ?? 0) }}
                </td>
                <td class="py-3.5 px-4 text-right font-semibold tabular-nums text-[var(--color-text)]">
                  {{ formatRupiah(item.subtotal ?? ((item.unit_price ?? item.price ?? 0) * item.quantity)) }}
                </td>
              </tr>
            </tbody>
            <tfoot class="bg-[var(--color-bg)] border-t border-[var(--color-surface-raised)]">
              <tr>
                <td colspan="3" class="py-3.5 px-4 font-medium text-[var(--color-text)] text-right">
                  Total Pembayaran
                </td>
                <td class="py-3.5 px-4 text-right font-bold text-base font-sans text-[var(--color-primary)] tabular-nums">
                  {{ formatRupiah(order.total ?? order.total_price ?? 0) }}
                </td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>

      <!-- Action: Shop Again -->
      <div class="text-center pt-2">
        <router-link
          to="/"
          class="inline-flex items-center justify-center h-11 px-8 bg-[var(--color-primary)] text-[var(--color-bg)] hover:bg-[var(--color-accent-hover)] active:bg-[var(--color-accent)] font-semibold rounded-[var(--radius-control)] transition-colors text-sm"
        >
          Belanja Lagi
        </router-link>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { ordersApi } from '@/api/orders'
import { formatRupiah, formatDate } from '@/utils/format'
import EmptyState from '@/components/EmptyState.vue'
import ErrorState from '@/components/ErrorState.vue'

const route = useRoute()
const order = ref(null)
const loading = ref(true)
const notFound = ref(false)
const errorMessage = ref(null)

async function loadOrder() {
  const orderNumber = route.params.orderNumber
  if (!orderNumber) {
    notFound.value = true
    loading.value = false
    return
  }

  loading.value = true
  notFound.value = false
  errorMessage.value = null

  try {
    const res = await ordersApi.getOrder(orderNumber)
    order.value = res.data
  } catch (err) {
    if (err.response?.status === 404) {
      notFound.value = true
    } else {
      errorMessage.value = err.response?.data?.message || 'Gagal memuat informasi pesanan.'
    }
  } finally {
    loading.value = false
  }
}

watch(() => route.params.orderNumber, (newNum) => {
  if (newNum) loadOrder()
})

onMounted(() => {
  loadOrder()
})
</script>
