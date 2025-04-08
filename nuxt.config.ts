// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2024-11-01',
  devtools: { enabled: true },
  modules: ['nuxt-windicss'],
  
  // Configure Nitro server
  nitro: {
    // Configure dev server to proxy PHP requests to a separate PHP server
    devProxy: {
      '/server': {
        target: 'http://localhost:8000/server',
        changeOrigin: true,
        prependPath: false
      }
    }
  },
  
  // Runtime config
  runtimeConfig: {
    public: {
      apiBase: '/server'
    }
  }
})