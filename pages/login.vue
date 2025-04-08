<template>
  <div class="login-container">
    <h1>Inloggen</h1>
    
    <div v-if="loading" class="loading-message">
      Bezig met inloggen...
    </div>
    
    <div v-else-if="error" class="error-message">
      {{ error }}
    </div>
    
    <form v-else @submit.prevent="handleLogin" class="login-form">
      <div class="form-group">
        <label for="username">Gebruikersnaam</label>
        <input 
          type="text" 
          id="username" 
          v-model="username" 
          required
          placeholder="Voer je gebruikersnaam in"
        />
      </div>
      
      <div class="form-group">
        <label for="password">Wachtwoord</label>
        <input 
          type="password" 
          id="password" 
          v-model="password" 
          required
          placeholder="Voer je wachtwoord in"
        />
      </div>
      
      <button type="submit" class="login-button">Inloggen</button>
    </form>
    
    <div class="register-link">
      Nog geen account? <NuxtLink to="/register">Registreer hier</NuxtLink>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const username = ref('')
const password = ref('')
const error = ref('')
const loading = ref(false)
const router = useRouter()

const handleLogin = async () => {
  try {
    loading.value = true
    error.value = ''

    const response = await fetch('/server/api/auth/login.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        username: username.value,
        password: password.value
      })
    })

    const data = await response.json()

    if (data.success) {
      localStorage.setItem('user', JSON.stringify(data.user))
      window.dispatchEvent(new Event('storage'))
      router.push('/landingpage')
    } else {
      error.value = data.message || 'Inloggen mislukt'
      loading.value = false
    }
  } catch (err) {
    error.value = 'Er is een probleem met de verbinding'
    loading.value = false
  }
}
</script>

<style scoped>
.login-container {
  max-width: 500px;
  margin: 0 auto;
  padding: 2rem;
  background-color: #f8f9fa;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

h1 {
  text-align: center;
  color: #333;
  margin-bottom: 1.5rem;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

label {
  font-weight: 600;
  color: #444;
}

input {
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
}

.login-button {
  margin-top: 1rem;
  padding: 0.75rem;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  cursor: pointer;
  background-color: #4CAF50;
}

.login-button:hover {
  background-color: #3e8e41;
}

.error-message {
  color: #d32f2f;
  background-color: #ffebee;
  padding: 0.75rem;
  border-radius: 4px;
  margin-bottom: 1rem;
}

.loading-message {
  text-align: center;
  color: #555;
  padding: 1rem;
}

.register-link {
  margin-top: 1.5rem;
  text-align: center;
  color: #666;
}

.register-link a {
  color: #2196F3;
  text-decoration: none;
}

.register-link a:hover {
  text-decoration: underline;
}
</style> 