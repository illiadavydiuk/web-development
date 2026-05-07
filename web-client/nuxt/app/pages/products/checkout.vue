<script setup lang="ts">
import { vMaska } from 'maska/vue'

useSeoMeta({
  title: 'Checkout',
  description: ''
})

const subscriptionStore = useSubscriptionStore()

if (!subscriptionStore.isSelected) {
  navigateTo('/products')
}

const plan = computed(() => subscriptionStore.selectedPlan)
const isAnnual = computed(() => subscriptionStore.billing === 'annual')
const displayTotal = computed(() => subscriptionStore.displayTotal)

const date = new Date()
const day = date.getDate()
const month = date.getMonth() + 1
const year = date.getFullYear()

const state = reactive({
  planId: plan.value?.id,
  billing: subscriptionStore.billing,
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
  subscriptionStore.clearPlan()
  navigateTo('/products')
}
</script>

<template>

  <div v-if="subscriptionStore.isSelected" class="flex justify-center w-full">
    <div class="min-w-[540px] w-full max-w-[900px]">
      <div class="mt-8 mb-4">
        <NuxtLink to="/products" class="text-sm text-slate-400"> &lt;&lt; back</NuxtLink>
        <h1 class="text-2xl text-slate-700 font-bold mt-3">You're Almost In - Start Your 3-Day Free Trial Now!</h1>
        <p class="text-base text-slate-500 mt-2">Set up your account to gain instant access! You won't be charged
          if you decide to cancel within 3 days.</p>
      </div>
      <div class="flex">
        <div class="h-fit">
          <ProductCard v-if="plan" :plan="plan" :billing-period="subscriptionStore.billing" hide-button />
        </div>

        <div class="min-w-[400px] w-[55%] h-full ml-10 my-6 p-8 rounded-2xl border-2 border-slate-100 shadow-sm
         text-slate-700 text-sm">
          <h3 class="text-md font-bold mb-6">Order Summary</h3>

          <div class="flex justify-between mb-2">
            <p>{{ isAnnual ? 'Annual' : 'Monthly' }} Plan</p>
            <p>${{ displayTotal }}</p>
          </div>

          <hr class="border-slate-100 mb-2"/>

          <div class="flex justify-between mb-2">
            <p>Total Due <span class="text-[10px] font-baseline text-slate-400">(*not including sales tax where applicable)</span></p>
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
            <p class="text-sm text-gray-500">Card Details</p>
            <div class="flex justify-between w-full bg-gray-50 border-gray-300 border rounded p-1 mt-1 mb-2">
              <UInput name="cardNumber" type="text" variant="none" icon="lucide:credit-card"
                      placeholder="Number" v-maska="'#### #### #### ####'" v-model="state.cardNumber"
                      class="w-[50%]"/>
              <UInput name="expirationDate" type="text" variant="none"
                      placeholder="MM / YY" v-maska="'##/##'" v-model="state.expirationDate"
                      class="w-[25%]"/>
              <UInput name="verificationCode" type="text" variant="none"
                      placeholder="CVC" v-maska="'###'" v-model="state.verificationCode"
                      class="w-[15%]"/>
            </div>

            <p class="text-sm text-slate-400">Address</p>
            <div class="w-full bg-neutral-50 border-slate-200 border rounded-lg p-3 mt-1 mb-2">
              <div class="mb-2">
                <label for="fullName" class="text-sm text-slate-400">Full Name</label><br>
                <div class="border-gray-300 border rounded bg-white mt-1">
                  <UInput id="fullName" name="fullName" type="text" variant="none" v-model="state.fullName" class="w-full"/>
                </div>
              </div>
              <div>
                <label for="address" class="text-sm text-slate-400">Address</label><br>
                <div class="border border-slate-200 rounded-lg bg-white mt-1">
                  <UInput id="address" name="address" type="text" variant="none" v-model="state.address" class="w-full"/>
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
                billing period, which can be done by calling (888) 463-3163.</p>
            </div>

            <UButton
              type="submit"
              :disabled="!state.isConsent"
              class="disabled:bg-slate-100 disabled:text-slate-400
                enabled:bg-gradient-to-r enabled:from-lime-400 enabled:to-emerald-400 enabled:text-black
                enabled:hover:from-green-400 enabled:hover:to-cyan-400 duration-200
                font-semibold py-2 px-5 rounded-lg">
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
