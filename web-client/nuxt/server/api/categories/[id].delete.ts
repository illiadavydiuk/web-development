export default defineEventHandler(async (event) => {
    const id = getRouterParam(event, 'id')

    return $fetch(`http://localhost/api/admin/blog/categories/${id}`, {
        method: 'DELETE'
    })
})