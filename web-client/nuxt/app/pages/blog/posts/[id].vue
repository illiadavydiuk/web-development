<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import type { Post } from '../../../types/blogPost'


const route = useRoute()
const postId = route.params.id


const post = ref<Post | null>(null)
const isPageLoading = ref(true)
const errorMessage = ref('')

const loadTargetPost = async () => {
    isPageLoading.value = true
    errorMessage.value = ''
    
    try {
        const response = await $fetch<{ data: Post }>(`/api/posts/${postId}`)
        
        if (response && response.data) {
        post.value = response.data
        } else {
        post.value = response as any
        }
    } catch (error: any) {
        console.error('Помилка при завантаженні поста:', error)
        errorMessage.value = 'Не вдалося завантажити статтю.'
    } finally {
        isPageLoading.value = false
    }
}

onMounted(() => {
    loadTargetPost()
})
</script>

<template>
    <div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
        
        <div class="mb-6">
        <NuxtLink 
            to="/blog/posts/dashboard" 
            class="inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition"
        >
            <span class="text-base">←</span> Назад до панелі керування
        </NuxtLink>
        </div>

        <div v-if="isPageLoading" class="p-16 text-center bg-white border border-gray-100 rounded-2xl shadow-sm">
        <div class="flex flex-col items-center gap-3">
            <div class="w-8 h-8 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
            <p class="text-sm font-medium text-gray-500 animate-pulse">Очікування відповіді...</p>
        </div>
        </div>

        <div v-else-if="errorMessage" class="p-6 text-center bg-red-50 border border-red-200 rounded-xl text-red-600">
        <p class="font-semibold">{{ errorMessage }}</p>
        </div>

        <article v-else-if="post" class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden p-6 sm:p-8 space-y-6">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-gray-100 pb-4 text-xs font-medium text-gray-400">
            <span class="self-start px-2.5 py-1 bg-indigo-50 text-indigo-600 rounded-full font-semibold">
            {{ post.category?.title || 'Без категорії' }}
            </span>
            <div class="flex items-center gap-1">
            <span>Статус:</span>
            <span :class="post.is_published ? 'text-green-600' : 'text-amber-600'">
                {{ post.is_published ? 'Опубліковано' : 'Чернетка' }}
            </span>
            <span class="mx-1">•</span>
            <span>Дата: {{ post.published_at ? new Date(post.published_at).toLocaleDateString() : '—' }}</span>
            <template v-if="post.updated_at && new Date(post.updated_at).toLocaleDateString() !== new Date(post.created_at).toLocaleDateString()">
                <span class="mx-1">•</span>
                <span class="text-amber-600 font-medium">
                    Оновлено: {{ new Date(post.updated_at).toLocaleDateString() }}
                </span>
            </template>
            </div>
        </div>

        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-gray-950 leading-tight">
            {{ post.title }}
        </h1>

        <div class="flex items-center gap-3 p-3 bg-slate-50 border border-slate-100 rounded-xl text-sm">
            <img 
            if="post.user?.profile_photo_url"
            :src="post.user?.profile_photo_url" 
            alt="Avatar" 
            class="w-8 h-8 rounded-full object-cover border bg-gray-200"
            @error="(e: any) => e.target.style.display = 'none'"
            />
            <div class="flex flex-col sm:flex-row sm:gap-2">
            <span class="font-bold text-gray-700">Автор публікації:</span>
            <span class="text-gray-600">{{ post.user?.name || 'ID ' + post.user_id }}</span>
            </div>
        </div>

        <div v-if="post.excerpt" class="p-4 bg-indigo-50/40 border-l-4 border-indigo-500 rounded-r-xl text-gray-600 text-sm italic">
            {{ post.excerpt }}
        </div>

        <div class="text-gray-800 leading-relaxed text-base pt-2">
            <div v-if="post.content_html" v-html="post.content_html" class="prose max-w-none"></div>
            <div v-else class="whitespace-pre-line">{{ post.content_raw || 'Вміст статті порожній.' }}</div>
        </div>

        </article>
    </div>
</template>