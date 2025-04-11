<!--
    ============================================
    Author: Apothecare Team
    Description: Login page for Apothecare
    ============================================
-->
<template>
  <div class="login-page">
    <div class="login-container">
      <div class="login-header">
        <h2>Inloggen bij ApotheCare</h2>
        <p>Vul je gegevens in om toegang te krijgen tot je account</p>
      </div>
      <form class="login-form" @submit.prevent="handleLogin">
        <div class="form-group">
          <label for="username">Gebruikersnaam</label>
          <input
            id="username"
            v-model="username"
            name="username"
            type="text"
            required
            class="form-input"
            :class="{ 'input-error': usernameError }"
            placeholder="Vul je gebruikersnaam in"
            @blur="validateUsername"
          />
          <p v-if="usernameError" class="error-message">{{ usernameError }}</p>
        </div>
        
        <div class="form-group">
          <label for="password">Wachtwoord</label>
          <input
            id="password"
            v-model="password"
            name="password"
            type="password"
            required
            class="form-input"
            :class="{ 'input-error': passwordError }"
            placeholder="Vul je wachtwoord in"
            @blur="validatePassword"
          />
          <p v-if="passwordError" class="error-message">{{ passwordError }}</p>
        </div>

        <div v-if="error" class="alert-error">
          {{ error }}
        </div>

        <div class="form-actions">
          <button
            type="submit"
            class="btn-primary"
            :disabled="isSubmitting"
          >
            <span v-if="isSubmitting">Inloggen...</span>
            <span v-else>Inloggen</span>
          </button>
        </div>
      </form>
      <div class="login-footer">
        <NuxtLink
          to="/register"
          class="link-register"
        >
          Nog geen account? Registreer hier
        </NuxtLink>
      </div>
    </div>
  </div>
</template>

<script setup>
import { validateUsername as validateUsernameField, validatePassword as validatePasswordField, getValidationErrors } from '~/utils/validation'

const username = ref('')
const password = ref('')
const error = ref('')
const usernameError = ref('')
const passwordError = ref('')
const isSubmitting = ref(false)

const validateUsername = () => {
  usernameError.value = ''
  
  if (!username.value) {
    usernameError.value = 'Gebruikersnaam is verplicht'
    return false
  } else if (!validateUsernameField(username.value)) {
    usernameError.value = getValidationErrors('username', username.value)
    return false
  }
  
  return true
}

const validatePassword = () => {
  passwordError.value = ''
  
  if (!password.value) {
    passwordError.value = 'Wachtwoord is verplicht'
    return false
  } else if (!validatePasswordField(password.value)) {
    passwordError.value = getValidationErrors('password', password.value)
    return false
  }
  
  return true
}

const validateForm = () => {
  let isValid = true
  
  // Reset errors
  usernameError.value = ''
  passwordError.value = ''
  
  // Validate username
  if (!username.value) {
    usernameError.value = 'Gebruikersnaam is verplicht'
    isValid = false
  } else if (!validateUsernameField(username.value)) {
    usernameError.value = getValidationErrors('username', username.value)
    isValid = false
  }
  
  // Validate password
  if (!password.value) {
    passwordError.value = 'Wachtwoord is verplicht'
    isValid = false
  } else if (!validatePasswordField(password.value)) {
    passwordError.value = getValidationErrors('password', password.value)
    isValid = false
  }
  
  return isValid
}

const handleLogin = async () => {
  if (!validateForm()) return
  
  isSubmitting.value = true
  error.value = ''
  
  try {
    const response = await fetch('http://localhost:8000/api/login.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        email: username.value,
        password: password.value,
      }),
    })

    const data = await response.json()

    if (data.success) {
      if (process.client) {
        localStorage.setItem('user', JSON.stringify(data.user))
        window.dispatchEvent(new Event('storage'))
        await navigateTo('/landingpage')
      }
    } else {
      error.value = data.error || 'Inloggen mislukt. Controleer je gegevens.'
    }
  } catch (error) {
    console.error('Login error:', error)
    error.value = 'Er is een probleem met de verbinding. Controleer of de server draait.'
  } finally {
    isSubmitting.value = false
  }
}
</script>

<style scoped>
.login-page {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 80vh;
  padding: 20px;
  background-color: #f8f9fa;
}

.login-container {
  width: 100%;
  max-width: 450px;
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  padding: 30px;
}

.login-header {
  text-align: center;
  margin-bottom: 30px;
}

.login-header h2 {
  font-size: 24px;
  color: #3066f6;
  margin-bottom: 10px;
}

.login-header p {
  color: #666;
  font-size: 14px;
}

.login-form {
  margin-bottom: 20px;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 6px;
  font-weight: 500;
  color: #333;
}

.form-input {
  width: 100%;
  padding: 12px 15px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
  transition: border-color 0.3s;
}

.form-input:focus {
  border-color: #3066f6;
  outline: none;
}

.input-error {
  border-color: #e53e3e;
}

.error-message {
  color: #e53e3e;
  font-size: 12px;
  margin-top: 4px;
}

.alert-error {
  background-color: #ffe5e5;
  color: #e53e3e;
  padding: 10px 15px;
  border-radius: 4px;
  margin-bottom: 20px;
  font-size: 14px;
  text-align: center;
}

.form-actions {
  margin-top: 30px;
}

.btn-primary {
  width: 100%;
  background-color: #3066f6;
  color: white;
  border: none;
  padding: 12px 20px;
  border-radius: 4px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s;
}

.btn-primary:hover {
  background-color: #2050c8;
}

.btn-primary:disabled {
  background-color: #a0aec0;
  cursor: not-allowed;
}

.login-footer {
  text-align: center;
  margin-top: 20px;
}

.link-register {
  color: #3066f6;
  text-decoration: none;
  font-size: 14px;
  transition: color 0.3s;
}

.link-register:hover {
  color: #2050c8;
  text-decoration: underline;
}
</style> 