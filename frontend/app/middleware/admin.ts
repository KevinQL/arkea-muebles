export default defineNuxtRouteMiddleware((to) => {
  const token = useCookie('arkea_token')

  if (!token.value && to.path !== '/admin/login') {
    return navigateTo('/admin/login')
  }

  if (token.value && to.path === '/admin/login') {
    return navigateTo('/admin')
  }
})
