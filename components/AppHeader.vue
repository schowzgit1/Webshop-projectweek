<!--
    ============================================
    Author: Apothecare Team
    Description: Header component for Apothecare
    ============================================
-->
<template>
  <header class="bg-white shadow">
    <div class="container header-container">
      <div class="logo">
        <h1><NuxtLink to="/" class="logo-link">ApotheCare</NuxtLink></h1>
      </div>
      <div class="nav-links">
        <NuxtLink to="/contact">Contact ons</NuxtLink>
        <NuxtLink to="/medicijnen">Medicijnen</NuxtLink>
        <NuxtLink
          v-if="!isLoggedIn"
          to="/login"
          class="login-btn"
        >
          Inloggen
        </NuxtLink>
        <button
          v-else
          @click="handleLogout"
          class="login-btn"
        >
          Uitloggen
        </button>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { useRouter } from '#imports'

const router = useRouter()
const isLoggedIn = ref(false)

const checkLoginStatus = () => {
  if (process.client) {
    const user = localStorage.getItem('user')
    isLoggedIn.value = !!user
  }
}

const handleLogout = async () => {
  try {
    await fetch('http://localhost:8000/api/logout.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
    })
    if (process.client) {
      localStorage.removeItem('user')
      isLoggedIn.value = false
      router.push('/')
    }
  } catch (error) {
    console.error('Error logging out:', error)
  }
}

onMounted(() => {
  if (process.client) {
    checkLoginStatus()
    window.addEventListener('storage', checkLoginStatus)
  }
})

onBeforeUnmount(() => {
  if (process.client) {
    window.removeEventListener('storage', checkLoginStatus)
  }
})
</script>

<style scoped>
.header-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  height: 60px;
}

.logo h1 {
  font-size: 1.5rem;
  font-weight: bold;
  color: #3066f6;
}

.logo-link {
  text-decoration: none;
  color: inherit;
}

.nav-links {
  display: flex;
  gap: 20px;
  align-items: center;
}

.nav-links a {
  text-decoration: none;
  color: #333;
  transition: color 0.3s ease;
}

.nav-links a:hover {
  color: #3066f6;
}

.login-btn {
  background-color: #3066f6;
  color: white;
  border: none;
  padding: 8px 15px;
  border-radius: 5px;
  cursor: pointer;
  font-weight: bold;
  text-decoration: none;
}

.login-btn:hover {
  background-color: #2050c8;
}
</style> 