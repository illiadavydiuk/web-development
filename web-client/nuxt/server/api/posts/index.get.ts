export default defineEventHandler((event) => {
  return $fetch('http://localhost/api/blog/posts', { query: getQuery(event) });
})