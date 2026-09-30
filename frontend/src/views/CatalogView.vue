<template>
  <main class="min-h-screen">
    <!-- Hero Landing Section (REQ-CAT-16, ui.md §5.3) -->
    <section
      aria-label="Selamat Datang di MiniShop"
      class="hero-banner relative w-full min-h-screen flex flex-col justify-center items-center text-center px-4 sm:px-6 lg:px-8 py-20"
    >
      <div class="relative z-10 max-w-4xl mx-auto flex flex-col items-center">
        <!-- Hero Headline -->
        <h1 class="animate-hero-1 font-display text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-normal text-[var(--color-primary)] tracking-tight leading-[1.12] mb-6 drop-shadow-md">
          Hadirkan Ketenangan Botani di Setiap Sudut Ruang Anda Dengan Minishop Kami
        </h1>

        <!-- Hero Description -->
        <p class="animate-hero-2 text-base sm:text-lg md:text-xl text-[var(--color-text)] max-w-2xl leading-relaxed mb-10 drop-shadow">
          Kurasi tanaman hias indoor segar, pot keramik artisan, dan perlengkapan berkebun urban pilihan untuk menyegarkan suasana rumah Anda dengan katalog minishop Kami.
        </p>

        <!-- CTA Action -->
        <div class="animate-hero-3 flex flex-col sm:flex-row items-center gap-4">
          <a
            href="#katalog"
            @click.prevent="scrollToCatalog"
            class="group inline-flex items-center justify-center gap-2.5 px-8 py-4 rounded-xl bg-[var(--color-primary)] text-[var(--color-bg)] font-semibold text-base hover:bg-[var(--color-accent-hover)] active:scale-[0.98] transition-all duration-200 cursor-pointer shadow-lg hover:shadow-[0_0_24px_rgba(197,239,203,0.3)]"
          >
            <span>Jelajahi Koleksi</span>
            <svg class="w-5 h-5 transition-transform duration-200 group-hover:translate-y-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
            </svg>
          </a>
        </div>
      </div>

      <!-- Bottom Scroll Indicator Cue -->
      <button
        type="button"
        @click="scrollToCatalog"
        aria-label="Scroll ke katalog produk"
        class="absolute bottom-8 left-1/2 -translate-x-1/2 text-[var(--color-text-secondary)] hover:text-[var(--color-primary)] transition-colors animate-bounce cursor-pointer p-2"
      >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
      </button>
    </section>

    <!-- Catalog Content Container -->
    <div class="py-8 px-4 max-w-[1200px] mx-auto">
      <!-- Catalog Header -->
      <header id="katalog" class="mb-8 scroll-mt-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
          <div>
            <h2 class="text-3xl md:text-4xl font-normal font-display text-[var(--color-primary)] tracking-tight">
              Katalog Produk
            </h2>
            <p class="text-sm text-[var(--color-text-secondary)] mt-1">
              Pilih dari beragam tanaman indoor dan pot pilihan.
            </p>
          </div>

          <!-- Search Bar -->
          <div class="w-full md:w-80">
            <SearchInput
              v-model="searchQuery"
              placeholder="Cari tanaman, pot..."
              label="Cari produk di katalog"
            />
          </div>
        </div>

        <!-- Category Filter Chips -->
        <div class="border-b border-[var(--color-surface-raised)] pb-4">
          <CategoryFilter v-model="selectedCategory" />
        </div>
      </header>

    <!-- Content Sections with Transition -->
    <section aria-label="Daftar Produk">
      <Transition name="fade-slide" mode="out-in">
        <!-- 1. Loading State (Skeleton Grid) -->
        <div
          v-if="loading"
          key="loading"
          class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6"
          aria-busy="true"
          aria-label="Memuat daftar produk"
        >
          <SkeletonCard v-for="i in 8" :key="i" />
        </div>

        <!-- 2. Error State -->
        <ErrorState
          v-else-if="errorMessage"
          key="error"
          title="Gagal Memuat Katalog"
          :message="errorMessage"
          retry-text="Coba lagi"
          @retry="fetchProducts"
        />

        <!-- 3. Empty State (REQ-CAT-06, BR-CAT-6) -->
        <EmptyState
          v-else-if="products.length === 0"
          key="empty"
          title="Produk Tidak Ditemukan"
          message="Tidak ada produk yang cocok dengan pencarian atau filter yang Anda pilih."
          action-text="Reset Filter"
          @action="resetFilters"
        />

        <!-- 4. Product Grid & Pagination -->
        <div v-else key="content">
          <ProductGrid :products="products" />

          <!-- Pagination -->
          <div class="mt-8">
            <Pagination
              :current-page="paginationMeta.current_page"
              :last-page="paginationMeta.last_page"
              :total="paginationMeta.total"
              @change-page="handlePageChange"
            />
          </div>
        </div>
      </Transition>
    </section>
    </div>
  </main>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { catalogApi } from '@/api/catalog'
import { useDebounce } from '@/composables/useDebounce'
import SearchInput from '@/components/SearchInput.vue'
import CategoryFilter from '@/components/CategoryFilter.vue'
import ProductGrid from '@/components/ProductGrid.vue'
import SkeletonCard from '@/components/SkeletonCard.vue'
import EmptyState from '@/components/EmptyState.vue'
import ErrorState from '@/components/ErrorState.vue'
import Pagination from '@/components/Pagination.vue'

const route = useRoute()
const router = useRouter()

// Reactive States initialized from URL query params (REQ-CAT-05, BR-CAT-1 debounce 250ms)
const searchQuery = ref(route.query.q?.toString() || '')
const debouncedSearch = useDebounce(searchQuery, 250)
const selectedCategory = ref(route.query.category?.toString() || '')
const currentPage = ref(Number(route.query.page) || 1)

const products = ref([])
const loading = ref(true)
const errorMessage = ref(null)
const paginationMeta = ref({
  current_page: 1,
  last_page: 1,
  total: 0,
})

// Prevent race condition when multiple requests fire quickly
let currentRequestId = 0

/**
 * Fetch products from backend API with active filters
 */
async function fetchProducts() {
  const reqId = ++currentRequestId
  loading.value = true
  errorMessage.value = null

  try {
    const res = await catalogApi.getProducts({
      search: debouncedSearch.value || undefined,
      category: selectedCategory.value || undefined,
      page: currentPage.value,
      per_page: 12,
    })

    if (reqId === currentRequestId) {
      products.value = res.data || []
      paginationMeta.value = res.meta || {
        current_page: 1,
        last_page: 1,
        total: products.value.length,
      }
    }
  } catch (err) {
    if (reqId === currentRequestId) {
      errorMessage.value = err.response?.data?.message || 'Gagal memuat produk. Silakan periksa koneksi dan coba lagi.'
    }
  } finally {
    if (reqId === currentRequestId) {
      loading.value = false
    }
  }
}

/**
 * Update URL query parameters cleanly
 */
function updateUrlQuery() {
  const query = {}
  if (debouncedSearch.value) query.q = debouncedSearch.value
  if (selectedCategory.value) query.category = selectedCategory.value
  if (currentPage.value > 1) query.page = currentPage.value

  router.replace({ query })
}

/**
 * Reset all active search and category filters (REQ-CAT-06)
 */
function resetFilters() {
  searchQuery.value = ''
  selectedCategory.value = ''
  currentPage.value = 1
}

/**
 * Handle page navigation
 */
function handlePageChange(newPage) {
  if (newPage === currentPage.value) return
  currentPage.value = newPage
  updateUrlQuery()
  fetchProducts()
  scrollToCatalog()
}

/**
 * Smooth scroll to catalog section
 */
function scrollToCatalog() {
  const el = document.getElementById('katalog')
  if (el) {
    el.scrollIntoView({ behavior: 'smooth' })
  }
}

// Watch debounced search changes: reset to page 1 & fetch
watch(debouncedSearch, (newVal, oldVal) => {
  if (newVal !== oldVal) {
    currentPage.value = 1
    updateUrlQuery()
    fetchProducts()
  }
})

// Watch category filter changes: reset to page 1 & fetch
watch(selectedCategory, (newVal, oldVal) => {
  if (newVal !== oldVal) {
    currentPage.value = 1
    updateUrlQuery()
    fetchProducts()
  }
})

// Watch route query changes (e.g. browser back/forward navigation)
watch(
  () => route.query,
  (newQuery) => {
    const qFromUrl = newQuery.q?.toString() || ''
    const catFromUrl = newQuery.category?.toString() || ''
    const pageFromUrl = Number(newQuery.page) || 1

    let changed = false
    if (searchQuery.value !== qFromUrl) {
      searchQuery.value = qFromUrl
      changed = true
    }
    if (selectedCategory.value !== catFromUrl) {
      selectedCategory.value = catFromUrl
      changed = true
    }
    if (currentPage.value !== pageFromUrl) {
      currentPage.value = pageFromUrl
      changed = true
    }

    if (changed) {
      fetchProducts()
    }
  }
)

// Watch hash changes (e.g. navigation back to /#katalog)
watch(
  () => route.hash,
  (newHash) => {
    if (newHash === '#katalog') {
      setTimeout(() => {
        scrollToCatalog()
      }, 50)
    }
  }
)

onMounted(() => {
  fetchProducts()
  if (route.hash === '#katalog') {
    setTimeout(() => {
      scrollToCatalog()
    }, 100)
  }
})
</script>
