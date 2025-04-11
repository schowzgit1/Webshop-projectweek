// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  devtools: { enabled: true },
  ssr: true,

  app: {
    head: {
      title: 'ApotheCare',
      meta: [
        { charset: 'utf-8' },
        { name: 'viewport', content: 'width=device-width, initial-scale=1' },
      ]
    },
  },

  modules: [],

  runtimeConfig: {
    public: {
      apiBase: 'http://localhost:8000/api'
    }
  },

  build: {
    transpile: []
  },

  nitro: {
    preset: 'node-server',
    // Exclude problematic files
    externals: {
      // List of modules to exclude from bundling
      external: ['server/api/js/validation.js']
    }
  },

  compatibilityDate: '2025-04-11'
})