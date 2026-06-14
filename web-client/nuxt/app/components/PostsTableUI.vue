<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import type { Post } from '../types/blogPost'

type ColumnConfig = { accessorKey: string; header: string; cell?: (info: any) => any }
type DropdownAction = { label: string; onSelect: () => void }

const itemsCollection = ref<Post[]>([])
const activePage = ref(1)
const itemsPerPage = ref(10)
const textSearchFilter = ref('')
const tableSortingState = ref([{ id: 'id', desc: true }])
const isFetchPending = ref(false)
const totalItemsInDatabase = ref(0)

const synchronizeBlogPosts = async () => {
  isFetchPending.value = true
  
  const queryParameters: Record<string, any> = {
    page: activePage.value,
    per_page: itemsPerPage.value
  }
  
  if (textSearchFilter.value) {
    queryParameters['search'] = textSearchFilter.value
  }

  try {
    const backendResponse = await $fetch<any>('/api/posts', { 
      query: queryParameters 
    })
    
    itemsCollection.value = backendResponse?.data || []
    totalItemsInDatabase.value = backendResponse?.total || 0
  } catch (error) {
    console.error('Помилка синхронізації:', error)
  } finally {
    isFetchPending.value = false
  }
}

const gridColumns: ColumnConfig[] = [
  { 
    accessorKey: 'id', 
    header: '#' 
  },
  { 
    accessorKey: 'user.name', 
    header: 'Автор' 
  },
  { 
    accessorKey: 'category.title', 
    header: 'Категорія' 
  },
  {
    accessorKey: 'title',
    header: 'Заголовок статті',
    cell: ({ row }: any) => {
      return h('a', {
        href: `/blog/posts/${row.getValue('id')}`,
        class: 'text-indigo-600 font-medium hover:underline'
      }, row.getValue('title'))
    }
  },
  {
    accessorKey: 'published_at',
    header: 'Дата публікації',
    cell: ({ row }: any) => {
      const dateValue = row.getValue('published_at')
      return dateValue ? new Date(dateValue).toLocaleDateString() : '—'
    }
  }
]

const customLimitOptions: DropdownAction[][] = [[
  { label: '5 записів', onSelect: () => { itemsPerPage.value = 5 } },
  { label: '10 записів', onSelect: () => { itemsPerPage.value = 10 } },
  { label: '25 записів', onSelect: () => { itemsPerPage.value = 25 } },
  { label: '50 записів', onSelect: () => { itemsPerPage.value = 50 } }
]]

watch(textSearchFilter, () => {
  activePage.value = 1
  synchronizeBlogPosts()
})

watch([itemsPerPage, activePage], () => {
  synchronizeBlogPosts()
})

onMounted(() => {
  synchronizeBlogPosts()
})
</script>

<template>
  <div class="max-w-7xl mx-auto p-6 space-y-4">
    
    <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-sm flex flex-col sm:flex-row justify-between items-center gap-4">
      <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
        
        
        <div class="flex items-center gap-2">
          <span class="text-xs text-gray-500 font-medium">Сторінка:</span>
          <UInput v-model="activePage" type="number" class="w-16 text-center" />
        </div>

        <div class="flex items-center gap-2">
          <span class="text-xs text-gray-500 font-medium">Показувати по:</span>
          <UDropdownMenu :items="customLimitOptions" class="m-1">
            <UButton 
              :label="itemsPerPage.toString()" 
              color="neutral" 
              variant="outline" 
              trailing-icon="i-lucide-chevron-down" 
              class="w-16 justify-between"
            />
          </UDropdownMenu>
          <UInput 
          v-model="textSearchFilter" 
          icon="i-lucide-search" 
          placeholder="Пошук статті за назвою..." 
          class="w-full sm:w-64" 
        />
        
        </div>
      </div>

      <a 
        href="http://localhost/admin/blog/posts/create" 
        class="w-full sm:w-auto text-center px-4 py-2 text-sm font-medium bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition"
      >
        Створити публікацію
      </a>
    </div>

    <div class="border border-gray-200 rounded-xl shadow-sm overflow-hidden bg-white">
      <UTable 
        :data="itemsCollection" 
        :columns="gridColumns"
        :loading="isFetchPending"
        v-model:sorting="tableSortingState"
        v-model:global-filter="textSearchFilter"
        :ui="{
          thead: 'bg-slate-50 text-slate-600 font-semibold border-b border-slate-200',
          tr: 'hover:bg-slate-50/50 transition-colors'
        }"
      />
    </div>

    <div class="flex justify-between items-center bg-gray-50 p-4 border border-gray-200 rounded-xl text-xs text-gray-500 font-medium">
      <p>Всього знайдено записів у базі: <span class="text-gray-800 font-bold">{{ totalItemsInDatabase }}</span></p>
      <p>Поточна сторінка: <span class="text-indigo-600 font-bold">{{ activePage }}</span></p>
    </div>

  </div>
</template>