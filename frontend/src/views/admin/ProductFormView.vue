<template>
  <AdminLayout>
    <main class="p-6 md:p-8 max-w-[1000px] w-full mx-auto">
      <!-- Breadcrumb & Header -->
      <header class="mb-8">
        <nav class="flex items-center gap-2 text-xs text-[var(--color-text-secondary)] mb-3" aria-label="Breadcrumb">
          <router-link to="/admin/products" class="hover:text-[var(--color-primary)] transition-colors">
            Produk
          </router-link>
          <span class="opacity-50">›</span>
          <span class="text-[var(--color-text)] font-medium">{{ isEdit ? 'Ubah Produk' : 'Tambah Produk' }}</span>
        </nav>

        <h1 class="text-2xl md:text-3xl font-normal font-display text-[var(--color-primary)] tracking-tight">
          {{ isEdit ? 'Ubah Produk' : 'Tambah Produk Baru' }}
        </h1>
        <p class="text-xs md:text-sm text-[var(--color-text-secondary)] mt-1">
          {{ isEdit ? 'Perbarui informasi dan stok produk.' : 'Isi formulir untuk menambahkan tanaman baru ke katalog.' }}
        </p>
      </header>

      <!-- Form Grid (Form Left, Image Preview Right) -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Form Section (7 cols) -->
        <section class="lg:col-span-7 bg-[var(--color-surface)] border border-[var(--color-surface-raised)] rounded-[var(--radius-card)] p-6 md:p-8">
          <form @submit.prevent="handleSubmit" novalidate class="space-y-5">
            <!-- Name -->
            <div>
              <label for="name" class="block text-xs font-medium text-[var(--color-text-secondary)] mb-1.5">
                Nama Produk <span class="text-[var(--color-danger)]">*</span>
              </label>
              <input
                id="name"
                v-model="form.name"
                type="text"
                required
                placeholder="Contoh: Monstera Deliciosa"
                :class="[
                  'w-full h-11 px-3.5 bg-[var(--color-bg)] border rounded-[var(--radius-control)] text-sm text-[var(--color-text)] placeholder:text-[var(--color-text-secondary)] focus:outline-none transition-colors',
                  errors.name ? 'border-[var(--color-danger)]' : 'border-[var(--color-border-strong)] focus:border-[var(--color-primary)]',
                ]"
                :aria-invalid="!!errors.name"
                :aria-describedby="errors.name ? 'name-error' : undefined"
              />
              <p v-if="errors.name" id="name-error" class="mt-1 text-xs text-[var(--color-danger)]">{{ errors.name[0] }}</p>
            </div>

            <!-- Category -->
            <div>
              <label for="category_id" class="block text-xs font-medium text-[var(--color-text-secondary)] mb-1.5">
                Kategori <span class="text-[var(--color-danger)]">*</span>
              </label>
              <select
                id="category_id"
                v-model="form.category_id"
                required
                :class="[
                  'w-full h-11 px-3.5 bg-[var(--color-bg)] border rounded-[var(--radius-control)] text-sm text-[var(--color-text)] focus:outline-none transition-colors cursor-pointer',
                  errors.category_id ? 'border-[var(--color-danger)]' : 'border-[var(--color-border-strong)] focus:border-[var(--color-primary)]',
                ]"
                :aria-invalid="!!errors.category_id"
                :aria-describedby="errors.category_id ? 'category-error' : undefined"
              >
                <option value="" disabled>Pilih kategori...</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                  {{ cat.name }}
                </option>
              </select>
              <p v-if="errors.category_id" id="category-error" class="mt-1 text-xs text-[var(--color-danger)]">{{ errors.category_id[0] }}</p>
            </div>

            <!-- Price & Stock Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <!-- Price -->
              <div>
                <label for="price" class="block text-xs font-medium text-[var(--color-text-secondary)] mb-1.5">
                  Harga (Rupiah) <span class="text-[var(--color-danger)]">*</span>
                </label>
                <input
                  id="price"
                  v-model.number="form.price"
                  type="number"
                  min="0"
                  step="1"
                  required
                  placeholder="150000"
                  :class="[
                    'w-full h-11 px-3.5 bg-[var(--color-bg)] border rounded-[var(--radius-control)] text-sm text-[var(--color-text)] tabular-nums focus:outline-none transition-colors',
                    errors.price ? 'border-[var(--color-danger)]' : 'border-[var(--color-border-strong)] focus:border-[var(--color-primary)]',
                  ]"
                  :aria-invalid="!!errors.price"
                  :aria-describedby="errors.price ? 'price-error' : undefined"
                />
                <p v-if="errors.price" id="price-error" class="mt-1 text-xs text-[var(--color-danger)]">{{ errors.price[0] }}</p>
              </div>

              <!-- Stock -->
              <div>
                <label for="stock" class="block text-xs font-medium text-[var(--color-text-secondary)] mb-1.5">
                  Jumlah Stok <span class="text-[var(--color-danger)]">*</span>
                </label>
                <input
                  id="stock"
                  v-model.number="form.stock"
                  type="number"
                  min="0"
                  step="1"
                  required
                  placeholder="10"
                  :class="[
                    'w-full h-11 px-3.5 bg-[var(--color-bg)] border rounded-[var(--radius-control)] text-sm text-[var(--color-text)] tabular-nums focus:outline-none transition-colors',
                    errors.stock ? 'border-[var(--color-danger)]' : 'border-[var(--color-border-strong)] focus:border-[var(--color-primary)]',
                  ]"
                  :aria-invalid="!!errors.stock"
                  :aria-describedby="errors.stock ? 'stock-error' : undefined"
                />
                <p v-if="errors.stock" id="stock-error" class="mt-1 text-xs text-[var(--color-danger)]">{{ errors.stock[0] }}</p>
              </div>
            </div>

            <!-- Image URL -->
            <div>
              <label for="image_url" class="block text-xs font-medium text-[var(--color-text-secondary)] mb-1.5">
                URL Gambar Produk
              </label>
              <input
                id="image_url"
                v-model="form.image_url"
                type="url"
                placeholder="https://images.unsplash.com/..."
                :class="[
                  'w-full h-11 px-3.5 bg-[var(--color-bg)] border rounded-[var(--radius-control)] text-sm text-[var(--color-text)] placeholder:text-[var(--color-text-secondary)] focus:outline-none transition-colors',
                  errors.image_url ? 'border-[var(--color-danger)]' : 'border-[var(--color-border-strong)] focus:border-[var(--color-primary)]',
                ]"
                :aria-invalid="!!errors.image_url"
                :aria-describedby="errors.image_url ? 'image-error' : undefined"
              />
              <p v-if="errors.image_url" id="image-error" class="mt-1 text-xs text-[var(--color-danger)]">{{ errors.image_url[0] }}</p>
            </div>

            <!-- Description -->
            <div>
              <label for="description" class="block text-xs font-medium text-[var(--color-text-secondary)] mb-1.5">
                Deskripsi Produk
              </label>
              <textarea
                id="description"
                v-model="form.description"
                rows="4"
                placeholder="Deskripsi singkat tanaman, cara perawatan, atau spesifikasi..."
                class="w-full p-3.5 bg-[var(--color-bg)] border border-[var(--color-border-strong)] focus:border-[var(--color-primary)] rounded-[var(--radius-control)] text-sm text-[var(--color-text)] placeholder:text-[var(--color-text-secondary)] focus:outline-none transition-colors leading-relaxed"
                :aria-invalid="!!errors.description"
                :aria-describedby="errors.description ? 'description-error' : undefined"
              ></textarea>
              <p v-if="errors.description" id="description-error" class="mt-1 text-xs text-[var(--color-danger)]">{{ errors.description[0] }}</p>
            </div>

            <!-- Actions -->
            <div class="pt-4 border-t border-[var(--color-surface-raised)] flex items-center justify-end gap-3">
              <router-link
                to="/admin/products"
                class="h-11 px-5 border border-[var(--color-border-strong)] text-[var(--color-text)] hover:text-[var(--color-primary)] hover:border-[var(--color-primary)] rounded-[var(--radius-control)] text-sm font-medium flex items-center transition-colors"
              >
                Batal
              </router-link>
              <button
                type="submit"
                :disabled="submitting"
                class="h-11 px-6 bg-[var(--color-primary)] text-[var(--color-bg)] hover:bg-[var(--color-accent-hover)] active:bg-[var(--color-accent)] font-semibold rounded-[var(--radius-control)] text-sm transition-colors flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50"
              >
                <span>{{ submitting ? 'Menyimpan…' : (isEdit ? 'Simpan Perubahan' : 'Tambah Produk') }}</span>
              </button>
            </div>
          </form>
        </section>

        <!-- Preview Section (5 cols) -->
        <aside class="lg:col-span-5 bg-[var(--color-surface)] border border-[var(--color-surface-raised)] rounded-[var(--radius-card)] p-6 sticky top-8">
          <h2 class="text-sm font-semibold text-[var(--color-text-secondary)] mb-4">
            Pratinjau Gambar
          </h2>
          <div class="w-full aspect-square bg-[var(--color-bg)] border border-[var(--color-surface-raised)] rounded-[var(--radius-media)] overflow-hidden flex items-center justify-center">
            <img
              v-if="form.image_url"
              :src="form.image_url"
              :alt="form.name || 'Pratinjau Gambar'"
              class="w-full h-full object-cover"
              @error="previewError = true"
              @load="previewError = false"
            />
            <div v-else class="text-center p-6 text-[var(--color-text-secondary)]">
              <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <p class="text-xs">Masukkan URL gambar untuk melihat pratinjau</p>
            </div>
          </div>
          <div v-if="form.name" class="mt-4">
            <div class="text-sm font-semibold text-[var(--color-text)] truncate">{{ form.name }}</div>
            <div class="text-sm font-bold text-[var(--color-primary)] tabular-nums mt-0.5">
              {{ formatRupiah(form.price || 0) }}
            </div>
          </div>
        </aside>
      </div>
    </main>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { adminApi } from '@/api/admin'
import { catalogApi } from '@/api/catalog'
import { formatRupiah } from '@/utils/format'
import { useToastStore } from '@/stores/toast'
import AdminLayout from '@/components/AdminLayout.vue'

const route = useRoute()
const router = useRouter()
const toastStore = useToastStore()

const isEdit = computed(() => !!route.params.id)
const productId = computed(() => route.params.id)

const categories = ref([])
const submitting = ref(false)
const previewError = ref(false)
const errors = ref({})

const form = reactive({
  name: '',
  category_id: '',
  price: null,
  stock: null,
  image_url: '',
  description: '',
})

async function fetchCategories() {
  try {
    const res = await catalogApi.getCategories()
    categories.value = res.data || []
  } catch (err) {
    console.error('Gagal mengambil kategori:', err)
  }
}

async function loadProduct() {
  if (!isEdit.value) return

  try {
    const res = await adminApi.getProduct(productId.value)
    const p = res.data
    form.name = p.name
    form.category_id = p.category?.id || p.category_id
    form.price = p.price
    form.stock = p.stock
    form.image_url = p.image_url || ''
    form.description = p.description || ''
  } catch (err) {
    toastStore.show('Gagal memuat data produk untuk diedit.', 'error')
    router.push('/admin/products')
  }
}

async function handleSubmit() {
  submitting.value = true
  errors.value = {}

  const payload = {
    name: form.name?.trim(),
    category_id: form.category_id,
    price: form.price,
    stock: form.stock,
    image_url: form.image_url?.trim() || null,
    description: form.description?.trim() || null,
  }

  try {
    if (isEdit.value) {
      await adminApi.updateProduct(productId.value, payload)
      toastStore.show('Produk berhasil diperbarui.', 'success')
    } else {
      await adminApi.createProduct(payload)
      toastStore.show('Produk baru berhasil ditambahkan.', 'success')
    }
    router.push('/admin/products')
  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data?.errors || {}
      toastStore.show('Mohon periksa kolom formulir yang belum valid.', 'error')
    } else {
      toastStore.show(err.response?.data?.message || 'Gagal menyimpan produk.', 'error')
    }
  } finally {
    submitting.value = false
  }
}

onMounted(() => {
  fetchCategories()
  loadProduct()
})
</script>
