<script setup lang="ts">
import type { Plan } from '~/types/plan'

const props = defineProps<{
  plan: Plan
  billingPeriod?: 'annual' | 'monthly'
  hideButton?: boolean
}>()
const isAnnual = computed(() => props.billingPeriod !== 'monthly')

const displayPrice = computed(() =>
  isAnnual.value ? props.plan.priceMonthly : props.plan.priceMonthlyFull
)

const displayTotal = computed(() =>
  isAnnual.value ? props.plan.priceYearlyDiscounted : props.plan.priceMonthlyFull * 12
)

function redirectToCheckout(id: number) {
  navigateTo({
    path: '/products/checkout',
    query: {
      plan: id,
      billing: props.billingPeriod ?? 'annual'
    }
  })
}
</script>

<template>
  <div v-if="plan" class="w-full max-w-[340px] bg-white rounded-xl overflow-hidden border border-gray-200
    hover:border-gray-400 transition-colors duration-300 flex flex-col">
    <div class="h-1.5 bg-gradient-to-r from-green-400 via-cyan-400 to-blue-500"></div>
    <div class="p-8 flex flex-col flex-1">
      <h2 class="text-2xl font-bold text-gray-800 mb-6 min-h-[2rem]">{{ plan.name }} - {{ isAnnual ? 'Annual' : 'Monthly' }}</h2>

      <div class="mb-6">
        <span class="text-xs font-semibold text-gray-500 bg-gray-100 px-2 py-1 rounded">
          3-days free then:
        </span>
        <div class="mt-2 flex items-end leading-none">
          <span class="text-4xl font-extrabold text-gray-900">${{ displayPrice }}</span>
          <span class="ml-1 text-gray-400">/month</span>
        </div>
        <p class="mt-2 text-sm text-gray-400">
          <span v-if="isAnnual">
            billed yearly at
            <span class="line-through">${{ plan.priceYearly }}</span>
            <span class="font-medium text-gray-600"> ${{ plan.priceYearlyDiscounted }}</span>
          </span>
          <span v-else>
            billed monthly at
            <span class="font-medium text-gray-600">${{ plan.priceMonthlyFull }}</span>
          </span>
        </p>
        <span v-if="isAnnual" class="inline-block mt-2 text-xs font-semibold text-green-700 bg-blue-50 px-2 py-1 rounded">
          ${{ plan.savings }} in savings
        </span>
      </div>

      <div class="">
        <div v-if="!hideButton" class="mb-8">
          <button
            :disabled="!plan.isFreeTrialAvailable"
            class="w-full py-3 rounded-lg font-semibold transition-colors"
            :class="plan.isFreeTrialAvailable
              ? 'bg-gradient-to-r from-amber-300 to-orange-400 hover:from-yellow-300 hover:to-amber-400 text-black cursor-pointer'
              : 'bg-gray-300 text-gray-500 cursor-not-allowed'"
            @click="plan.isFreeTrialAvailable && redirectToCheckout(plan.id)"
          >
            {{ plan.isFreeTrialAvailable ? 'Try It Free' : 'Trial Unavailable' }}
          </button>
        </div>
      </div>

      <hr class="border-gray-100 mb-6"/>

      <ul class="space-y-3 text-[15px] text-gray-700 flex-1">
        <li
          v-for="(item, index) in plan.descriptionItems"
          :key="index"
          class="flex items-start gap-3"
        >
          <div class="flex items-center h-5 shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="text-green-400"
                 width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 1L9 9l-8 3l8 3l3 8l3-8l8-3l-8-3z"/>
            </svg>
          </div>
          <div>
            <p class="leading-5" v-html="item.mainText"></p>
            <p v-if="item.subText" class="text-sm text-gray-400 mt-0.5">{{ item.subText }}</p>
          </div>
        </li>
      </ul>
    </div>
  </div>
</template>
