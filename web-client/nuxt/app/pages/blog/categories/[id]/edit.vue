<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { z } from 'zod'

const route = useRoute()
const categoryId = route.params.id

const isSubmitPending = ref(false)
const isDataLoading = ref(true)
const parentCategoriesList = ref<{ id: number; title: string }[]>([])

const formErrors = ref<Record<string, string>>({})

const formData = ref({
    title: '',
    slug: '',
    parent_id: null as number | null,
    description: ''
})

const categorySchema = z.object({
    title: z.string()
        .min(5, 'Назва категорії має містити щонайменше 5 символів')
        .max(200, 'Назва не може перевищувати 200 символів'),
    slug: z.string().max(200, 'Slug не може перевищувати 200 символів').optional().or(z.literal('')),
    parent_id: z.number().nullable(),
    description: z.string()
        .max(500, 'Опис не може перевищувати 500 символів')
        .refine(val => val === '' || val.trim().length >= 3, {
        message: 'Опис має містити щонайменше 3 символи, якщо він заповнений'
        }).optional().or(z.literal(''))
})

const loadCategoryData = async () => {
    isDataLoading.value = true
    try {
        const response = await $fetch<any>(`/api/categories/${categoryId}`)
        
        const category = response?.data || response
        
        formData.value = {
            title: category?.title || '',
            slug: category?.slug || '',
            parent_id: category?.parent_id || null,
            description: category?.description || ''
        }
    } catch (err: any) {
        console.error('Помилка завантаження даних категорії:', err)
        alert('Не вдалося завантажити дані категорії.')
    } finally {
        isDataLoading.value = false
    }
}

const loadParentOptions = async () => {
    try {
        const response = await $fetch<any>('/api/categories', { query: { per_page: 100 } })
        
        parentCategoriesList.value = (response?.data || []).filter((cat: any) => cat.id !== Number(categoryId))
    } catch (err: any) {
        console.error('Не вдалося завантажити опції батьків:', err)
    }
}

const handleUpdateCategory = async () => {
    formErrors.value = {}

    const validationResult = categorySchema.safeParse(formData.value)

    if (!validationResult.success) {
        validationResult.error.issues.forEach(issue => {
            const fieldName = issue.path[0] as string
            formErrors.value[fieldName] = issue.message
        })
        return 
    }

    isSubmitPending.value = true
    try {
        const payload: Record<string, any> = { ...formData.value }

        if (payload.parent_id === null || payload.parent_id === '') {
            payload.parent_id = 1 
        } else {
            payload.parent_id = Number(payload.parent_id)
        }

        if (!payload.description || payload.description.trim().length < 3) {
            delete payload.description
        }
        if (!payload.slug || !payload.slug.trim()) {
            delete payload.slug
        }

        const response = await $fetch<any>(`/api/categories/${categoryId}`, {
            method: 'PUT',
            body: payload
        })

        if (response.success) {
            alert(response.message || 'Оновлено успішно!')
            navigateTo('/blog/categories') 
        } else {
            alert(response.message || 'Помилка при оновленні')
        }
    } catch (err: any) {
        console.error('Помилка сервера при оновленні:', err)
        const serverErrors = err.response?._data?.data?.errors || err.response?._data?.errors
        
        if (serverErrors) {
            Object.entries(serverErrors).forEach(([field, messages]: any) => {
                formErrors.value[field] = messages[0]
            })
        } else {
            const serverMessage = err.response?._data?.data?.message || err.response?._data?.message || 'Помилка оновлення'
            alert(`Помилка: ${serverMessage}`)
        }
    } finally {
        isSubmitPending.value = false
    }
}

onMounted(async () => {
    await Promise.all([loadCategoryData(), loadParentOptions()])
})
</script>

<template>
    <div class="max-w-3xl mx-auto p-6 space-y-6">
        <div class="flex items-center gap-4 border-b border-gray-100 pb-4">
        <UButton icon="i-lucide-arrow-left" color="neutral" variant="ghost" to="/blog/categories" />
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Редагування категорії</h1>
            <p class="text-xs text-gray-500">Зміна параметрів категорії ID: {{ categoryId }}</p>
        </div>
        </div>

        <div v-if="isDataLoading" class="flex flex-col items-center justify-center py-12 space-y-2">
        <span class="text-sm text-gray-500 font-medium">Завантаження даних з Laravel...</span>
        </div>

        <form v-else @submit.prevent="handleUpdateCategory" class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Назва категорії *</label>
                <UInput v-model="formData.title" :color="formErrors.title ? 'error' : 'neutral'" />
                <p v-if="formErrors.title" class="mt-1 text-xs text-red-500 font-medium">{{ formErrors.title }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Slug (URL-псевдонім)</label>
                <UInput v-model="formData.slug" :color="formErrors.slug ? 'error' : 'neutral'" />
                <p v-if="formErrors.slug" class="mt-1 text-xs text-red-500 font-medium">{{ formErrors.slug }}</p>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Батьківська категорія</label>
            <select 
                v-model="formData.parent_id" 
                class="w-full h-9 px-3 rounded-lg border text-sm focus:outline-none focus:ring-2"
                :class="formErrors.parent_id ? 'border-red-500 focus:ring-red-500 text-red-900' : 'border-gray-200 focus:ring-indigo-500 text-gray-900'"
            >
                <option :value="null">-- Немає батьківської категорії (Корнева) --</option>
                <option v-for="cat in parentCategoriesList" :key="cat.id" :value="cat.id">
                    {{ cat.title }}
                </option>
            </select>
            <p v-if="formErrors.parent_id" class="mt-1 text-xs text-red-500 font-medium">{{ formErrors.parent_id }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Опис категорії</label>
            <UTextarea v-model="formData.description" :rows="4" :color="formErrors.description ? 'error' : 'neutral'" />
            <p v-if="formErrors.description" class="mt-1 text-xs text-red-500 font-medium">{{ formErrors.description }}</p>
        </div>

        <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
            <UButton label="Скасувати" color="neutral" variant="outline" to="/blog/categories" />
            <UButton type="submit" label="Зберегти зміни" color="primary" :loading="isSubmitPending" />
        </div>
        </form>
    </div>
</template>