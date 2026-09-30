<template>
  <span class="font-medium text-[var(--color-primary)] tabular-nums">
    {{ formattedPrice }}
  </span>
</template>

<script setup>
import { computed } from 'vue'
import { formatRupiah } from '@/utils/format'

const props = defineProps({
  price: {
    type: [Number, String],
    required: true,
  },
})

const formattedPrice = computed(() => formatRupiah(props.price))
</script>
