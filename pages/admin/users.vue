<template>
  <div class="admin-users-container">
    <div class="admin-header">
      <h1>Gebruikersbeheer</h1>
      <NuxtLink to="/admin" class="back-button">← Terug naar Dashboard</NuxtLink>
    </div>

    <div v-if="loading" class="loading-message">
      Gebruikers laden...
    </div>

    <div v-else-if="error" class="error-message">
      <p>{{ error }}</p>
      <button @click="loadUsers" class="retry-button">Opnieuw proberen</button>
      <button @click="useLocalUsers" class="fallback-button">Gebruik lokale gebruikers (demo)</button>
    </div>

    <div v-else class="users-content">
      <div class="users-count">
        <p>Totaal aantal gebruikers: <span class="count">{{ users.length }}</span></p>
      </div>

      <div class="users-table-container">
        <table class="users-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Gebruikersnaam</th>
              <th>E-mail</th>
              <th>Rol</th>
              <th>Aangemaakt op</th>
              <th>Acties</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in users" :key="user.id" :class="{ 'admin-row': user.role === 'admin' }">
              <td>{{ user.id }}</td>
              <td>{{ user.username }}</td>
              <td>{{ user.email }}</td>
              <td><span :class="'role-badge ' + user.role">{{ user.role }}</span></td>
              <td>{{ formatDate(user.created_at || user.createdAt) }}</td>
              <td>
                <button 
                  v-if="user.role !== 'admin'"
                  @click="deleteUser(user.id)" 
                  class="delete-button"
                  :disabled="isDeleting"
                >
                  Verwijderen
                </button>
                <span v-else class="admin-note">Admin-account</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="isDemoMode" class="demo-notice">
        <p>Let op: U gebruikt de demo modus. Wijzigingen worden alleen lokaal opgeslagen.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const users = ref([])
const loading = ref(true)
const error = ref(null)
const isDeleting = ref(false)
const isDemoMode = ref(false)

// Timeout voor API aanroep
let apiTimeout = null

// Functie om te controleren of de huidige gebruiker admin is
const checkAdminAccess = () => {
  const userStr = localStorage.getItem('user')
  if (!userStr) {
    router.push('/login')
    return false
  }

  const user = JSON.parse(userStr)
  if (user.role !== 'admin') {
    router.push('/')
    return false
  }
  
  return true
}

// Functie om gebruikers te laden
const loadUsers = async () => {
  if (!checkAdminAccess()) return
  
  loading.value = true
  error.value = null
  isDemoMode.value = false
  
  // Instellen van een timeout voor de API aanroep (10 seconden)
  clearTimeout(apiTimeout)
  apiTimeout = setTimeout(() => {
    if (loading.value) {
      loading.value = false
      error.value = 'Het laden van gebruikers duurt te lang. De server reageert niet.'
    }
  }, 10000)
  
  try {
    // API aanroepen om gebruikers te laden met timeout
    const controller = new AbortController()
    const timeoutId = setTimeout(() => controller.abort(), 8000)
    
    const response = await fetch('/server/api/admin/get-users.php', {
      headers: {
        'Authorization': `User ${JSON.parse(localStorage.getItem('user')).id}`
      },
      signal: controller.signal
    })
    
    clearTimeout(timeoutId)
    
    const data = await response.json()
    
    if (data.success) {
      users.value = data.users
    } else {
      error.value = data.message || 'Er is een fout opgetreden bij het laden van gebruikers'
    }
  } catch (err) {
    console.error('Error loading users:', err)
    
    if (err.name === 'AbortError') {
      error.value = 'Het verzoek werd afgebroken wegens timeout. De server reageert te traag.'
    } else {
      error.value = 'Er is een fout opgetreden bij het verbinden met de server'
    }
  } finally {
    clearTimeout(apiTimeout)
    loading.value = false
  }
}

// Functie om lokale gebruikers te gebruiken (demo modus)
const useLocalUsers = () => {
  loading.value = true
  error.value = null
  isDemoMode.value = true
  
  setTimeout(() => {
    // Admin gebruiker
    const adminUser = JSON.parse(localStorage.getItem('user'))
    
    // Geregistreerde gebruikers
    const registeredUsers = JSON.parse(localStorage.getItem('registered_users') || '[]')
    
    // Alle gebruikers samenvoegen
    users.value = [
      {
        id: adminUser.id,
        username: adminUser.username,
        email: adminUser.email,
        role: adminUser.role,
        created_at: new Date().toISOString()
      },
      ...registeredUsers.map(user => ({
        id: user.id,
        username: user.username,
        email: user.email,
        role: user.role || 'user',
        created_at: user.createdAt || new Date().toISOString()
      }))
    ]
    
    loading.value = false
  }, 800)
}

// Functie om een gebruiker te verwijderen
const deleteUser = async (userId) => {
  if (!confirm('Weet je zeker dat je deze gebruiker wilt verwijderen?')) {
    return
  }
  
  isDeleting.value = true
  
  if (isDemoMode.value) {
    // In demo modus: lokale gebruiker verwijderen
    setTimeout(() => {
      // Gebruiker uit localStorage verwijderen
      const registeredUsers = JSON.parse(localStorage.getItem('registered_users') || '[]')
      const updatedUsers = registeredUsers.filter(user => user.id !== userId)
      localStorage.setItem('registered_users', JSON.stringify(updatedUsers))
      
      // Gebruiker uit de lijst verwijderen
      users.value = users.value.filter(user => user.id !== userId)
      
      isDeleting.value = false
    }, 800)
  } else {
    // Echte API aanroepen
    try {
      const response = await fetch('/server/api/admin/delete-user.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `User ${JSON.parse(localStorage.getItem('user')).id}`
        },
        body: JSON.stringify({ userId })
      })
      
      const data = await response.json()
      
      if (data.success) {
        // Gebruiker uit de lijst verwijderen
        users.value = users.value.filter(user => user.id !== userId)
      } else {
        alert(data.message || 'Er is een fout opgetreden bij het verwijderen van de gebruiker')
      }
    } catch (err) {
      console.error('Error deleting user:', err)
      alert('Er is een fout opgetreden bij het verbinden met de server')
    } finally {
      isDeleting.value = false
    }
  }
}

// Datum formatteren
const formatDate = (dateString) => {
  if (!dateString) return '-'
  
  const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }
  return new Date(dateString).toLocaleDateString('nl-NL', options)
}

// Gebruikers laden bij het laden van de pagina
onMounted(() => {
  loadUsers()
})
</script>

<style scoped>
.admin-users-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

.admin-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

h1 {
  color: #333;
  margin: 0;
}

.back-button {
  background-color: #f0f0f0;
  color: #333;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  text-decoration: none;
  font-weight: 500;
  transition: background-color 0.3s;
}

.back-button:hover {
  background-color: #e0e0e0;
}

.loading-message {
  text-align: center;
  padding: 2rem;
  color: #666;
}

.error-message {
  background-color: #ffebee;
  color: #d32f2f;
  padding: 1rem;
  border-radius: 8px;
  margin-bottom: 1rem;
  text-align: center;
}

.retry-button, .fallback-button {
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  margin-top: 0.5rem;
  cursor: pointer;
  color: white;
  font-weight: 500;
}

.retry-button {
  background-color: #d32f2f;
  margin-right: 0.5rem;
}

.fallback-button {
  background-color: #ff9800;
}

.users-count {
  margin-bottom: 1rem;
  font-size: 1.1rem;
}

.count {
  font-weight: bold;
  color: #2196F3;
}

.users-table-container {
  overflow-x: auto;
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.users-table {
  width: 100%;
  border-collapse: collapse;
}

.users-table th,
.users-table td {
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid #eee;
}

.users-table th {
  background-color: #f5f5f5;
  font-weight: 600;
  color: #333;
}

.users-table tbody tr:hover {
  background-color: #f9f9f9;
}

.admin-row {
  background-color: #f0f7ff;
}

.role-badge {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 500;
}

.role-badge.admin {
  background-color: #e3f2fd;
  color: #1976d2;
}

.role-badge.user {
  background-color: #e8f5e9;
  color: #388e3c;
}

.delete-button {
  background-color: #f44336;
  color: white;
  border: none;
  padding: 0.4rem 0.75rem;
  border-radius: 4px;
  cursor: pointer;
  font-size: 0.9rem;
  transition: background-color 0.3s;
}

.delete-button:hover {
  background-color: #d32f2f;
}

.delete-button:disabled {
  background-color: #ffcdd2;
  cursor: not-allowed;
}

.admin-note {
  color: #666;
  font-style: italic;
  font-size: 0.9rem;
}

.demo-notice {
  margin-top: 1.5rem;
  padding: 0.75rem;
  background-color: #fff3e0;
  color: #e65100;
  border-radius: 4px;
  font-size: 0.9rem;
}
</style> 