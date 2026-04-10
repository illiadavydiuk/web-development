<script setup lang="ts">
import type { Plan } from '~/types/plan'
import { vMaska } from 'maska/vue'

useSeoMeta({
  title: 'Checkout',
  description: ''
})

const route = useRoute()
const planId = route.query['plan']?.toString()
const billing = route.query['billing']?.toString() as 'annual' | 'monthly' ?? 'annual'

const { data: plans, status } = await useLazyFetch<Plan[]>('/api/plans')

const plan = computed<Plan | undefined>(() => {
  if (status.value === 'success' && plans.value && planId !== undefined)
    return plans.value[parseInt(planId)]
})

const isAnnual = billing === 'annual'

const displayPrice = computed(() =>
  isAnnual ? plan.value?.priceMonthly : plan.value?.priceMonthlyFull
)

const displayTotal = computed(() => {
  if (!plan.value) return 0
  return isAnnual
    ? plan.value.priceYearlyDiscounted
    : plan.value.priceMonthlyFull
})

const date = new Date()
const day = date.getDate()
const month = date.getMonth() + 1
const year = date.getFullYear()

const state = reactive({
  planId,
  billing,
  cardNumber: undefined as string | undefined,
  expirationDate: undefined as string | undefined,
  verificationCode: undefined as string | undefined,
  fullName: undefined as string | undefined,
  address: undefined as string | undefined,
  isConsent: false
})

async function handleSubmit() {
  await $fetch('/api/subscription/create', {
    method: 'POST',
    body: state
  })
  state.cardNumber = undefined
  state.expirationDate = undefined
  state.verificationCode = undefined
  state.fullName = undefined
  state.address = undefined
  state.isConsent = false
}
</script>

<template>
  <div v-if="planId && plans" class="flex justify-center w-full">
    <div class="min-w-[540px] w-full max-w-[900px]">

      <div class="mt-8 mb-4">
        <NuxtLink to="/products" class="text-sm text-slate-400">&lt;&lt; back</NuxtLink>
        <h1 class="text-2xl text-slate-700 font-bold mt-3">
          You're Almost In - Start Your 3-Day Free Trial Now!
        </h1>
        <p class="text-base text-slate-500 mt-2">
          Set up your account to gain instant access! You won't be charged if you decide to cancel within 3 days.
        </p>
      </div>

      <div class="flex">
        <ProductCard v-if="plan" :plan="plan" :billing-period="billing" hide-button />

        <div class="min-w-[400px] w-[55%] h-full ml-10 my-6 p-8 rounded-2xl border-2 border-slate-100 shadow-sm
         text-slate-700 text-sm">
          <h3 class="text-md font-bold mb-6">Order Summary</h3>

          <div class="flex justify-between mb-2">
            <p>{{ isAnnual ? 'Annual' : 'Monthly' }} Plan</p>
            <p>${{ displayTotal }}</p>
          </div>
          <hr class="border-slate-100 mb-2"/>
          <div class="flex justify-between mb-2">
            <p>Total Due <span class="text-[10px] text-slate-400">(*not including sales tax where applicable)</span></p>
            <p>${{ displayTotal }}</p>
          </div>
          <div class="flex justify-between mb-6 font-semibold">
            <p>Due Today</p>
            <p>$0.00</p>
          </div>
          <div class="bg-slate-50 rounded-lg text-slate-500 font-semibold text-center py-3 mb-7">
            Includes 3-Day Free Trial
          </div>

          <UForm :state="state" @submit="handleSubmit">
            <div class="flex items-center gap-1 mb-4">
              <h3 class="text-md font-bold">Billing Information</h3>
              <UIcon name="lucide:info" class="text-slate-300"/>
            </div>

            <p class="text-sm text-slate-400 mb-1">Card Details</p>
            <div class="flex justify-between w-full bg-neutral-50 border border-slate-200 rounded-lg p-1 mb-2">
              <UInput
                v-maska="'#### #### #### ####'"
                v-model="state.cardNumber"
                name="cardNumber"
                type="text"
                variant="none"
                icon="lucide:credit-card"
                placeholder="Number"
                class="w-[50%]"
              />
              <UInput
                v-maska="'##/##'"
                v-model="state.expirationDate"
                name="expirationDate"
                type="text"
                variant="none"
                placeholder="MM / YY"
                class="w-[25%]"
              />
              <UInput
                v-maska="'###'"
                v-model="state.verificationCode"
                name="verificationCode"
                type="text"
                variant="none"
                placeholder="CVC"
                class="w-[15%]"
              />
            </div>

            <p class="text-sm text-slate-400 mb-1">Address</p>
            <div class="w-full bg-neutral-50 border border-slate-200 rounded-lg p-3 mb-2">
              <div class="mb-2">
                <label for="fullName" class="text-sm text-slate-400">Full Name</label>
                <div class="border border-slate-200 rounded-lg bg-white mt-1">
                  <UInput id="fullName" v-model="state.fullName" name="fullName" type="text"
                          variant="none" class="w-full"/>
                </div>
              </div>
              <div>
                <label for="address" class="text-sm text-slate-400">Address</label>
                <div class="border border-slate-200 rounded-lg bg-white mt-1">
                  <UInput id="address" v-model="state.address" name="address" type="text"
                          variant="none" class="w-full"/>
                </div>
              </div>
            </div>

            <div class="flex gap-2 mb-4">
              <UCheckbox v-model="state.isConsent" required name="isConsent"/>
              <p class="text-xs text-slate-500">
                I consent to <a class="font-bold underline cursor-pointer">Terms of Use</a>
                and understand my 3-day free trial will automatically convert to
                ${{ displayTotal }} per {{ isAnnual ? 'year' : 'month' }} starting on
                {{ day }}/{{ month }}/{{ year }}. The yearly fee will be automatically charged each year going
                forward unless I cancel my account at least one (1) business day before the end of current
                billing period, which can be done by calling (888) 463-3163.
              </p>
            </div>

            <UButton
              type="submit"
              :disabled="!state.isConsent"
              class="disabled:bg-slate-100 disabled:text-slate-400
                     enabled:bg-gradient-to-r enabled:from-lime-400 enabled:to-emerald-400
                     enabled:text-black enabled:hover:from-green-400 enabled:hover:to-cyan-400
                     transition-all duration-200 font-semibold py-2 px-5 rounded-lg"
            >
              Try It Free
            </UButton>
          </UForm>
        </div>
      </div>
    </div>
  </div>

  <div v-else class="flex justify-center p-8">
    <h1 class="text-xl text-slate-400">Couldn't find the product</h1>
  </div>
</template>
