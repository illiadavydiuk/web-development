<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { z } from 'zod'

const isSubmitPending = ref(false)
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

const loadParentOptions = async () => {
    try {
        const response = await $fetch<any>('/api/categories', { query: { per_page: 100 } })
        parentCategoriesList.value = response?.data || []
    } catch (err: any) {
        console.error('Не вдалося завантажити категорії:', err)
    }
}

const handleCreateCategory = async () => {
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

        if (!payload.slug || !payload.slug.trim()) {
            delete payload.slug
        }

        if (!payload.description || payload.description.trim().length < 3) {
            delete payload.description
        }

        const response = await $fetch<any>('/api/categories', {
            method: 'POST',
            body: payload
        })

        if (response.success) {
            alert(response.message || 'Категорію створено!')
            navigateTo('/blog/categories')
        } else {
            alert(response.message || 'Помилка збереження')
        }
    } catch (err: any) {
        console.error('Повна помилка сервера:', err)
        
        const serverErrors = err.response?._data?.data?.errors || err.response?._data?.errors
        
        if (serverErrors) {
            Object.entries(serverErrors).forEach(([field, messages]: any) => {
                formErrors.value[field] = messages[0]
            })
        } else {
            alert(`Не вдалося створити категорію. Перевірте логи контейнера.`)
        }
    } finally {
        isSubmitPending.value = false
    }
}

onMounted(() => {
    loadParentOptions()
})
</script>

<template>
    <div class="max-w-3xl mx-auto p-6 space-y-6">
        <div class="flex items-center gap-4 border-b border-gray-100 pb-4">
        <UButton icon="i-lucide-arrow-left" color="neutral" variant="ghost" to="/blog/categories" />
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Створення категорії</h1>
            <p class="text-xs text-gray-500">Додавання нового розділу для статей блогу</p>
        </div>
        </div>

        <form @submit.prevent="handleCreateCategory" class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Назва категорії *</label>
                <UInput v-model="formData.title" placeholder="Програмування" :color="formErrors.title ? 'error' : 'neutral'" />
                <p v-if="formErrors.title" class="mt-1 text-xs text-red-500 font-medium">{{ formErrors.title }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Slug (Залиште порожнім для автогенерації)</label>
                <UInput v-model="formData.slug" placeholder="" :color="formErrors.slug ? 'error' : 'neutral'" />
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
            <UTextarea v-model="formData.description" placeholder="Короткий опис розділу..." :rows="4" :color="formErrors.description ? 'error' : 'neutral'" />
            <p v-if="formErrors.description" class="mt-1 text-xs text-red-500 font-medium">{{ formErrors.description }}</p>
        </div>

        <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
            <UButton label="Скасувати" color="neutral" variant="outline" to="/blog/categories" />
            <UButton type="submit" label="Зберегти категорію" color="primary" :loading="isSubmitPending" />
        </div>
        </form>
    </div>
</template>