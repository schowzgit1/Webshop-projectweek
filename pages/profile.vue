<template>
  <div class="profile-container">
    <div class="container">
      <h1>Mijn Profiel</h1>
      
      <div v-if="loading" class="loading">
        <p>Bezig met laden...</p>
      </div>
      
      <div v-else-if="error" class="error-message">
        <p>{{ error }}</p>
        <button @click="fetchUserData" class="retry-button">Opnieuw proberen</button>
      </div>
      
      <div v-else>
        <div class="profile-grid">
          <div class="profile-info">
            <h2>Persoonlijke gegevens</h2>
            <div class="info-card">
              <div class="info-item">
                <span class="info-label">Gebruikersnaam:</span>
                <span class="info-value">{{ user.username }}</span>
              </div>
              <div class="info-item">
                <span class="info-label">E-mailadres:</span>
                <span class="info-value">{{ user.email }}</span>
              </div>
              <div class="info-item">
                <span class="info-label">Account aangemaakt op:</span>
                <span class="info-value">{{ formatDate(user.created_at) }}</span>
              </div>
            </div>
            <button class="edit-button">Gegevens bewerken</button>
          </div>
          
          <div class="order-history">
            <h2>Mijn bestellingen</h2>
            <div v-if="orders.length > 0">
              <div v-for="order in orders" :key="order.id" class="order-card">
                <div class="order-header">
                  <div class="order-id">Bestelling #{{ order.id }}</div>
                  <div class="order-date">{{ formatDate(order.created_at) }}</div>
                </div>
                <div class="order-status">
                  Status: <span :class="'status-' + order.status">{{ order.status }}</span>
                </div>
                <div class="order-total">
                  Totaal: €{{ formatNumber(order.total_price) }}
                </div>
                <button class="view-details-button">Details bekijken</button>
              </div>
            </div>
            <div v-else class="no-orders">
              <p>U heeft nog geen bestellingen geplaatst.</p>
              <NuxtLink to="/medicijnen" class="shop-button">Ga naar de shop</NuxtLink>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const user = ref({
  id: 0,
  username: '',
  email: '',
  role: '',
  created_at: ''
});

const orders = ref([]);
const loading = ref(true);
const error = ref('');

async function fetchUserData() {
  loading.value = true;
  error.value = '';
  
  try {
    // Fetch user data
    const userResponse = await fetch('/server/api/auth/check-session.php');
    const userData = await userResponse.json();
    
    if (userData.success && userData.loggedIn) {
      user.value = userData.user;
      
      // Fetch order history
      const ordersResponse = await fetch('/server/api/user/order-history.php');
      const ordersData = await ordersResponse.json();
      
      if (ordersData.success) {
        orders.value = ordersData.orders;
      } else {
        error.value = ordersData.error || 'Er is een fout opgetreden bij het ophalen van uw bestellingen.';
      }
    } else {
      error.value = 'U bent niet ingelogd. Log in om uw profiel te bekijken.';
    }
  } catch (err) {
    error.value = 'Er is een fout opgetreden bij het ophalen van uw gegevens.';
    console.error('Error fetching user data:', err);
  } finally {
    loading.value = false;
  }
}

function formatNumber(number) {
  return number.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function formatDate(dateString) {
  const date = new Date(dateString);
  return date.toLocaleDateString('nl-NL', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  });
}

onMounted(() => {
  fetchUserData();
});
</script>

<style scoped>
.profile-container {
  padding: 30px 0;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 15px;
}

h1 {
  margin-bottom: 30px;
  color: #3066f6;
}

h2 {
  margin-bottom: 20px;
  color: #333;
}

.loading, .error-message {
  text-align: center;
  padding: 30px;
  background-color: #f5f5f5;
  border-radius: 8px;
  margin-bottom: 30px;
}

.error-message {
  color: #c62828;
}

.retry-button {
  margin-top: 15px;
  padding: 8px 15px;
  background-color: #3066f6;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.profile-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 30px;
}

@media (min-width: 768px) {
  .profile-grid {
    grid-template-columns: 1fr 2fr;
  }
}

.profile-info, .order-history {
  background-color: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.info-card {
  margin-bottom: 20px;
}

.info-item {
  margin-bottom: 15px;
  display: flex;
  flex-direction: column;
}

.info-label {
  font-weight: bold;
  color: #666;
  margin-bottom: 5px;
}

.info-value {
  color: #333;
}

.edit-button, .view-details-button, .shop-button {
  padding: 10px 15px;
  background-color: #3066f6;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s;
  text-decoration: none;
  display: inline-block;
}

.edit-button:hover, .view-details-button:hover, .shop-button:hover {
  background-color: #2555d5;
}

.order-card {
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 15px;
  margin-bottom: 15px;
}

.order-header {
  display: flex;
  justify-content: space-between;
  margin-bottom: 10px;
}

.order-id {
  font-weight: bold;
}

.order-date {
  color: #666;
}

.order-status {
  margin-bottom: 10px;
}

.status-pending {
  color: #f57c00;
}

.status-processing {
  color: #1976d2;
}

.status-completed {
  color: #388e3c;
}

.status-cancelled {
  color: #d32f2f;
}

.order-total {
  font-weight: bold;
  margin-bottom: 15px;
}

.view-details-button {
  width: 100%;
}

.no-orders {
  text-align: center;
  padding: 30px 0;
}

.shop-button {
  margin-top: 15px;
}
</style> 