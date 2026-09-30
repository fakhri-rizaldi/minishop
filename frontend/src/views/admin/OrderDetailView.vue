<template>
  <AdminLayout>
    <main class="p-6 md:p-8 max-w-[1000px] w-full mx-auto">
      <!-- Breadcrumb Navigation -->
      <nav class="flex items-center gap-2 text-xs text-[var(--color-text-secondary)] mb-4" aria-label="Breadcrumb">
        <router-link to="/admin/orders" class="hover:text-[var(--color-primary)] transition-colors">
          Pesanan
        </router-link>
        <span class="opacity-50">›</span>
        <span class="text-[var(--color-text)] font-medium">
          {{ order ? order.order_number : 'Detail Pesanan' }}
        </span>
      </nav>

      <!-- 1. Loading State -->
      <div v-if="loading" class="p-8 bg-[var(--color-surface)] border border-[var(--color-surface-raised)] rounded-[var(--radius-card)] animate-pulse space-y-6">
        <div class="h-8 w-48 bg-[var(--color-surface-raised)] rounded"></div>
        <div class="h-24 bg-[var(--color-surface-raised)] rounded"></div>
        <div class="h-40 bg-[var(--color-surface-raised)] rounded"></div>
      </div>

      <!-- 2. 404 Not Found (REQ-ORD-03) -->
      <div v-else-if="notFound">
        <EmptyState
          title="Pesanan Tidak Ditemukan"
          message="Pesanan dengan ID yang Anda cari tidak terdaftar dalam database."
          action-text="Kembali ke Daftar Pesanan"
          action-link="/admin/orders"
        />
      </div>

      <!-- 3. Error State -->
      <div v-else-if="errorMessage">
        <ErrorState
          title="Gagal Memuat Detail Pesanan"
          :message="errorMessage"
          @retry="loadOrder"
        />
      </div>

      <!-- 4. Order Detail Card (REQ-ORD-02) -->
      <div v-else-if="order" class="space-y-6">
        <!-- Header -->
        <header class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div>
            <h1 class="text-2xl md:text-3xl font-normal font-display text-[var(--color-primary)] tracking-tight">
              Pesanan {{ order.order_number }}
            </h1>
            <p class="text-xs md:text-sm text-[var(--color-text-secondary)] mt-1">
              Dibuat pada {{ formatDate(order.created_at) }}
            </p>
          </div>

          <router-link
            to="/admin/orders"
            class="inline-flex items-center gap-1.5 text-xs text-[var(--color-text-secondary)] hover:text-[var(--color-primary)] transition-colors"
          >
            ← Kembali ke Daftar Pesanan
          </router-link>
        </header>

        <!-- Customer Card -->
        <section class="p-6 bg-[var(--color-surface)] border border-[var(--color-surface-raised)] rounded-[var(--radius-card)]">
          <h2 class="text-sm font-semibold text-[var(--color-text-secondary)] mb-4">
            Informasi Pembeli
          </h2>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
              <div class="text-xs text-[var(--color-text-secondary)] mb-0.5">Nama Pemesan</div>
              <div class="font-medium text-[var(--color-text)]">{{ order.customer_name }}</div>
            </div>
            <div>
              <div class="text-xs text-[var(--color-text-secondary)] mb-0.5">Alamat Email</div>
              <div class="font-medium text-[var(--color-text)]">{{ order.customer_email }}</div>
            </div>
          </div>
        </section>

        <!-- Items Snapshot Table -->
        <section class="bg-[var(--color-surface)] border border-[var(--color-surface-raised)] rounded-[var(--radius-card)] overflow-hidden">
          <div class="p-6 border-b border-[var(--color-surface-raised)]">
            <h2 class="text-base font-semibold text-[var(--color-text)]">
              Rincian Item Pesanan
            </h2>
          </div>

          <table class="w-full text-left text-sm">
            <thead class="bg-[var(--color-bg)] text-xs text-[var(--color-text-secondary)] border-b border-[var(--color-surface-raised)]">
              <tr>
                <th scope="col" class="py-3.5 px-6 font-medium">Nama Produk</th>
                <th scope="col" class="py-3.5 px-6 font-medium text-center">Jumlah</th>
                <th scope="col" class="py-3.5 px-6 font-medium text-right">Harga Satuan</th>
                <th scope="col" class="py-3.5 px-6 font-medium text-right">Subtotal</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[var(--color-surface-raised)]">
              <tr v-for="item in order.items" :key="item.id" class="hover:bg-[var(--color-surface-raised)]/30 transition-colors">
                <td class="py-3.5 px-6 font-medium text-[var(--color-text)]">
                  {{ item.product_name || item.name }}
                </td>
                <td class="py-3.5 px-6 text-center tabular-nums text-[var(--color-text-secondary)]">
                  {{ item.quantity }}
                </td>
                <td class="py-3.5 px-6 text-right tabular-nums text-[var(--color-text-secondary)]">
                  {{ formatRupiah(item.unit_price ?? item.price ?? 0) }}
                </td>
                <td class="py-3.5 px-6 text-right font-semibold tabular-nums text-[var(--color-text)]">
                  {{ formatRupiah(item.subtotal ?? ((item.unit_price ?? item.price ?? 0) * item.quantity)) }}
                </td>
              </tr>
            </tbody>
            <tfoot class="bg-[var(--color-bg)] border-t border-[var(--color-surface-raised)]">
              <tr>
                <td colspan="3" class="py-4 px-6 font-medium text-[var(--color-text)] text-right">
                  Total Nilai Pesanan
                </td>
                <td class="py-4 px-6 text-right font-bold text-lg font-sans text-[var(--color-primary)] tabular-nums">
                  {{ formatRupiah(order.total ?? order.total_price ?? 0) }}
                </td>
              </tr>
            </tfoot>
          </table>
        </section>
      </div>
    </main>
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { adminApi } from '@/api/admin'
import { formatRupiah, formatDate } from '@/utils/format'
import AdminLayout from '@/components/AdminLayout.vue'
import EmptyState from '@/components/EmptyState.vue'
import ErrorState from '@/components/ErrorState.vue'

const route = useRoute()
const order = ref(null)
const loading = ref(true)
const notFound = ref(false)
const errorMessage = ref(null)

async function loadOrder() {
  const id = route.params.id
  if (!id) {
    notFound.value = true
    loading.value = false
    return
  }

  loading.value = true
  notFound.value = false
  errorMessage.value = null

  try {
    const res = await adminApi.getOrder(id)
    order.value = res.data
  } catch (err) {
    if (err.response?.status === 404) {
      notFound.value = true
    } else {
      errorMessage.value = err.response?.data?.message || 'Gagal memuat informasi detail pesanan.'
    }
  } finally {
    loading.value = false
  }
}

watch(() => route.params.id, (newId) => {
  if (newId) loadOrder()
})

onMounted(() => {
  loadOrder()
})
</script>
