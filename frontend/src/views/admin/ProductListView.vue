<template>
  <AdminLayout>
    <main class="p-6 md:p-8 max-w-[1200px] w-full mx-auto">
      <!-- Page Header -->
      <header class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
          <h1 class="text-2xl md:text-3xl font-normal font-display text-[var(--color-primary)] tracking-tight">
            Manajemen Produk
          </h1>
          <p class="text-xs md:text-sm text-[var(--color-text-secondary)] mt-1">
            Kelola katalog tanaman, stok, dan harga produk toko.
          </p>
        </div>

        <!-- Add Product Button -->
        <router-link
          to="/admin/products/new"
          class="inline-flex items-center justify-center gap-2 h-10 px-5 bg-[var(--color-primary)] text-[var(--color-bg)] hover:bg-[var(--color-accent-hover)] active:bg-[var(--color-accent)] text-sm font-semibold rounded-[var(--radius-control)] transition-colors shrink-0"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          <span>Tambah Produk</span>
        </router-link>
      </header>

      <!-- Filters Row -->
      <div class="flex flex-col sm:flex-row gap-3 mb-6">
        <!-- Search Input -->
        <div class="flex-1 max-w-sm">
          <SearchInput
            v-model="searchQuery"
            placeholder="Cari nama produk..."
            label="Cari produk admin"
          />
        </div>

        <!-- Category Dropdown Filter -->
        <div class="w-full sm:w-48">
          <select
            v-model="selectedCategory"
            aria-label="Filter kategori"
            class="w-full h-11 px-3.5 bg-[var(--color-bg)] border border-[var(--color-border-strong)] rounded-[var(--radius-control)] text-sm text-[var(--color-text)] focus:outline-none focus:border-[var(--color-primary)] transition-colors cursor-pointer"
          >
            <option value="">Semua Kategori</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.slug">
              {{ cat.name }}
            </option>
          </select>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="p-8 bg-[var(--color-surface)] border border-[var(--color-surface-raised)] rounded-[var(--radius-card)] animate-pulse space-y-4">
        <div v-for="i in 5" :key="i" class="h-12 bg-[var(--color-surface-raised)] rounded"></div>
      </div>

      <!-- Error State -->
      <ErrorState
        v-else-if="errorMessage"
        title="Gagal Memuat Produk"
        :message="errorMessage"
        @retry="fetchProducts"
      />

      <!-- Empty State -->
      <EmptyState
        v-else-if="products.length === 0"
        title="Tidak Ada Produk"
        message="Tidak ada data produk yang cocok dengan kriteria pencarian."
        action-text="Reset Filter"
        @action="resetFilters"
      />

      <!-- Table Section (Desktop Table, Mobile Cards) -->
      <div v-else class="space-y-6">
        <!-- Desktop Table View (Hidden on mobile) -->
        <div class="hidden md:block bg-[var(--color-surface)] border border-[var(--color-surface-raised)] rounded-[var(--radius-card)] overflow-hidden">
          <table class="w-full text-left text-sm">
            <thead class="bg-[var(--color-bg)] text-xs text-[var(--color-text-secondary)] border-b border-[var(--color-surface-raised)]">
              <tr>
                <th scope="col" class="py-3.5 px-4 font-medium">Produk</th>
                <th scope="col" class="py-3.5 px-4 font-medium">Kategori</th>
                <th scope="col" class="py-3.5 px-4 font-medium text-right">Harga</th>
                <th scope="col" class="py-3.5 px-4 font-medium text-center">Stok</th>
                <th scope="col" class="py-3.5 px-4 font-medium text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[var(--color-surface-raised)]">
              <tr
                v-for="product in products"
                :key="product.id"
                class="hover:bg-[var(--color-surface-raised)]/30 transition-colors"
              >
                <!-- Image & Name -->
                <td class="py-3 px-4">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 shrink-0 bg-[var(--color-surface-raised)] rounded-[var(--radius-media)] overflow-hidden">
                      <img
                        v-if="product.image_url"
                        :src="product.image_url"
                        :alt="product.name"
                        class="w-full h-full object-cover"
                      />
                    </div>
                    <span class="font-medium text-[var(--color-text)]">{{ product.name }}</span>
                  </div>
                </td>

                <!-- Category -->
                <td class="py-3 px-4 text-[var(--color-text-secondary)]">
                  {{ product.category?.name || '—' }}
                </td>

                <!-- Price -->
                <td class="py-3 px-4 text-right font-medium text-[var(--color-text)] tabular-nums">
                  {{ formatRupiah(product.price) }}
                </td>

                <!-- Stock -->
                <td class="py-3 px-4 text-center">
                  <StockBadge :stock="product.stock" />
                </td>

                <!-- Actions -->
                <td class="py-3 px-4 text-right">
                  <div class="inline-flex items-center gap-2">
                    <router-link
                      :to="`/admin/products/${product.id}/edit`"
                      class="px-2.5 py-1 text-xs font-medium text-[var(--color-accent)] hover:text-[var(--color-primary)] border border-[var(--color-border-strong)] rounded-[var(--radius-control)] hover:border-[var(--color-primary)] transition-colors"
                    >
                      Ubah
                    </router-link>
                    <button
                      type="button"
                      class="px-2.5 py-1 text-xs font-medium text-[var(--color-danger)] hover:bg-[var(--color-danger)]/10 border border-[var(--color-danger)]/40 rounded-[var(--radius-control)] transition-colors cursor-pointer"
                      @click="promptDelete(product)"
                    >
                      Hapus
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Mobile Card View (Visible on mobile) -->
        <div class="md:hidden space-y-3">
          <div
            v-for="product in products"
            :key="product.id"
            class="p-4 bg-[var(--color-surface)] border border-[var(--color-surface-raised)] rounded-[var(--radius-card)] flex flex-col gap-3"
          >
            <div class="flex items-center gap-3">
              <div class="w-14 h-14 shrink-0 bg-[var(--color-surface-raised)] rounded-[var(--radius-media)] overflow-hidden">
                <img
                  v-if="product.image_url"
                  :src="product.image_url"
                  :alt="product.name"
                  class="w-full h-full object-cover"
                />
              </div>
              <div class="flex-1 min-w-0">
                <div class="text-xs text-[var(--color-text-secondary)]">{{ product.category?.name }}</div>
                <h3 class="text-sm font-semibold text-[var(--color-text)] truncate">{{ product.name }}</h3>
                <div class="text-sm font-bold text-[var(--color-primary)] tabular-nums mt-0.5">
                  {{ formatRupiah(product.price) }}
                </div>
              </div>
              <StockBadge :stock="product.stock" />
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-[var(--color-surface-raised)]">
              <router-link
                :to="`/admin/products/${product.id}/edit`"
                class="px-3 py-1.5 text-xs font-medium text-[var(--color-accent)] border border-[var(--color-border-strong)] rounded-[var(--radius-control)]"
              >
                Ubah
              </router-link>
              <button
                type="button"
                class="px-3 py-1.5 text-xs font-medium text-[var(--color-danger)] border border-[var(--color-danger)]/40 rounded-[var(--radius-control)]"
                @click="promptDelete(product)"
              >
                Hapus
              </button>
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

    <!-- Confirm Delete Dialog (REQ-ADM-05) -->
    <ConfirmDialog
      :is-open="showDeleteDialog"
      title="Hapus Produk?"
      :message="`Apakah Anda yakin ingin menghapus produk '${productToDelete?.name}'? Riwayat pesanan lama yang memuat produk ini tidak akan terhapus.`"
      confirm-text="Ya, Hapus Produk"
      cancel-text="Batal"
      :is-danger="true"
      @confirm="confirmDeleteProduct"
      @cancel="showDeleteDialog = false"
    />
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { adminApi } from '@/api/admin'
import { catalogApi } from '@/api/catalog'
import { formatRupiah } from '@/utils/format'
import { useToastStore } from '@/stores/toast'
import { useDebounce } from '@/composables/useDebounce'
import AdminLayout from '@/components/AdminLayout.vue'
import SearchInput from '@/components/SearchInput.vue'
import StockBadge from '@/components/StockBadge.vue'
import Pagination from '@/components/Pagination.vue'
import EmptyState from '@/components/EmptyState.vue'
import ErrorState from '@/components/ErrorState.vue'
import ConfirmDialog from '@/components/ConfirmDialog.vue'

const toastStore = useToastStore()

const products = ref([])
const categories = ref([])
const loading = ref(true)
const errorMessage = ref(null)

const searchQuery = ref('')
const debouncedSearch = useDebounce(searchQuery, 300)
const selectedCategory = ref('')
const currentPage = ref(1)

const paginationMeta = ref({
  current_page: 1,
  last_page: 1,
  total: 0,
})

const showDeleteDialog = ref(false)
const productToDelete = ref(null)

async function fetchCategories() {
  try {
    const res = await catalogApi.getCategories()
    categories.value = res.data || []
  } catch (err) {
    console.error('Gagal mengambil kategori:', err)
  }
}

async function fetchProducts() {
  loading.value = true
  errorMessage.value = null

  try {
    const res = await adminApi.getProducts({
      search: debouncedSearch.value || undefined,
      category: selectedCategory.value || undefined,
      page: currentPage.value,
      per_page: 10,
    })

    products.value = res.data || []
    paginationMeta.value = res.meta || {
      current_page: 1,
      last_page: 1,
      total: products.value.length,
    }
  } catch (err) {
    errorMessage.value = err.response?.data?.message || 'Gagal memuat daftar produk admin.'
  } finally {
    loading.value = false
  }
}

function resetFilters() {
  searchQuery.value = ''
  selectedCategory.value = ''
  currentPage.value = 1
}

function handlePageChange(newPage) {
  currentPage.value = newPage
  fetchProducts()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

function promptDelete(product) {
  productToDelete.value = product
  showDeleteDialog.value = true
}

async function confirmDeleteProduct() {
  if (!productToDelete.value) return

  try {
    await adminApi.deleteProduct(productToDelete.value.id)
    toastStore.show(`Produk '${productToDelete.value.name}' berhasil dihapus.`, 'success')
    showDeleteDialog.value = false
    productToDelete.value = null
    fetchProducts()
  } catch (err) {
    toastStore.show(err.response?.data?.message || 'Gagal menghapus produk.', 'error')
  }
}

watch([debouncedSearch, selectedCategory], () => {
  currentPage.value = 1
  fetchProducts()
})

onMounted(() => {
  fetchCategories()
  fetchProducts()
})
</script>
