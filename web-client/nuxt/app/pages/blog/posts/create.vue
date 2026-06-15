<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { z } from 'zod'

const isSubmitPending = ref(false)
const categoriesList = ref<{ id: number; title: string }[]>([])

const formErrors = ref<Record<string, string>>({})

const formData = ref({
    title: '',
    slug: '',
    category_id: 1 as number | null,
    user_id: 1,                     
    content_raw: '',
    excerpt: '',
    is_published: 1      
})

const postSchema = z.object({
    title: z.string()
        .min(5, 'Заголовок має містити щонайменше 5 символів')
        .max(200, 'Заголовок не може перевищувати 200 символів'),
    slug: z.string().max(200, 'Slug не може перевищувати 200 символів').optional().or(z.literal('')),
    category_id: z.number({ message: 'Оберіть коректну категорію' }),
    user_id: z.number(),
    excerpt: z.string().optional().or(z.literal('')),
    content_raw: z.string()
    .min(3, 'Текст статті має містити щонайменше 3 символи')
    .max(10000, 'Текст статті занадто великий')
})

const loadCategories = async () => {
    try {
        const response = await $fetch<any>('/api/categories', { query: { per_page: 100 } })
        categoriesList.value = response?.data || []
    } catch (err: any) { 
        console.error('Не вдалося завантажити категорії:', err)
    }
}

const handleCreatePost = async () => {
    formErrors.value = {}

    const validationResult = postSchema.safeParse(formData.value)

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
        
        if (!payload.slug || !payload.slug.trim()) delete payload.slug
        if (!payload.excerpt || !payload.excerpt.trim()) delete payload.excerpt

        const response = await $fetch<any>('/api/posts', {
            method: 'POST',
            body: payload
        })

        if (response.success) {
            alert('Публікацію успішно створено!')
            navigateTo('/blog/posts/dashboard')
        } else {
            alert(response.msg || 'Помилка збереження статті')
        }
    } catch (err: any) {
        console.error('Помилка:', err)
        const serverErrors = err.response?._data?.data?.errors || err.response?._data?.errors
        if (serverErrors) {
            Object.entries(serverErrors).forEach(([field, messages]: any) => {
                formErrors.value[field] = messages[0]
            })
        } else {
            alert('Не вдалося створити публікацію.')
        }
    } finally {
        isSubmitPending.value = false
    }
}

onMounted(() => {
    loadCategories()
})
</script>

<template>
    <div class="max-w-4xl mx-auto p-6 space-y-6">
        <div class="flex items-center gap-4 border-b border-gray-100 pb-4">
        <UButton icon="i-lucide-arrow-left" color="neutral" variant="ghost" to="/blog/posts/dashboard" />
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Нова стаття</h1>
            <p class="text-xs text-gray-500">Створення нової публікації в блозі</p>
        </div>
        </div>

        <form @submit.prevent="handleCreatePost" class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Заголовок статті *</label>
                <UInput class="w-full shadow-sm" v-model="formData.title" placeholder="Введіть крутий заголовок..." :color="formErrors.title ? 'error' : 'neutral'" />
                <p v-if="formErrors.title" class="mt-1 text-xs text-red-500 font-medium">{{ formErrors.title }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Slug (Автогенерація, якщо порожньо)</label>
                <UInput v-model="formData.slug" :color="formErrors.slug ? 'error' : 'neutral'" />
                <p v-if="formErrors.slug" class="mt-1 text-xs text-red-500 font-medium">{{ formErrors.slug }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Категорія статті *</label>
                <select 
                    v-model="formData.category_id" 
                    class="w-full h-9 px-3 rounded-lg border bg-white text-sm focus:outline-none focus:ring-2"
                    :class="formErrors.category_id ? 'border-red-500 focus:ring-red-500 text-red-900' : 'border-gray-200 focus:ring-indigo-500 text-gray-900'"
                >
                    <option v-for="cat in categoriesList" :key="cat.id" :value="cat.id">
                        {{ cat.title }}
                    </option>
                </select>
                <p v-if="formErrors.category_id" class="mt-1 text-xs text-red-500 font-medium">{{ formErrors.category_id }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Статус публікації</label>
                <select 
                    v-model="formData.is_published" 
                    class="w-full h-9 px-3 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                >
                    <option :value="1">Опубліковано (Доступно всім)</option>
                    <option :value="0">Чернетка (Приховано)</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Короткий уривок (Excerpt)</label>
            <UInput class="w-full shadow-sm" v-model="formData.excerpt" placeholder="Короткий опис для прев'ю" :color="formErrors.excerpt ? 'error' : 'neutral'" />
            <p v-if="formErrors.excerpt" class="mt-1 text-xs text-red-500 font-medium">{{ formErrors.excerpt }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Текст статті *</label>
            <UTextarea class="w-full shadow-sm" v-model="formData.content_raw" placeholder="Напишіть щось дивовижне..." :rows="10" :color="formErrors.content_raw ? 'error' : 'neutral'" />
            <p v-if="formErrors.content_raw" class="mt-1 text-xs text-red-500 font-medium">{{ formErrors.content_raw }}</p>
        </div>

        <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
            <UButton label="Скасувати" color="neutral" variant="outline" to="/blog/posts/dashboard" />
            <UButton type="submit" label="Опублікувати статтю" color="primary" :loading="isSubmitPending" />
        </div>
        </form>
    </div>
</template>