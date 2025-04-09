<!--
    ============================================
    Author: Apothecare Team
    Description: Header component for Apothecare
    ============================================
-->
<template>
  <header class="bg-white shadow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <div class="flex">
          <div class="flex-shrink-0 flex items-center">
            <nuxt-link to="/" class="text-xl font-bold text-gray-900">
              Webshop
            </nuxt-link>
          </div>
        </div>
        <div class="flex items-center">
          <button
            v-if="!isLoggedIn"
            @click="$router.push('/login')"
            class="ml-4 px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Inloggen
          </button>
          <button
            v-else
            @click="handleLogout"
            class="ml-4 px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Uitloggen
          </button>
        </div>
      </div>
    </div>
  </header>
</template>

<script>
export default {
  data() {
    return {
      isLoggedIn: false,
    }
  },
  mounted() {
    // Check if user is logged in
    this.checkLoginStatus()
  },
  methods: {
    async checkLoginStatus() {
      try {
        const response = await fetch('http://localhost:8000/api/check-login.php')
        const data = await response.json()
        this.isLoggedIn = data.loggedIn
      } catch (error) {
        console.error('Error checking login status:', error)
      }
    },
    async handleLogout() {
      try {
        await fetch('http://localhost:8000/api/logout.php')
        this.isLoggedIn = false
        this.$router.push('/')
      } catch (error) {
        console.error('Error logging out:', error)
      }
    },
  },
}
</script> 