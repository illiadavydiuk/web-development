export default defineEventHandler(async () => {
  const data = await $fetch('https://dummyjson.com/products?limit=100')
  return data
})
