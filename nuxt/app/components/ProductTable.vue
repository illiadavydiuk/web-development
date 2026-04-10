<script setup lang="ts">
import { getPaginationRowModel } from '@tanstack/vue-table'
import type { TableColumn } from '@nuxt/ui'
import type { Product } from '~/types/product'

const props = defineProps<{
  products: Product[] | null
  loading?: boolean
}>()

const table = useTemplateRef('table')

const globalFilter = ref('')
const sorting = ref([{ id: 'title', desc: false }])
const pagination = ref({ pageIndex: 0, pageSize: 10 })

watch(globalFilter, () => {
  table.value?.tableApi?.setPageIndex(0)
})

const total = computed(
  () => table.value?.tableApi?.getFilteredRowModel().rows.length || props.products?.length || 0
)

const columns: TableColumn<Product>[] = [
  { id: 'selection', header: ' ' },
  {
    accessorKey: 'title',
    header: ({ column }) => {
      const isSorted = column.getIsSorted()
      return h('div', {
        class: 'flex items-center gap-1 cursor-pointer hover:text-blue-600 transition-colors uppercase text-[11px] font-semibold text-gray-500',
        onClick: () => column.toggleSorting(isSorted === 'asc')
      }, [
        h('span', { class: 'whitespace-nowrap' }, 'Title/Brand'),
        isSorted ? h('span', {}, isSorted === 'asc' ? ' ↑' : ' ↓') : null
      ])
    }
  },
  {
    accessorKey: 'description',
    header: 'Description'
  },
  {
    accessorKey: 'price',
    header: ({ column }) => {
      const isSorted = column.getIsSorted()
      return h('div', {
        class: 'flex items-center gap-1 cursor-pointer hover:text-blue-600 transition-colors uppercase text-[11px] font-semibold text-gray-500',
        onClick: () => column.toggleSorting(isSorted === 'asc')
      }, [
        h('span', { class: 'whitespace-nowrap' }, 'Price'),
        isSorted ? h('span', {}, isSorted === 'asc' ? ' ↑' : ' ↓') : null
      ])
    }
  },
  {
    accessorKey: 'rating',
    header: ({ column }) => {
      const isSorted = column.getIsSorted()
      return h('div', {
        class: 'flex items-center gap-1 cursor-pointer hover:text-blue-600 transition-colors uppercase text-[11px] font-semibold text-gray-500',
        onClick: () => column.toggleSorting(isSorted === 'asc')
      }, [
        h('span', { class: 'whitespace-nowrap' }, 'Rating'),
        isSorted ? h('span', {}, isSorted === 'asc' ? ' ↑' : ' ↓') : null
      ])
    }
  },
  {
    accessorKey: 'category',
    header: ({ column }) => {
      const isSorted = column.getIsSorted()
      return h('div', {
        class: 'flex items-center gap-1 cursor-pointer hover:text-blue-600 transition-colors uppercase text-[11px] font-semibold text-gray-500',
        onClick: () => column.toggleSorting(isSorted === 'asc')
      }, [
        h('span', { class: 'whitespace-nowrap' }, 'Category'),
        isSorted ? h('span', {}, isSorted === 'asc' ? ' ↑' : ' ↓') : null
      ])
    }
  }
]

const hoveredImg = ref('')
const mousePos = ref({ x: 0, y: 0 })

function showImg(e: MouseEvent, url: string) {
  hoveredImg.value = url
  mousePos.value = { x: e.clientX + 20, y: e.clientY - 100 }
}

function hideImg() {
  hoveredImg.value = ''
}
</script>

<template>
  <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
    <div class="flex justify-between items-center mb-6 px-2">
      <div class="flex items-center gap-4">
        <UCheckbox :model-value="false" />
        <UInput
          v-model="globalFilter"
          icon="i-lucide-search"
          placeholder="Search products..."
          variant="none"
          class="w-80 border-b border-gray-200"
        />
      </div>
    </div>

    <div class="table-wrapper border border-gray-200 rounded-xl overflow-x-auto">
      <UTable
        ref="table"
        :data="products ?? []"
        :columns="columns"
        :loading="loading"
        v-model:global-filter="globalFilter"
        v-model:sorting="sorting"
        v-model:pagination="pagination"
        :pagination-options="{ getPaginationRowModel: getPaginationRowModel() }"
        :ui="{ td: 'whitespace-normal' }"
        class="w-full table-fixed min-w-[1200px] min-h-[570px]"
      >
        <template #selection-cell>
          <div class="pl-4"><UCheckbox color="primary" /></div>
        </template>

        <template #title-cell="{ row }">
          <div class="flex items-start justify-between gap-4 py-3 pr-6 min-w-0">
            <div class="min-w-0 flex-1">
              <div class="text-gray-900 font-bold underline underline-offset-4 decoration-gray-300 text-[13px] break-words">
                {{ row.original.title }}
              </div>
              <div class="text-gray-400 text-[10px] uppercase mt-1">
                {{ row.original.brand }}
              </div>
            </div>
            <UIcon
              name="i-lucide-image"
              class="text-gray-400 w-5 h-5 flex-shrink-0 cursor-pointer hover:text-blue-500 transition-colors mt-0.5"
              @mouseenter="(e: any) => showImg(e, row.original.thumbnail)"
              @mouseleave="hideImg"
            />
          </div>
        </template>

        <template #description-cell="{ row }">
          <p class="text-gray-500 text-[12px] line-clamp-2 px-6 leading-relaxed break-words">
            {{ row.original.description }}
          </p>
        </template>

        <template #price-cell="{ row }">
          <span class="font-extrabold text-sm text-gray-900 px-2">
            ${{ row.original.price }}
          </span>
        </template>

        <template #rating-cell="{ row }">
          <span
            :class="row.original.rating < 4.5 ? 'text-red-500' : 'text-green-600'"
            class="font-bold px-2 text-[13px]"
          >
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

      <!-- Пагінація — як у друга, через tableApi напряму -->
      <div class="p-4 border-t border-gray-100 flex justify-center bg-gray-50/30">
        <UPagination
          :page="(table?.tableApi?.getState().pagination.pageIndex || 0) + 1"
          :items-per-page="table?.tableApi?.getState().pagination.pageSize || pagination.pageSize"
          :total="total"
          @update:page="(p) => table?.tableApi?.setPageIndex(p - 1)"
        />
      </div>
    </div>

    <div
      v-if="hoveredImg"
      class="fixed z-[9999] pointer-events-none p-1 bg-white border border-gray-200 shadow-2xl rounded-xl"
      :style="{ left: mousePos.x + 'px', top: mousePos.y + 'px' }"
    >
      <img :src="hoveredImg" class="w-[100px] h-[100px] object-contain rounded-lg" alt="preview" />
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
