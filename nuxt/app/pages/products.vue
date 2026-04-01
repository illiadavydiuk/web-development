<script setup lang="ts">
interface Product {
  id: number
  title: string
  description: string
  price: number
  rating: number
  brand: string
  category: string
  thumbnail: string
}

useHead({ title: 'Список продуктів' })

const { data: productsData, status } = await useFetch('/api/products')
const products = computed<Product[]>(() => productsData.value?.products ?? [])

const { data: plansData } = await useFetch('/api/plans')
const plans = computed(() => plansData.value ?? [])

const q = ref('')
const page = ref(1)
const pageCount = 10
const sortCol = ref('title')
const sortDesc = ref(false)

const columns = [
  { id: 'selection', header: ' ' },
  { id: 'title', accessorKey: 'title', header: 'Title/Brand' },
  { id: 'description', accessorKey: 'description', header: 'Description' },
  { id: 'price', accessorKey: 'price', header: 'Price' },
  { id: 'rating', accessorKey: 'rating', header: 'Rating' },
  { id: 'category', accessorKey: 'category', header: 'Category' }
]

function toggleSort(id: string) {
  if (id === 'description' || id === 'selection') return
  sortCol.value === id ? (sortDesc.value = !sortDesc.value) : (sortCol.value = id, sortDesc.value = false)
}

const filteredData = computed(() => {
  const all = products.value
  if (!q.value) return all
  const query = q.value.toLowerCase()
  return all.filter(p => p.title?.toLowerCase().includes(query) || p.description?.toLowerCase().includes(query))
})

const sortedData = computed(() => {
  const data = [...filteredData.value]
  return data.sort((a: any, b: any) => {
    const aVal = a[sortCol.value], bVal = b[sortCol.value]
    if (aVal === bVal) return 0
    const res = aVal < bVal ? -1 : 1
    return sortDesc.value ? -res : res
  })
})

const rows = computed(() => sortedData.value.slice((page.value - 1) * pageCount, page.value * pageCount))

const hoveredImg = ref('')
const mousePos = ref({ x: 0, y: 0 })
function showImg(e: MouseEvent, url: string) {
  hoveredImg.value = url
  mousePos.value = { x: e.clientX + 20, y: e.clientY - 100 }
}

watch(q, () => { page.value = 1 })
</script>

<template>
  <div class="bg-gray-100 min-h-screen p-8 text-gray-900 font-sans">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Список продуктів</h1>

    <div v-if="plans?.length" class="flex flex-wrap gap-6 justify-center mb-12">
      <ProductCard
        v-for="plan in plans"
        :key="plan.id"
        :plan="plan"
      />
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
      <div class="flex justify-between items-center mb-6 px-2">
        <div class="flex items-center gap-4">
          <UCheckbox :model-value="false" />
          <UInput
            v-model="q"
            icon="i-lucide-search"
            placeholder="Search..."
            variant="none"
            class="w-80 border-b border-gray-200"
          />
        </div>
      </div>

      <div class="table-wrapper border border-gray-200 rounded-xl overflow-x-auto">
        <UTable
          :data="rows"
          :columns="columns"
          :loading="status === 'pending'"
          :ui="{td: 'whitespace-normal'}"
          class="w-full table-fixed min-w-[1200px]"
        >
          <template v-for="col in columns" :key="col.id" #[`${col.id}-header`]>
            <div
              v-if="col.id !== 'selection' && col.id !== 'description'"
              class="flex items-center gap-1 cursor-pointer hover:text-blue-600 transition-colors uppercase
              text-[11px] font-semibold text-gray-500"
              @click="toggleSort(col.id)"
            >
              <span class="whitespace-nowrap">{{ col.header }}</span>
              <UIcon v-if="sortCol === col.id" :name="sortDesc ? 'i-lucide-arrow-down' : 'i-lucide-arrow-up'"
                     class="w-3 h-3 flex-shrink-0" />
            </div>
            <span v-else class="uppercase text-[11px] font-semibold text-gray-500">{{ col.header }}</span>
          </template>

          <template #selection-cell>
            <div class="pl-4"><UCheckbox color="primary" /></div>
          </template>

          <template #title-cell="{ row }">
            <div class="flex items-start justify-between gap-4 py-3 pr-6 min-w-0">
              <div class="min-w-0 flex-1">
                <div class="text-gray-900 font-bold underline underline-offset-4 decoration-gray-300 text-[13px]
                whitespace-normal break-words">
                  {{ row.original.title }}
                </div>

                <div class="text-gray-400 text-[10px] uppercase mt-1 whitespace-normal">
                  {{ row.original.brand }}
                </div>
              </div>

              <UIcon
                name="i-lucide-image"
                class="text-gray-400 w-5 h-5 flex-shrink-0 cursor-pointer hover:text-blue-500 transition-colors
                mt-0.5"
                @mouseenter="(e: any) => showImg(e, row.original.thumbnail)"
                @mouseleave="hoveredImg = ''"
              />
            </div>
          </template>

          <template #description-cell="{ row }">
            <p class="text-gray-500 text-[12px] line-clamp-2 px-6 leading-relaxed break-words">
              {{ row.original.description }}
            </p>
          </template>

          <template #price-cell="{ row }">
            <span class="font-extrabold text-sm text-gray-900 px-2">${{ row.original.price }}</span>
          </template>

          <template #rating-cell="{ row }">
            <span :class="row.original.rating < 4 ? 'text-red-500' : 'text-green-600'" class="font-bold px-2
            text-[13px]">
              {{ row.original.rating }}
            </span>
          </template>

          <template #category-cell="{ row }">
            <div class="px-2 flex justify-center">
              <UBadge
                variant="subtle"
                color="neutral"
                class="capitalize text-[10px] truncate max-w-full justify-center text-center"
              >
                {{ row.original.category }}
              </UBadge>
            </div>
          </template>
        </UTable>

        <div class="p-4 border-t border-gray-100 flex justify-center bg-gray-50/30">
          <UPagination v-model:page="page" :items-per-page="pageCount" :total="filteredData.length" />
        </div>
      </div>
    </div>

    <div v-if="hoveredImg" class="fixed z-[9999] pointer-events-none p-1 bg-white border border-gray-200 shadow-2xl
    rounded-xl w-52" :style="{ left: mousePos.x + 'px', top: mousePos.y + 'px' }">
      <img :src="hoveredImg" class="w-full h-auto rounded-lg" alt="preview" />
    </div>
  </div>
</template>

<style scoped>
:deep(table) {
  table-layout: fixed !important;
  width: 100% !important;
  border-collapse: collapse;
}

:deep(th:nth-child(1)), :deep(td:nth-child(1)) { width: 50px !important; min-width: 50px !important; }
:deep(th:nth-child(2)), :deep(td:nth-child(2)) { width: 250px !important; min-width: 250px !important; border-right: 1px solid #f3f4f6; }
:deep(th:nth-child(3)), :deep(td:nth-child(3)) { width: auto !important; }
:deep(th:nth-child(4)), :deep(td:nth-child(4)) { width: 100px !important; min-width: 100px !important; }
:deep(th:nth-child(5)), :deep(td:nth-child(5)) { width: 100px !important; min-width: 100px !important; }
:deep(th:nth-child(6)), :deep(td:nth-child(6)) { width: 150px !important; min-width: 150px !important; }

:deep(th) {
  background-color: #f9fafb;
  padding: 1rem;
  border-bottom: 1px solid #e5e7eb;
}

.table-wrapper {
  overflow-x: auto;
  overflow-y: visible;
}
</style>
