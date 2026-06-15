export default defineEventHandler((event) => {
    return $fetch('http://localhost/api/admin/blog/categories', { query: getQuery(event) })
})