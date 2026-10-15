// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2026-10-15',
  devtools: { enabled: true },

  css: ['~/assets/css/main.css'],

  modules: ['@nuxt/image'],

  vite: {
    plugins: [(await import('@tailwindcss/vite')).default()],
  },

  runtimeConfig: {
    public: {
      apiBase: 'http://localhost:8080/api',
    },
  },

  app: {
    head: {
      title: 'Arkea Muebles | Catálogo - Andahuaylas',
      meta: [
        { charset: 'utf-8' },
        { name: 'viewport', content: 'width=device-width, initial-scale=1' },
        {
          name: 'description',
          content:
            'Catálogo digital de muebles Arkea Muebles en Andahuaylas. Sofás, comedores, dormitorios y oficina con los mejores precios.',
        },
      ],
      link: [{ rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' }],
    },
  },
})
