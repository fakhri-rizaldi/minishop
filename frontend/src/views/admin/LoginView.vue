<template>
  <main class="min-h-screen flex items-center justify-center p-4 bg-[var(--color-bg)]">
    <div class="w-full max-w-md bg-[var(--color-surface)] border border-[var(--color-surface-raised)] rounded-[var(--radius-card)] p-8">
      <!-- Brand & Header -->
      <div class="text-center mb-8">
        <router-link to="/" class="inline-block mb-3">
          <span class="text-2xl font-bold font-display text-[var(--color-primary)] tracking-tight">
            MiniShop
          </span>
        </router-link>
        <h1 class="text-xl font-medium font-display text-[var(--color-text)]">
          Masuk Panel Admin
        </h1>
        <p class="text-xs text-[var(--color-text-secondary)] mt-1">
          Gunakan akun admin untuk mengelola katalog dan transaksi.
        </p>
      </div>

      <!-- Alert Error Message -->
      <div
        v-if="errorMessage"
        class="mb-6 p-3.5 rounded-[var(--radius-control)] bg-[var(--color-danger)]/10 border border-[var(--color-danger)]/30 text-[var(--color-danger)] text-xs flex items-start gap-2.5"
        role="alert"
      >
        <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>{{ errorMessage }}</span>
      </div>

      <!-- Login Form -->
      <form @submit.prevent="handleLogin" class="space-y-4" novalidate>
        <!-- Email -->
        <div>
          <label for="email" class="block text-xs font-medium text-[var(--color-text-secondary)] mb-1.5">
            Email Admin
          </label>
          <input
            id="email"
            v-model="form.email"
            type="email"
            autocomplete="email"
            required
            placeholder="admin@minishop.test"
            class="w-full h-11 px-3.5 bg-[var(--color-bg)] border border-[var(--color-border-strong)] rounded-[var(--radius-control)] text-sm text-[var(--color-text)] placeholder:text-[var(--color-text-secondary)] focus:outline-none focus:border-[var(--color-primary)] transition-colors"
          />
        </div>

        <!-- Password -->
        <div>
          <label for="password" class="block text-xs font-medium text-[var(--color-text-secondary)] mb-1.5">
            Kata Sandi
          </label>
          <input
            id="password"
            v-model="form.password"
            type="password"
            autocomplete="current-password"
            required
            placeholder="••••••••"
            class="w-full h-11 px-3.5 bg-[var(--color-bg)] border border-[var(--color-border-strong)] rounded-[var(--radius-control)] text-sm text-[var(--color-text)] placeholder:text-[var(--color-text-secondary)] focus:outline-none focus:border-[var(--color-primary)] transition-colors"
          />
        </div>

        <!-- Submit Button -->
        <button
          type="submit"
          :disabled="loading"
          class="w-full h-11 px-6 bg-[var(--color-primary)] text-[var(--color-bg)] hover:bg-[var(--color-accent-hover)] active:bg-[var(--color-accent)] font-semibold rounded-[var(--radius-control)] transition-colors flex items-center justify-center gap-2 cursor-pointer mt-6 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <svg
            v-if="loading"
            class="w-4 h-4 animate-spin text-[var(--color-bg)]"
            fill="none"
            viewBox="0 0 24 24"
          >
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span>{{ loading ? 'Memverifikasi…' : 'Masuk' }}</span>
        </button>
      </form>

      <!-- Back Link -->
      <div class="mt-6 text-center">
        <router-link to="/" class="text-xs text-[var(--color-text-secondary)] hover:text-[var(--color-text)] transition-colors">
          ← Kembali ke Toko Publik
        </router-link>
      </div>
    </div>
  </main>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useToastStore } from '@/stores/toast'

const router = useRouter()
const authStore = useAuthStore()
const toastStore = useToastStore()

const form = reactive({
  email: '',
  password: '',
})

const loading = ref(false)
const errorMessage = ref(null)

async function handleLogin() {
  if (!form.email || !form.password) {
    errorMessage.value = 'Email dan kata sandi wajib diisi.'
    return
  }

  loading.value = true
  errorMessage.value = null

  try {
    await authStore.login({
      email: form.email,
      password: form.password,
    })
    toastStore.show('Berhasil masuk ke panel admin.', 'success')
    router.push('/admin/products')
  } catch (err) {
    const status = err.response?.status
    const data = err.response?.data

    if (status === 401 || status === 422) {
      errorMessage.value = data?.message || 'Email atau kata sandi tidak cocok.'
    } else if (status === 429) {
      errorMessage.value = 'Terlalu banyak percobaan masuk. Silakan tunggu 1 menit sebelum mencoba lagi.'
    } else {
      errorMessage.value = 'Terjadi kesalahan sistem saat mencoba masuk.'
    }
  } finally {
    loading.value = false
  }
}
</script>
