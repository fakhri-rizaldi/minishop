<script setup>
import { useRoute } from 'vue-router'
import AppHeader from '@/components/AppHeader.vue'
import Toast from '@/components/Toast.vue'
import FloatingCartButton from '@/components/FloatingCartButton.vue'

const route = useRoute()
</script>

<template>
  <div class="min-h-screen bg-[var(--color-bg)] text-[var(--color-text)] flex flex-col">
    <AppHeader v-if="!route.path.startsWith('/admin')" />
    <div :class="{ 'pt-16': !route.path.startsWith('/admin') && route.path !== '/' }">
      <router-view />
    </div>
    <Toast />
    <FloatingCartButton />
  </div>
</template>
