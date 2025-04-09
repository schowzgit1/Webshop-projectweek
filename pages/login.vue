<!--
    ============================================
    Author: Apothecare Team
    Description: Login page for Apothecare
    ============================================
-->
<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          Inloggen
        </h2>
      </div>
      <form class="mt-8 space-y-6" @submit.prevent="handleLogin">
        <div class="rounded-md shadow-sm -space-y-px">
          <div>
            <label for="username" class="sr-only">Gebruikersnaam</label>
            <input
              id="username"
              v-model="username"
              name="username"
              type="text"
              required
              class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
              placeholder="Gebruikersnaam"
            />
          </div>
          <div>
            <label for="password" class="sr-only">Wachtwoord</label>
            <input
              id="password"
              v-model="password"
              name="password"
              type="password"
              required
              class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
              placeholder="Wachtwoord"
            />
          </div>
        </div>

        <div v-if="error" class="text-red-500 text-sm text-center">
          {{ error }}
        </div>

        <div>
          <button
            type="submit"
            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Inloggen
          </button>
        </div>
      </form>
      <div class="text-center">
        <nuxt-link
          to="/register"
          class="font-medium text-indigo-600 hover:text-indigo-500"
        >
          Nog geen account? Registreer hier
        </nuxt-link>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      username: '',
      password: '',
      error: '',
    }
  },
  methods: {
    async handleLogin() {
      try {
        // Debug log
        console.log('Attempting login with:', { username: this.username, password: this.password })

        const response = await fetch('http://localhost:7860/v1/chat/completions', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer vHrsX9bWGF3AW7fR0hblwIQzSekJQHa1'
          },
          body: JSON.stringify({
            model: "mistral-tiny",
            messages: [
              {
                role: "user",
                content: `Verifieer de login voor gebruiker ${this.username} met wachtwoord ${this.password}`
              }
            ],
            temperature: 0.7,
            max_tokens: 150
          })
        })

        // Debug log
        console.log('Response status:', response.status)
        console.log('Response headers:', response.headers)

        const data = await response.json()
        console.log('Response data:', data)

        if (response.ok) {
          // Simuleer een succesvolle login voor demo doeleinden
          const user = {
            id: 1,
            username: this.username,
            role: 'user'
          }
          
          // Store user data in localStorage
          localStorage.setItem('user', JSON.stringify(user))
          // Update the app state
          window.dispatchEvent(new Event('storage'))
          // Redirect to landing page
          this.$router.push('/landingpage')
        } else {
          this.error = 'Inloggen mislukt. Controleer je gegevens.'
        }
      } catch (error) {
        console.error('Login error:', error)
        this.error = 'Er is een probleem met de verbinding. Controleer of de server draait.'
      }
    },
  },
}
</script> 