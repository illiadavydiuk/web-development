import { defineStore } from 'pinia'
import type { Plan } from '~/types/plan'

export const useSubscriptionStore = defineStore('subscription', () => {
  // State
  const selectedPlan = ref<Plan | null>(null)
  const billing = ref<'annual' | 'monthly'>('annual')

  // Getters
  const displayTotal = computed(() => {
    if (!selectedPlan.value) return 0
    return billing.value === 'annual'
      ? selectedPlan.value.priceYearlyDiscounted
      : selectedPlan.value.priceMonthlyFull
  })

  const isSelected = computed(() => selectedPlan.value !== null)

  // Actions
  function selectPlan(plan: Plan, billingPeriod: 'annual' | 'monthly') {
    selectedPlan.value = plan
    billing.value = billingPeriod
  }

  function clearPlan() {
    selectedPlan.value = null
    billing.value = 'annual'
  }

  return {
    selectedPlan,
    billing,
    displayTotal,
    isSelected,
    selectPlan,
    clearPlan
  }
})
