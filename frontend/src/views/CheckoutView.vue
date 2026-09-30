<template>
  <main class="min-h-screen py-8 px-4 max-w-[1200px] mx-auto">
    <!-- Header -->
    <header class="mb-8">
      <nav class="flex items-center gap-2 text-sm text-[var(--color-text-secondary)] mb-4" aria-label="Breadcrumb">
        <router-link to="/cart" class="hover:text-[var(--color-primary)] transition-colors">
          Keranjang
        </router-link>
        <span class="opacity-50">›</span>
        <span class="text-[var(--color-text)] font-medium">Checkout</span>
      </nav>

      <h1 class="text-3xl md:text-4xl font-normal font-display text-[var(--color-primary)] tracking-tight">
        Checkout
      </h1>
      <p class="text-sm text-[var(--color-text-secondary)] mt-1">
        Lengkapi data penerima untuk menyelesaikan pesanan Anda.
      </p>
    </header>

    <!-- Layout: Form (Left) + Order Summary (Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
      <!-- Form Section (8 Cols) -->
      <section class="lg:col-span-7 bg-[var(--color-surface)] border border-[var(--color-surface-raised)] rounded-[var(--radius-card)] p-6 md:p-8">
        <h2 class="text-xl font-normal font-display text-[var(--color-text)] mb-6">
          Informasi Pemesan
        </h2>

        <form @submit.prevent="handleSubmit" novalidate class="space-y-6">
          <!-- General Alert Message (if any) -->
          <div
            v-if="generalError"
            class="p-4 rounded-[var(--radius-control)] bg-[var(--color-danger)]/10 border border-[var(--color-danger)]/30 text-[var(--color-danger)] text-sm flex items-start gap-3"
            role="alert"
          >
            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>{{ generalError }}</div>
          </div>

          <!-- Customer Name -->
          <div>
            <label for="customer_name" class="block text-sm font-medium text-[var(--color-text-secondary)] mb-2">
              Nama Lengkap <span class="text-[var(--color-danger)]">*</span>
            </label>
            <input
              id="customer_name"
              v-model="form.customer_name"
              type="text"
              autocomplete="name"
              required
              placeholder="Contoh: Budi Santoso"
              :class="[
                'w-full h-11 px-3.5 bg-[var(--color-bg)] border rounded-[var(--radius-control)] text-sm text-[var(--color-text)] placeholder:text-[var(--color-text-secondary)] focus:outline-none transition-colors',
                (fieldErrors['customer.name'] || fieldErrors.customer_name)
                  ? 'border-[var(--color-danger)] focus:border-[var(--color-danger)]'
                  : 'border-[var(--color-border-strong)] focus:border-[var(--color-primary)]',
              ]"
              :aria-invalid="!!(fieldErrors['customer.name'] || fieldErrors.customer_name)"
              :aria-describedby="(fieldErrors['customer.name'] || fieldErrors.customer_name) ? 'name-error' : undefined"
            />
            <p
              v-if="fieldErrors['customer.name'] || fieldErrors.customer_name"
              id="name-error"
              class="mt-1.5 text-xs text-[var(--color-danger)] flex items-center gap-1"
            >
              <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
              <span>{{ (fieldErrors['customer.name'] || fieldErrors.customer_name)[0] }}</span>
            </p>
          </div>

          <!-- Customer Email -->
          <div>
            <label for="customer_email" class="block text-sm font-medium text-[var(--color-text-secondary)] mb-2">
              Alamat Email <span class="text-[var(--color-danger)]">*</span>
            </label>
            <input
              id="customer_email"
              v-model="form.customer_email"
              type="email"
              autocomplete="email"
              required
              placeholder="Contoh: budi@example.com"
              :class="[
                'w-full h-11 px-3.5 bg-[var(--color-bg)] border rounded-[var(--radius-control)] text-sm text-[var(--color-text)] placeholder:text-[var(--color-text-secondary)] focus:outline-none transition-colors',
                (fieldErrors['customer.email'] || fieldErrors.customer_email)
                  ? 'border-[var(--color-danger)] focus:border-[var(--color-danger)]'
                  : 'border-[var(--color-border-strong)] focus:border-[var(--color-primary)]',
              ]"
              :aria-invalid="!!(fieldErrors['customer.email'] || fieldErrors.customer_email)"
              :aria-describedby="(fieldErrors['customer.email'] || fieldErrors.customer_email) ? 'email-error' : undefined"
            />
            <p
              v-if="fieldErrors['customer.email'] || fieldErrors.customer_email"
              id="email-error"
              class="mt-1.5 text-xs text-[var(--color-danger)] flex items-center gap-1"
            >
              <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
              <span>{{ (fieldErrors['customer.email'] || fieldErrors.customer_email)[0] }}</span>
            </p>
          </div>

          <!-- Submit Button (REQ-CO-03) -->
          <div class="pt-4 border-t border-[var(--color-surface-raised)]">
            <button
              type="submit"
              :disabled="submitting || cartStore.isEmpty"
              class="w-full h-12 px-6 bg-[var(--color-primary)] text-[var(--color-bg)] hover:bg-[var(--color-accent-hover)] active:bg-[var(--color-accent)] font-semibold rounded-[var(--radius-control)] transition-colors flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <svg
                v-if="submitting"
                class="w-5 h-5 animate-spin text-[var(--color-bg)]"
                fill="none"
                viewBox="0 0 24 24"
              >
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ submitting ? 'Memproses pesanan…' : 'Buat Pesanan' }}</span>
            </button>
            <p class="text-xs text-[var(--color-text-secondary)] text-center mt-3">
              Dengan membuat pesanan, stok produk akan langsung dikunci untuk Anda.
            </p>
          </div>
        </form>
      </section>

      <!-- Order Summary (5 Cols) -->
      <aside class="lg:col-span-5 bg-[var(--color-surface)] border border-[var(--color-surface-raised)] rounded-[var(--radius-card)] p-6 sticky top-24">
        <h2 class="text-xl font-normal font-display text-[var(--color-text)] mb-4">
          Ringkasan Pesanan ({{ cartStore.totalItems }})
        </h2>

        <!-- Items list (Read Only) -->
        <div class="space-y-3 max-h-[360px] overflow-y-auto pr-1 divide-y divide-[var(--color-surface-raised)]">
          <div
            v-for="item in cartStore.items"
            :key="item.id"
            class="pt-3 first:pt-0 flex items-center justify-between gap-3"
          >
            <div class="flex items-center gap-3 min-w-0">
              <div class="w-12 h-12 shrink-0 bg-[var(--color-surface-raised)] rounded-[var(--radius-media)] overflow-hidden">
                <img
                  v-if="item.image_url"
                  :src="item.image_url"
                  :alt="item.name"
                  class="w-full h-full object-cover"
                />
              </div>
              <div class="min-w-0">
                <div class="text-sm font-medium text-[var(--color-text)] truncate">
                  {{ item.name }}
                </div>
                <div class="text-xs text-[var(--color-text-secondary)] tabular-nums">
                  {{ item.quantity }} × {{ formatRupiah(item.price) }}
                </div>
              </div>
            </div>
            <div class="text-sm font-bold text-[var(--color-text)] tabular-nums text-right shrink-0">
              {{ formatRupiah(item.price * item.quantity) }}
            </div>
          </div>
        </div>

        <!-- Total Breakdown -->
        <div class="mt-6 pt-4 border-t border-[var(--color-surface-raised)] space-y-2 text-sm">
          <div class="flex justify-between text-[var(--color-text-secondary)]">
            <span>Subtotal</span>
            <span class="tabular-nums font-medium text-[var(--color-text)]">{{ formatRupiah(cartStore.totalPrice) }}</span>
          </div>
          <div class="flex justify-between text-[var(--color-text-secondary)]">
            <span>Pengiriman</span>
            <span class="text-[var(--color-accent)] font-medium">Gratis</span>
          </div>
          <div class="flex justify-between items-baseline pt-3 border-t border-[var(--color-surface-raised)]">
            <span class="text-base font-semibold text-[var(--color-text)]">Total Pembayaran</span>
            <span class="text-2xl font-bold font-sans text-[var(--color-primary)] tabular-nums">
              {{ formatRupiah(cartStore.totalPrice) }}
            </span>
          </div>
        </div>
      </aside>
    </div>
  </main>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { ordersApi } from '@/api/orders'
import { useCartStore } from '@/stores/cart'
import { useToastStore } from '@/stores/toast'
import { formatRupiah } from '@/utils/format'

const router = useRouter()
const cartStore = useCartStore()
const toastStore = useToastStore()

const form = reactive({
  customer_name: '',
  customer_email: '',
})

const submitting = ref(false)
const fieldErrors = ref({})
const generalError = ref(null)

onMounted(() => {
  // Guard empty cart (REQ-CO-02)
  if (cartStore.isEmpty) {
    toastStore.show('Keranjang Anda masih kosong. Silakan pilih produk terlebih dahulu.', 'error')
    router.replace('/cart')
  }
})

async function handleSubmit() {
  if (cartStore.isEmpty) {
    router.replace('/cart')
    return
  }

  submitting.value = true
  fieldErrors.value = {}
  generalError.value = null

  const payload = {
    customer: {
      name: form.customer_name.trim(),
      email: form.customer_email.trim(),
    },
    items: cartStore.items.map((item) => ({
      product_id: item.id || item.product_id,
      quantity: item.quantity,
    })),
  }

  try {
    const res = await ordersApi.createOrder(payload)
    const orderData = res.data

    // Order Success (REQ-CO-10)
    cartStore.clearCart()
    toastStore.show('Pesanan Anda berhasil dibuat!', 'success')
    router.push(`/orders/${orderData.order_number}/success`)
  } catch (err) {
    const status = err.response?.status
    const data = err.response?.data

    if (status === 409) {
      // 409 Insufficient Stock Conflict (REQ-CO-11)
      const conflictItems = data?.items || []
      const changes = cartStore.applyStockConflicts(conflictItems)

      toastStore.show(
        'Sebagian stok produk tidak mencukupi atau telah berubah. Keranjang Anda telah disesuaikan.',
        'error'
      )

      // Redirect back to cart to let customer review changes
      router.push('/cart')
    } else if (status === 422) {
      // 422 Validation Error (REQ-CO-04)
      fieldErrors.value = data?.errors || {}
      generalError.value = data?.message || 'Data formulir tidak valid. Silakan periksa kembali kolom isian.'
      toastStore.show('Mohon perbaiki data formulir yang belum sesuai.', 'error')
    } else {
      generalError.value = data?.message || 'Terjadi kesalahan sistem saat memproses pesanan. Silakan coba beberapa saat lagi.'
      toastStore.show('Gagal memproses pesanan.', 'error')
    }
  } finally {
    submitting.value = false
  }
}
</script>
