<template>
  <AdminLayout>
    <main class="p-6 md:p-8 max-w-[1200px] w-full mx-auto">
      <!-- Page Header -->
      <header class="mb-8">
        <h1 class="text-2xl md:text-3xl font-normal font-display text-[var(--color-primary)] tracking-tight">
          Daftar Pesanan
        </h1>
        <p class="text-xs md:text-sm text-[var(--color-text-secondary)] mt-1">
          Pantau seluruh transaksi dan riwayat belanja pelanggan toko.
        </p>
      </header>

      <!-- Loading State -->
      <div v-if="loading" class="p-8 bg-[var(--color-surface)] border border-[var(--color-surface-raised)] rounded-[var(--radius-card)] animate-pulse space-y-4">
        <div v-for="i in 5" :key="i" class="h-12 bg-[var(--color-surface-raised)] rounded"></div>
      </div>

      <!-- Error State -->
      <ErrorState
        v-else-if="errorMessage"
        title="Gagal Memuat Daftar Pesanan"
        :message="errorMessage"
        @retry="fetchOrders"
      />

      <!-- Empty State -->
      <EmptyState
        v-else-if="orders.length === 0"
        title="Belum Ada Pesanan"
        message="Belum ada transaksi pembelian yang tercatat di toko Anda."
      />

      <!-- Orders Section (Desktop Table + Mobile Cards) -->
      <div v-else class="space-y-6">
        <!-- Desktop Table -->
        <div class="hidden md:block bg-[var(--color-surface)] border border-[var(--color-surface-raised)] rounded-[var(--radius-card)] overflow-hidden">
          <table class="w-full text-left text-sm">
            <thead class="bg-[var(--color-bg)] text-xs text-[var(--color-text-secondary)] border-b border-[var(--color-surface-raised)]">
              <tr>
                <th scope="col" class="py-3.5 px-4 font-medium">Nomor Order</th>
                <th scope="col" class="py-3.5 px-4 font-medium">Pemesan</th>
                <th scope="col" class="py-3.5 px-4 font-medium text-center">Jumlah Item</th>
                <th scope="col" class="py-3.5 px-4 font-medium text-right">Total Harga</th>
                <th scope="col" class="py-3.5 px-4 font-medium text-right">Tanggal</th>
                <th scope="col" class="py-3.5 px-4 font-medium text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[var(--color-surface-raised)]">
              <tr
                v-for="order in orders"
                :key="order.id"
                class="hover:bg-[var(--color-surface-raised)]/30 transition-colors"
              >
                <!-- Order Number -->
                <td class="py-3.5 px-4 font-bold text-[var(--color-primary)] tabular-nums">
                  {{ order.order_number }}
                </td>

                <!-- Customer -->
                <td class="py-3.5 px-4">
                  <div class="font-medium text-[var(--color-text)]">{{ order.customer_name }}</div>
                  <div class="text-xs text-[var(--color-text-secondary)]">{{ order.customer_email }}</div>
                </td>

                <!-- Item Count -->
                <td class="py-3.5 px-4 text-center tabular-nums text-[var(--color-text-secondary)]">
                  {{ order.items_count || order.items?.length || 0 }} item
                </td>

                <!-- Total -->
                <td class="py-3.5 px-4 text-right font-semibold text-[var(--color-text)] tabular-nums">
                  {{ formatRupiah(order.total ?? order.total_price ?? 0) }}
                </td>

                <!-- Date -->
                <td class="py-3.5 px-4 text-right text-xs text-[var(--color-text-secondary)]">
                  {{ formatDate(order.created_at) }}
                </td>

                <!-- Action -->
                <td class="py-3.5 px-4 text-right">
                  <router-link
                    :to="`/admin/orders/${order.id || order.order_number}`"
                    class="px-2.5 py-1 text-xs font-medium text-[var(--color-accent)] hover:text-[var(--color-primary)] border border-[var(--color-border-strong)] rounded-[var(--radius-control)] hover:border-[var(--color-primary)] transition-colors"
                  >
                    Detail
                  </router-link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Mobile Card View -->
        <div class="md:hidden space-y-3">
          <div
            v-for="order in orders"
            :key="order.id"
            class="p-4 bg-[var(--color-surface)] border border-[var(--color-surface-raised)] rounded-[var(--radius-card)] flex flex-col gap-2.5"
          >
            <div class="flex items-center justify-between">
              <span class="font-bold text-[var(--color-primary)] text-sm tabular-nums">
                {{ order.order_number }}
              </span>
              <span class="text-xs text-[var(--color-text-secondary)]">
                {{ formatDate(order.created_at) }}
              </span>
            </div>

            <div class="text-xs text-[var(--color-text)]">
              <span class="font-medium">{{ order.customer_name }}</span> ({{ order.customer_email }})
            </div>

            <div class="flex items-center justify-between pt-2 border-t border-[var(--color-surface-raised)]">
              <div class="text-sm font-bold text-[var(--color-text)] tabular-nums">
                {{ formatRupiah(order.total ?? order.total_price ?? 0) }}
              </div>
              <router-link
                :to="`/admin/orders/${order.id || order.order_number}`"
                class="px-3 py-1 text-xs font-medium text-[var(--color-accent)] border border-[var(--color-border-strong)] rounded-[var(--radius-control)]"
              >
                Lihat Detail
              </router-link>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <Pagination
          :current-page="paginationMeta.current_page"
          :last-page="paginationMeta.last_page"
          :total="paginationMeta.total"
          @change-page="handlePageChange"
        />
      </div>
    </main>
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { adminApi } from '@/api/admin'
import { formatRupiah, formatDate } from '@/utils/format'
import AdminLayout from '@/components/AdminLayout.vue'
import Pagination from '@/components/Pagination.vue'
import EmptyState from '@/components/EmptyState.vue'
import ErrorState from '@/components/ErrorState.vue'

const orders = ref([])
const loading = ref(true)
const errorMessage = ref(null)
const currentPage = ref(1)

const paginationMeta = ref({
  current_page: 1,
  last_page: 1,
  total: 0,
})

async function fetchOrders() {
  loading.value = true
  errorMessage.value = null

  try {
    const res = await adminApi.getOrders({
      page: currentPage.value,
      per_page: 10,
    })

    orders.value = res.data || []
    paginationMeta.value = res.meta || {
      current_page: 1,
      last_page: 1,
      total: orders.value.length,
    }
  } catch (err) {
    errorMessage.value = err.response?.data?.message || 'Gagal memuat daftar order admin.'
  } finally {
    loading.value = false
  }
}

function handlePageChange(newPage) {
  currentPage.value = newPage
  fetchOrders()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

onMounted(() => {
  fetchOrders()
})
</script>
