<script setup lang="ts">
import type { Plan } from '~/types/plan'

useSeoMeta({
  title: 'Список продуктів',
  description: 'Наші тарифні плани'
})

const { data: plans, status } = await useFetch<Plan[]>('/api/plans')
const billingPeriod = ref<'annual' | 'monthly'>('annual')

const subscriptionStore = useSubscriptionStore()
</script>

<template>
  <div class="bg-gray-100 min-h-screen p-8">
    <div class="flex items-center justify-between mb-8 max-w-[1068px] mx-auto">
      <h1 class="text-3xl font-bold text-gray-800">Start Your 3 Day Free Trial</h1>

      <div class="flex items-center gap-2">
        <span class="text-sm font-semibold text-green-600">Save up to 20%</span>
        <div class="flex border border-gray-300 rounded-lg overflow-hidden bg-white">
          <button
            class="px-4 py-2 text-sm font-semibold transition-colors"
            :class="billingPeriod === 'annual' ? 'bg-gray-800 text-white' : 'text-gray-600 hover:bg-gray-50'"
            @click="billingPeriod = 'annual'"
          >
            Annual
          </button>
          <button
            class="px-4 py-2 text-sm font-semibold transition-colors"
            :class="billingPeriod === 'monthly' ? 'bg-gray-800 text-white' : 'text-gray-600 hover:bg-gray-50'"
            @click="billingPeriod = 'monthly'"
          >
            Monthly
          </button>
        </div>
      </div>
    </div>

    <div v-if="subscriptionStore.isSelected" class="max-w-[1068px] mx-auto mb-4">
      <div class="flex items-center justify-between px-4 py-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
        <span>
          Обраний план: <strong>{{ subscriptionStore.selectedPlan?.name }}</strong>
          ({{ subscriptionStore.billing === 'annual' ? 'Annual' : 'Monthly' }})
          — ${{ subscriptionStore.displayTotal }}
        </span>
        <button class="text-red-400 hover:text-red-600 font-semibold" @click="subscriptionStore.clearPlan">
          Скасувати вибір
        </button>
      </div>
    </div>

    <div v-if="status === 'pending'" class="text-center">Завантаження...</div>

    <div v-else-if="plans?.length" class="flex flex-wrap gap-6 justify-center">
      <ProductCard
        v-for="plan in plans"
        :key="plan.id"
        :plan="plan"
        :billing-period="billingPeriod"
      />
    </div>
  </div>
</template>
