export default defineEventHandler(async (event) => {
    const id = getRouterParam(event, 'id')
    const body = await readBody(event)
    return $fetch(`http://localhost/api/admin/blog/categories/${id}`, {
        method: 'PUT',
        body
    })
})