export default defineEventHandler(async (event) => {
    const body = await readBody(event)
    return $fetch('http://localhost/api/admin/blog/posts', {
        method: 'POST',
        body
    })
})