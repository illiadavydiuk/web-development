<script setup lang="ts">
import type { Product } from '~/types/product'

const { data, status } = await useLazyFetch<{ products: Product[] }>('/api/products')

useSeoMeta({
  title: 'Таблиця',
  description: ''
})
</script>

<template>
  <div class="p-8 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto">
      <h2 class="text-2xl font-bold mb-6 text-gray-800">Управління продуктами</h2>

      <ProductTable
        :products="data?.products ?? []"
        :loading="status === 'pending'"
      />
    </div>
  </div>
</template>
