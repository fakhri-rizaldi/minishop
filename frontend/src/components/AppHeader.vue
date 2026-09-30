<template>
  <header
    :class="[
      'fixed top-0 left-0 right-0 z-50 transition-all duration-300 ease-in-out pointer-events-none',
      isTransparent && !isMenuOpen
        ? 'bg-transparent py-5'
        : 'navbar-scrolled py-3.5 shadow-lg'
    ]"
  >
    <!-- Container Bar with Burger Button -->
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-end relative">
      <!-- Burger Toggle Button (pointer-events-auto) -->
      <button
        type="button"
        @click="toggleMenu"
        class="pointer-events-auto relative z-50 w-11 h-11 flex flex-col items-center justify-center gap-1.5 rounded-xl border border-[var(--color-surface-raised)] bg-[var(--color-surface)]/70 hover:bg-[var(--color-surface-raised)] hover:border-[var(--color-border-strong)] transition-all cursor-pointer p-2 focus:outline-none focus-visible:ring-2 focus-visible:ring-[var(--color-primary)] backdrop-blur-md shadow-sm"
        :aria-expanded="isMenuOpen"
        aria-label="Menu Navigasi"
      >
        <span
          :class="[
            'w-5 h-0.5 bg-[var(--color-primary)] rounded-full transition-all duration-300 transform',
            isMenuOpen ? 'rotate-45 translate-y-2' : ''
          ]"
        ></span>
        <span
          :class="[
            'w-5 h-0.5 bg-[var(--color-primary)] rounded-full transition-all duration-300',
            isMenuOpen ? 'opacity-0 scale-0' : 'opacity-100'
          ]"
        ></span>
        <span
          :class="[
            'w-5 h-0.5 bg-[var(--color-primary)] rounded-full transition-all duration-300 transform',
            isMenuOpen ? '-rotate-45 -translate-y-2' : ''
          ]"
        ></span>
      </button>
    </div>

    <!-- Kotak Hitam Blur Slide Down Memenuhi Seluruh Layar (Teleport ke Body) -->
    <Teleport to="body">
      <Transition name="slide-down">
        <div
          v-if="isMenuOpen"
          class="fixed inset-0 z-50 w-screen h-screen black-slide-panel flex flex-col justify-center"
          @click.self="closeMenu"
        >
          <!-- Top Bar inside Overlay (Close Button) -->
          <div class="absolute top-5 left-0 right-0 max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 flex justify-end items-center">
            <button
              type="button"
              @click="closeMenu"
              class="w-11 h-11 flex items-center justify-center rounded-xl border border-[var(--color-surface-raised)] bg-[var(--color-surface)]/80 hover:bg-[var(--color-surface-raised)] transition-all cursor-pointer p-2 focus:outline-none focus-visible:ring-2 focus-visible:ring-[var(--color-primary)] text-[var(--color-primary)] shadow-sm"
              aria-label="Tutup Menu"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Content Container (Left-aligned, centered vertically) -->
          <div class="max-w-[1200px] mx-auto w-full px-8 sm:px-16 md:px-24 lg:px-32 flex flex-col items-start text-left">
            <nav class="flex flex-col items-start gap-8 md:gap-12 text-left" aria-label="Menu Utama">
              <!-- Katalog Link -->
              <a
                href="#katalog"
                @click.prevent="handleCatalogClick"
                class="nav-link-underline font-display text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-normal text-[var(--color-text)] hover:text-[var(--color-primary)] transition-colors cursor-pointer select-none"
              >
                Katalog
              </a>

              <!-- Login / Admin Link -->
              <router-link
                v-if="authStore.isAuthenticated"
                to="/admin/products"
                @click="closeMenu"
                class="nav-link-underline font-display text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-normal text-[var(--color-text)] hover:text-[var(--color-primary)] transition-colors cursor-pointer select-none"
              >
                Admin Panel
              </router-link>
              <router-link
                v-else
                to="/admin/login"
                @click="closeMenu"
                class="nav-link-underline font-display text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-normal text-[var(--color-text)] hover:text-[var(--color-primary)] transition-colors cursor-pointer select-none"
              >
                Login
              </router-link>
            </nav>
          </div>
        </div>
      </Transition>
    </Teleport>
  </header>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const scrollY = ref(0)
const isMenuOpen = ref(false)

function onScroll() {
  scrollY.value = window.scrollY || document.documentElement.scrollTop || 0
}

function toggleMenu() {
  isMenuOpen.value = !isMenuOpen.value
}

function closeMenu() {
  isMenuOpen.value = false
}

function handleKeyDown(e) {
  if (e.key === 'Escape' && isMenuOpen.value) {
    closeMenu()
  }
}

// Lock/unlock body scroll when menu is open
watch(isMenuOpen, (open) => {
  if (open) {
    document.body.style.overflow = 'hidden'
  } else {
    document.body.style.overflow = ''
  }
})

// Close menu upon route change
watch(() => route.fullPath, () => {
  closeMenu()
})

onMounted(() => {
  window.addEventListener('scroll', onScroll, { passive: true })
  window.addEventListener('keydown', handleKeyDown)
  onScroll()
})

onUnmounted(() => {
  window.removeEventListener('scroll', onScroll)
  window.removeEventListener('keydown', handleKeyDown)
  document.body.style.overflow = ''
})

// Navbar is transparent only when on home route ('/'), within hero (< 100px), and menu closed
const isTransparent = computed(() => {
  if (route.path !== '/') return false
  return scrollY.value < 100
})

function handleCatalogClick() {
  closeMenu()
  if (route.path === '/') {
    const el = document.getElementById('katalog')
    if (el) {
      el.scrollIntoView({ behavior: 'smooth' })
    }
  } else {
    router.push({ path: '/' }).then(() => {
      setTimeout(() => {
        const el = document.getElementById('katalog')
        if (el) {
          el.scrollIntoView({ behavior: 'smooth' })
        }
      }, 150)
    })
  }
}
</script>

<style scoped>
.navbar-scrolled {
  background-color: rgba(11, 19, 15, 0.85);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
  border-bottom: 1px solid var(--color-surface-raised);
}

/* Kotak Hitam Opacity Kecil + Blur Memenuhi Section */
.black-slide-panel {
  background-color: rgba(0, 0, 0, 0.58);
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
}

/* Animated Underline Effect on Hover */
.nav-link-underline {
  position: relative;
  display: inline-block;
  padding-bottom: 6px;
  transition: color 250ms cubic-bezier(0.16, 1, 0.3, 1);
}

.nav-link-underline::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 2.5px;
  background-color: var(--color-primary);
  transform: scaleX(0);
  transform-origin: left;
  transition: transform 300ms cubic-bezier(0.16, 1, 0.3, 1);
}

.nav-link-underline:hover::after {
  transform: scaleX(1);
}

/* Slide Down Transition */
.slide-down-enter-active,
.slide-down-leave-active {
  transition: transform 350ms cubic-bezier(0.16, 1, 0.3, 1),
              opacity 350ms cubic-bezier(0.16, 1, 0.3, 1);
}

.slide-down-enter-from,
.slide-down-leave-to {
  transform: translateY(-100%);
  opacity: 0;
}

.slide-down-enter-to,
.slide-down-leave-from {
  transform: translateY(0);
  opacity: 1;
}
</style>
