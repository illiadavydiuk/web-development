<script setup lang="ts">
import type { Post } from '../types/blogPost'

const posts = ref<Post[]>([]);

const getPosts = () => {
    $fetch('http://localhost/api/blog/posts')
        .then((response: any) => {
            console.log(response);
            posts.value = response.data || response;
        })
        .catch(err => console.error('Помилка завантаження:', err));
};

getPosts();
</script>

<template>
  <div class="container">
    <div class="flex justify-center">
      <div class="w-full">
        <nav class="navbar bg-gray-100">
          <a href="/admin/blog/posts/create" class="">Додати</a>
        </nav>

        <div class="card">
          <div class="card-body">
            <table class="table table-auto">
              <thead>
              <tr>
                <th>#</th>
                <th>Автор</th>
                <th>Категорія</th>
                <th>Заголовок</th>
                <th>Дата публікації</th>
              </tr>
              </thead>

              <tbody>
              <tr v-for="post in posts">
                <td>{{ post.id }}</td>
                <td>{{ post.user.name }}</td>
                <td>{{ post.category.title }}</td>
                <td><a :href="'/admin/blog/posts/' + post.id + '/edit'">{{ post.title }}</a></td>
                <td>{{ post.published_at }}
                </td>
              </tr>
              </tbody>

            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

</template>

<style scoped>

</style>