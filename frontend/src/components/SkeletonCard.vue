<template>
  <div class="bg-[var(--color-surface)] border border-[var(--color-surface-raised)] rounded-[var(--radius-card)] p-4 flex flex-col">
    <!-- Image Skeleton -->
    <div class="w-full aspect-square bg-[var(--color-surface-raised)] rounded-[var(--radius-media)] mb-4 animate-pulse"></div>
    
    <!-- Category & Badge Skeleton -->
    <div class="flex items-center justify-between mb-2">
      <div class="h-3 w-20 bg-[var(--color-surface-raised)] rounded animate-pulse"></div>
      <div class="h-4 w-14 bg-[var(--color-surface-raised)] rounded-[var(--radius-pill)] animate-pulse"></div>
    </div>

    <!-- Title Skeleton -->
    <div class="h-5 w-3/4 bg-[var(--color-surface-raised)] rounded mb-2 animate-pulse"></div>
    <div class="h-3 w-full bg-[var(--color-surface-raised)] rounded mb-4 animate-pulse"></div>

    <!-- Price & Button Skeleton -->
    <div class="mt-auto pt-3 border-t border-[var(--color-surface-raised)] flex items-center justify-between">
      <div class="h-5 w-24 bg-[var(--color-surface-raised)] rounded animate-pulse"></div>
      <div class="h-8 w-24 bg-[var(--color-surface-raised)] rounded-[var(--radius-control)] animate-pulse"></div>
    </div>
  </div>
</template>

<script setup>
</script>
