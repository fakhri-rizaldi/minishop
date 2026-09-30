<template>
  <div class="min-h-screen bg-[var(--color-bg)] flex flex-col md:flex-row">
    <!-- Admin Sidebar / Header -->
    <aside class="w-full md:w-60 bg-[var(--color-surface)] border-b md:border-b-0 md:border-r border-[var(--color-surface-raised)] shrink-0 flex flex-col justify-between">
      <div>
        <!-- Brand Header -->
        <div class="h-16 px-6 flex items-center justify-between border-b border-[var(--color-surface-raised)]">
          <router-link to="/admin/products" class="flex items-center gap-2">
            <span class="text-xl font-bold font-display text-[var(--color-primary)] tracking-tight">
              MiniShop
            </span>
            <span class="text-xs px-2 py-0.5 rounded-[var(--radius-pill)] bg-[var(--color-surface-raised)] text-[var(--color-text-secondary)] font-sans font-medium">
              Admin
            </span>
          </router-link>
        </div>

        <!-- Navigation Links -->
        <nav class="p-4 space-y-1" aria-label="Menu Admin">
          <router-link
            to="/admin/products"
            :class="[
              'flex items-center gap-3 px-3.5 py-2.5 rounded-[var(--radius-control)] text-sm font-medium transition-colors',
              $route.path.startsWith('/admin/products')
                ? 'bg-[var(--color-surface-raised)] text-[var(--color-primary)] font-semibold'
                : 'text-[var(--color-text)] hover:bg-[var(--color-surface-raised)]/50 hover:text-[var(--color-primary)]',
            ]"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            <span>Produk</span>
          </router-link>

          <router-link
            to="/admin/orders"
            :class="[
              'flex items-center gap-3 px-3.5 py-2.5 rounded-[var(--radius-control)] text-sm font-medium transition-colors',
              $route.path.startsWith('/admin/orders')
                ? 'bg-[var(--color-surface-raised)] text-[var(--color-primary)] font-semibold'
                : 'text-[var(--color-text)] hover:bg-[var(--color-surface-raised)]/50 hover:text-[var(--color-primary)]',
            ]"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <span>Pesanan</span>
          </router-link>
        </nav>
      </div>

      <!-- Bottom User Section & Logout -->
      <div class="p-4 border-t border-[var(--color-surface-raised)] space-y-2">
        <router-link
          to="/"
          class="flex items-center gap-2 px-3 py-2 text-xs text-[var(--color-text-secondary)] hover:text-[var(--color-text)] transition-colors"
        >
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          <span>Kembali ke Toko</span>
        </router-link>

        <button
          type="button"
          class="w-full flex items-center gap-2 px-3 py-2 text-xs text-[var(--color-danger)] hover:bg-[var(--color-danger)]/10 rounded-[var(--radius-control)] transition-colors cursor-pointer"
          @click="handleLogout"
        >
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
          <span>Keluar</span>
        </button>
      </div>
    </aside>

    <!-- Main Admin Content Area -->
    <div class="flex-1 min-w-0 flex flex-col">
      <slot />
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useToastStore } from '@/stores/toast'

const router = useRouter()
const authStore = useAuthStore()
const toastStore = useToastStore()

async function handleLogout() {
  await authStore.logout()
  toastStore.show('Anda telah keluar dari akun admin.', 'success')
  router.push('/admin/login')
}
</script>
