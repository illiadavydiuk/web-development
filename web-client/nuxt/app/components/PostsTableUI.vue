<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import { refDebounced } from '@vueuse/core'
import type { Post } from '../types/blogPost'

type ColumnConfig = { accessorKey: string; header: string; cell?: (info: any) => any }
type DropdownAction = { label: string; onSelect: () => void }

const itemsCollection = ref<Post[]>([])
const activePage = ref(1)
const itemsPerPage = ref(10)

const textSearchFilter = ref('')
const textSearchFilterDebounced = refDebounced(textSearchFilter, 500)

const tableSortingState = ref([{ id: 'id', desc: true }])
const isFetchPending = ref(false)
const totalItemsInDatabase = ref(0)

const synchronizeBlogPosts = async () => {
  isFetchPending.value = true
  
  const queryParameters: Record<string, any> = {
    page: activePage.value,
    per_page: itemsPerPage.value
  }
  
  if (textSearchFilterDebounced.value) {
    queryParameters['search'] = textSearchFilterDebounced.value
  }

  try {
    const backendResponse = await $fetch<any>('/api/posts', { 
      query: queryParameters 
    })
    
    itemsCollection.value = backendResponse?.data || []
    totalItemsInDatabase.value = backendResponse?.meta?.total || 0
  } catch (error) {
    console.error('Помилка синхронізації:', error)
  } finally {
    isFetchPending.value = false
  }
}

const gridColumns: ColumnConfig[] = [
  { accessorKey: 'id', header: '#' },
  { accessorKey: 'user.name', header: 'Автор' },
  { accessorKey: 'category.title', header: 'Категорія' },
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
    accessorKey: 'date_published',
    header: 'Дата публікації',
    cell: ({ row }: any) => {
      const dateValue = row.getValue('date_published')
      return dateValue ? new Date(dateValue).toLocaleDateString() : '—'
    }
  },
  { accessorKey: 'actions', header: 'Дії' } 
]

const customLimitOptions: DropdownAction[][] = [[
  { label: '5 записів', onSelect: () => { itemsPerPage.value = 5; activePage.value = 1 } },
  { label: '10 записів', onSelect: () => { itemsPerPage.value = 10; activePage.value = 1 } },
  { label: '25 записів', onSelect: () => { itemsPerPage.value = 25; activePage.value = 1 } },
  { label: '50 записів', onSelect: () => { itemsPerPage.value = 50; activePage.value = 1 } }
]]

const getActionItems = (row: Post) => [
  {
    label: 'Редагувати',
    icon: 'i-lucide-edit',
    to: `/blog/posts/${row.id}/edit`
  },
  {
    label: 'Видалити',
    icon: 'i-lucide-trash',
    class: 'text-red-600 hover:text-red-700 font-medium',
    onSelect: () => executeDeletePost(row.id, row.title)
  }
]

const executeDeletePost = async (id: number, title: string) => {
  if (!confirm(`Ви впевнені, що хочете видалити статтю "${title}"?`)) return

  try {
    await $fetch(`/api/posts/${id}`, { method: 'DELETE' })
    synchronizeBlogPosts()
  } catch (err: any) {
    console.error('Помилка при видаленні статті:', err)
    const serverMessage = err.response?._data?.message || 'Не вдалося видалити статтю.'
    alert(`Помилка: ${serverMessage}`)
  }
}

watch(textSearchFilterDebounced, () => {
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
    
    <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-sm flex flex-col lg:flex-row justify-between items-center gap-4">
      <div class="flex flex-wrap items-center gap-4 w-full lg:w-auto">
        
        <UInput 
          v-model="textSearchFilter" 
          icon="i-lucide-search" 
          placeholder="Пошук статті за назвою..." 
          class="w-full sm:w-64" 
        />

        <div class="flex items-center gap-2">
          <span class="text-xs text-gray-500 font-medium">Сторінки:</span>
          <UPagination
            :total="totalItemsInDatabase"
            :page="activePage"
            :items-per-page="itemsPerPage"
            @update:page="(page) => { activePage = page }"
          />
        </div>

        <div class="flex items-center gap-2">
          <span class="text-xs text-gray-500 font-medium">Показувати по:</span>
          <UDropdownMenu :items="customLimitOptions">
            <UButton 
              :label="itemsPerPage.toString()" 
              color="neutral" 
              variant="outline" 
              trailing-icon="i-lucide-chevron-down" 
              class="w-20 justify-between"
            />
          </UDropdownMenu>
        </div>
      </div>

      <NuxtLink 
        to="/blog/posts/create" 
        class="w-full lg:w-auto text-center px-4 py-2 text-sm font-medium bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition shadow-sm"
      >
        Створити публікацію
      </NuxtLink>
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
      >
        <template #actions-cell="{ row }">
          <UDropdownMenu :items="getActionItems(row.original)">
            <UButton 
              color="neutral" 
              variant="ghost" 
              icon="i-lucide-ellipsis-vertical" 
              aria-label="Оберіть дію"
            />
          </UDropdownMenu>
        </template>
      </UTable>
    </div>

    <div class="flex justify-between items-center bg-gray-50 p-4 border border-gray-200 rounded-xl text-xs text-gray-500 font-medium">
      <p>
        {{ !textSearchFilter ? 'Всього знайдено записів у базі:' : 'Знайдено за фільтром:' }} 
        <span class="text-gray-800 font-bold">{{ totalItemsInDatabase }}</span>
      </p>
      <p>Поточна сторінка: <span class="text-indigo-600 font-bold">{{ activePage }}</span></p>
    </div>

  </div>
</template>