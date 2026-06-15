export default defineEventHandler((event) => {
    const id = getRouterParam(event, 'id')
    return $fetch(`http://localhost/api/admin/blog/categories/${id}`)
})