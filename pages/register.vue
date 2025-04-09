<template>
  <div class="register-container">
    <div class="register-form">
      <h2>Registreren</h2>
      <div v-if="error" class="error-message">
        {{ error }}
      </div>
      <div v-if="success" class="success-message">
        {{ success }}
      </div>
      <form @submit.prevent="handleRegister">
        <div class="form-group">
          <label for="username">Gebruikersnaam</label>
          <input 
            type="text" 
            id="username" 
            v-model="username" 
            required 
            placeholder="Kies een gebruikersnaam"
          />
        </div>
        <div class="form-group">
          <label for="email">E-mail</label>
          <input 
            type="email" 
            id="email" 
            v-model="email" 
            required 
            placeholder="Voer uw e-mailadres in"
          />
        </div>
        <div class="form-group">
          <label for="password">Wachtwoord</label>
          <input 
            type="password" 
            id="password" 
            v-model="password" 
            required 
            placeholder="Kies een wachtwoord"
          />
        </div>
        <div class="form-group">
          <label for="confirmPassword">Bevestig wachtwoord</label>
          <input 
            type="password" 
            id="confirmPassword" 
            v-model="confirmPassword" 
            required 
            placeholder="Bevestig uw wachtwoord"
          />
        </div>
        <button type="submit" class="register-button" :disabled="loading">
          {{ loading ? 'Bezig met registreren...' : 'Registreren' }}
        </button>
      </form>
      <div class="login-link">
        <p>Al een account? <NuxtLink to="/login">Log hier in</NuxtLink></p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const username = ref('');
const email = ref('');
const password = ref('');
const confirmPassword = ref('');
const error = ref('');
const success = ref('');
const loading = ref(false);

async function handleRegister() {
  // Reset error and success states
  error.value = '';
  success.value = '';
  
  // Validate form
  if (password.value !== confirmPassword.value) {
    error.value = 'De wachtwoorden komen niet overeen.';
    return;
  }
  
  loading.value = true;
  
  try {
    // Log voor debugging
    console.log("Registratie poging voor:", username.value);
    
    // Echte API aanroep naar de server
    const response = await fetch('/server/api/auth/register.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        username: username.value,
        email: email.value,
        password: password.value
      })
    });
    
    // Lees de response
    const data = await response.json();
    console.log('Registratie response:', data);
    
    if (data.success) {
      // Registratie is gelukt, toon succesmelding
      success.value = data.message || 'Registratie succesvol! U wordt doorverwezen naar de inlogpagina...';
      
      // Sla gebruiker ook op in localStorage voor client-side gebruik
      const newUser = {
        id: data.user.id,
        username: data.user.username,
        email: data.user.email,
        role: data.user.role || 'user'
      };
      
      // Sla gebruikers op in localStorage (als backup)
      const users = JSON.parse(localStorage.getItem('registered_users') || '[]');
      users.push(newUser);
      localStorage.setItem('registered_users', JSON.stringify(users));
      
      // Reset form
      username.value = '';
      email.value = '';
      password.value = '';
      confirmPassword.value = '';
      
      // Redirect to login page after 2 seconds
      setTimeout(() => {
        router.push('/login');
      }, 2000);
    } else {
      // Er was een probleem met registratie
      error.value = data.error || 'Er is een fout opgetreden tijdens het registreren.';
    }
  } catch (err) {
    console.error('Registration error:', err);
    error.value = 'Er is een fout opgetreden tijdens het registreren. Probeer het later opnieuw.';
  } finally {
    loading.value = false;
  }
}
</script>

<style scoped>
.register-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 80vh;
  padding: 20px;
}

.register-form {
  background-color: #f8f9fa;
  padding: 30px;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 400px;
}

h2 {
  text-align: center;
  margin-bottom: 20px;
  color: #3066f6;
  font-size: 30px;
  font-weight: bold;
}

.form-group {
  margin-bottom: 20px;
}

label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
}

input {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 16px;
}

.register-button {
  width: 100%;
  padding: 12px;
  background-color: #3066f6;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 16px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.register-button:hover {
  background-color: #2555d5;
}

.register-button:disabled {
  background-color: #a0b5e8;
  cursor: not-allowed;
}

.error-message {
  background-color: #ffebee;
  color: #c62828;
  padding: 10px;
  border-radius: 4px;
  margin-bottom: 20px;
  text-align: center;
}

.success-message {
  background-color: #e8f5e9;
  color: #2e7d32;
  padding: 10px;
  border-radius: 4px;
  margin-bottom: 20px;
  text-align: center;
}

.login-link {
  text-align: center;
  margin-top: 20px;
}

.login-link a {
  color: #3066f6;
  text-decoration: none;
}

.login-link a:hover {
  text-decoration: underline;
}
</style> 