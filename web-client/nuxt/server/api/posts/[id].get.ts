export default defineEventHandler(async (event) => {
    const id = getRouterParam(event, 'id')
    
    return await $fetch(`http://127.0.0.1/api/blog/posts/${id}`)
})