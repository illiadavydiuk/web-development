export default defineEventHandler(async (event) => {
  const body = await readBody(event)
  console.log('New subscription:', body)
  return { success: true }
})
