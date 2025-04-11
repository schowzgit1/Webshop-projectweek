<!--
    ============================================
    Author: Apothecare Team
    Description: Registration page for Apothecare
    ============================================
-->
<template>
  <div class="register-page">
    <div class="register-container">
      <div class="register-header">
        <h2>Registreer bij ApotheCare</h2>
        <p>Maak een nieuw account aan om gebruik te maken van onze diensten</p>
      </div>
      <form class="register-form" @submit.prevent="handleRegister">
        <div class="form-group">
          <label for="name">Volledige naam</label>
          <input
            id="name"
            v-model="name"
            name="name"
            type="text"
            required
            class="form-input"
            :class="{ 'input-error': nameError }"
            placeholder="Vul je volledige naam in"
            @blur="validateName"
          />
          <p v-if="nameError" class="error-message">{{ nameError }}</p>
        </div>
        
        <div class="form-group">
          <label for="email">Email</label>
          <input
            id="email"
            v-model="email"
            name="email"
            type="email"
            required
            class="form-input"
            :class="{ 'input-error': emailError }"
            placeholder="Vul je email adres in"
            @blur="validateEmail"
          />
          <p v-if="emailError" class="error-message">{{ emailError }}</p>
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
            placeholder="Kies een wachtwoord"
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
            <span v-if="isSubmitting">Registreren...</span>
            <span v-else>Registreren</span>
          </button>
        </div>
      </form>
      <div class="register-footer">
        <NuxtLink
          to="/login"
          class="link-login"
        >
          Al een account? Log hier in
        </NuxtLink>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { validateUsername as validateUsernameField, validatePassword as validatePasswordField, getValidationErrors } from '~/utils/validation'

const name = ref('')
const email = ref('')
const password = ref('')
const error = ref('')
const nameError = ref('')
const emailError = ref('')
const passwordError = ref('')
const isSubmitting = ref(false)

const validateName = () => {
  nameError.value = ''
  
  if (!name.value) {
    nameError.value = 'Naam is verplicht'
    return false
  } else if (name.value.length < 2) {
    nameError.value = 'Naam moet minimaal 2 karakters bevatten'
    return false
  }
  
  return true
}

const validateEmail = () => {
  emailError.value = ''
  
  if (!email.value) {
    emailError.value = 'Email is verplicht'
    return false
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
    emailError.value = 'Vul een geldig email adres in'
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
  
  if (!validateName()) isValid = false
  if (!validateEmail()) isValid = false
  if (!validatePassword()) isValid = false
  
  return isValid
}

const handleRegister = async () => {
  if (!validateForm()) return
  
  isSubmitting.value = true
  error.value = ''
  
  try {
    const response = await fetch('http://localhost:8000/api/register.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        name: name.value,
        email: email.value,
        password: password.value,
      }),
    })

    const data = await response.json()

    if (data.success) {
      await navigateTo('/login')
    } else {
      error.value = data.error || 'Er is een fout opgetreden bij het registreren'
    }
  } catch (error) {
    console.error('Registration error:', error)
    error.value = 'Er is een probleem met de verbinding. Controleer of de server draait.'
  } finally {
    isSubmitting.value = false
  }
}
</script>

<style scoped>
.register-page {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 80vh;
  padding: 20px;
  background-color: #f8f9fa;
}

.register-container {
  width: 100%;
  max-width: 450px;
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  padding: 30px;
}

.register-header {
  text-align: center;
  margin-bottom: 30px;
}

.register-header h2 {
  font-size: 24px;
  color: #3066f6;
  margin-bottom: 10px;
}

.register-header p {
  color: #666;
  font-size: 14px;
}

.register-form {
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

.register-footer {
  text-align: center;
  margin-top: 20px;
}

.link-login {
  color: #3066f6;
  text-decoration: none;
  font-size: 14px;
  transition: color 0.3s;
}

.link-login:hover {
  color: #2050c8;
  text-decoration: underline;
}
</style> 