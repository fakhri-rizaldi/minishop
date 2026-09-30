<template>
  <TransitionGroup
    name="catalog-grid"
    tag="div"
    class="catalog-grid-wrapper grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6"
  >
    <ProductCard
      v-for="product in products"
      :key="product.id"
      :product="product"
      @add-to-cart="$emit('add-to-cart', $event)"
    />
  </TransitionGroup>
</template>

<script setup>
import ProductCard from '@/components/ProductCard.vue'

defineProps({
  products: {
    type: Array,
    required: true,
  },
})

defineEmits(['add-to-cart'])
</script>
