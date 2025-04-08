<template>
  <div class="admin-dashboard" v-if="isAdmin">
    <div class="container">
      <h1>Admin Dashboard</h1>
      
      <div v-if="loading" class="loading">
        <p>Bezig met laden...</p>
      </div>
      
      <div v-else-if="error" class="error-message">
        <p>{{ error }}</p>
        <button @click="fetchData" class="retry-button">Opnieuw proberen</button>
      </div>
      
      <div v-else>
        <div class="stats-grid">
          <div class="stat-card">
            <h3>Omzet</h3>
            <p class="stat-value">€{{ formatNumber(stats.total_revenue) }}</p>
          </div>
          <div class="stat-card">
            <h3>Bestellingen</h3>
            <p class="stat-value">{{ stats.total_orders }}</p>
          </div>
          <div class="stat-card">
            <h3>Producten</h3>
            <p class="stat-value">{{ stats.total_products }}</p>
          </div>
          <div class="stat-card">
            <h3>Gebruikers</h3>
            <p class="stat-value">{{ stats.total_users }}</p>
          </div>
          <div class="stat-card">
            <h3>Bezoeken</h3>
            <p class="stat-value">{{ stats.total_visits }}</p>
          </div>
          <div class="stat-card">
            <h3>Openstaande tickets</h3>
            <p class="stat-value">{{ stats.open_tickets }}</p>
          </div>
        </div>
        
        <div class="admin-sections">
          <div class="admin-section-card">
            <h2>Beheer Producten</h2>
            <p>Voeg producten toe, wijzig of verwijder huidige producten.</p>
            <button class="action-button">Producten Beheren</button>
          </div>
          
          <div class="admin-section-card">
            <h2>Beheer Bestellingen</h2>
            <p>Bekijk en beheer alle klantbestellingen.</p>
            <button class="action-button">Bestellingen Bekijken</button>
          </div>
          
          <div class="admin-section-card">
            <h2>Beheer Gebruikers</h2>
            <p>Bekijk en beheer alle gebruikersaccounts.</p>
            <NuxtLink to="/admin/users" class="action-button">Gebruikers Beheren</NuxtLink>
          </div>
          
          <div class="admin-section-card">
            <h2>Beheer Tickets</h2>
            <p>Bekijk en reageer op klantvragen en ondersteuningstickets.</p>
            <button class="action-button">Tickets Bekijken</button>
          </div>
        </div>
        
        <div class="recent-data">
          <div class="recent-orders">
            <h2>Recente bestellingen</h2>
            <div v-if="stats.recent_orders && stats.recent_orders.length > 0">
              <table>
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Gebruiker</th>
                    <th>Bedrag</th>
                    <th>Status</th>
                    <th>Datum</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="order in stats.recent_orders" :key="order.id">
                    <td>{{ order.id }}</td>
                    <td>{{ order.username }}</td>
                    <td>€{{ formatNumber(order.total_price) }}</td>
                    <td>{{ order.status }}</td>
                    <td>{{ formatDate(order.created_at) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <p v-else>Geen recente bestellingen</p>
          </div>
          
          <div class="recent-tickets">
            <h2>Recente tickets</h2>
            <div v-if="stats.recent_tickets && stats.recent_tickets.length > 0">
              <table>
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Naam</th>
                    <th>Onderwerp</th>
                    <th>Status</th>
                    <th>Datum</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="ticket in stats.recent_tickets" :key="ticket.id">
                    <td>{{ ticket.id }}</td>
                    <td>{{ ticket.name }}</td>
                    <td>{{ ticket.subject }}</td>
                    <td>{{ ticket.status }}</td>
                    <td>{{ formatDate(ticket.created_at) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <p v-else>Geen recente tickets</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div v-else class="access-denied">
    <h1>Toegang Geweigerd</h1>
    <p>U heeft geen toestemming om deze pagina te bekijken.</p>
    <NuxtLink to="/login" class="login-link">Inloggen als Administrator</NuxtLink>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const stats = ref({
  total_revenue: 0,
  total_orders: 0,
  total_products: 0,
  total_users: 0,
  total_visits: 0,
  total_tickets: 0,
  open_tickets: 0,
  recent_orders: [],
  popular_products: [],
  recent_tickets: [],
  monthly_revenue: []
});
const loading = ref(true);
const error = ref('');
const isAdmin = ref(false);

// Voorbeeldgegevens voor recente bestellingen
const recentOrders = ref([
  { id: 1001, user: 'johndoe', amount: 59.99, status: 'Voltooid', date: '2023-11-15' },
  { id: 1002, user: 'janedoe', amount: 129.50, status: 'Verwerking', date: '2023-11-14' },
  { id: 1003, user: 'bobsmith', amount: 45.75, status: 'Verzonden', date: '2023-11-12' },
  { id: 1004, user: 'sarahj', amount: 89.99, status: 'Voltooid', date: '2023-11-10' },
  { id: 1005, user: 'michaelb', amount: 34.50, status: 'Verzonden', date: '2023-11-08' }
]);

// Voorbeeldgegevens voor recente tickets
const recentTickets = ref([
  { id: 2001, user: 'johndoe', subject: 'Betaalproblemen', status: 'Open', date: '2023-11-15' },
  { id: 2002, user: 'janedoe', subject: 'Levering vertraagd', status: 'Beantwoord', date: '2023-11-14' },
  { id: 2003, user: 'bobsmith', subject: 'Retour vraag', status: 'Gesloten', date: '2023-11-12' },
  { id: 2004, user: 'sarahj', subject: 'Product informatie', status: 'Open', date: '2023-11-10' },
  { id: 2005, user: 'michaelb', subject: 'Factuur probleem', status: 'Beantwoord', date: '2023-11-08' }
]);

// Haal de geregistreerde gebruikers op
const userCount = computed(() => {
  try {
    const registeredUsers = JSON.parse(localStorage.getItem('registered_users') || '[]');
    return registeredUsers.length + 1; // +1 voor de admin gebruiker
  } catch (err) {
    console.error('Error getting user count:', err);
    return 0;
  }
});

// Haal statistieken op (simuleer API oproep)
async function fetchData() {
  loading.value = true;
  error.value = '';
  
  try {
    // Simuleer een API aanroep voor statistieken
    await new Promise(resolve => setTimeout(resolve, 1000));
    
    // Voorbeeld statistieken
    stats.value = {
      total_revenue: 1250.75,
      total_orders: 36,
      total_products: 148,
      total_users: userCount.value,
      total_visits: 2456,
      total_tickets: 28,
      open_tickets: 12
    };
  } catch (err) {
    console.error('Error fetching stats:', err);
    error.value = 'Er is een fout opgetreden bij het ophalen van de statistieken.';
  } finally {
    loading.value = false;
  }
}

// Check of de gebruiker admin is
function checkAdminAccess() {
  try {
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    isAdmin.value = user && user.role === 'admin';
    
    if (!isAdmin.value) {
      console.log('Geen admin rechten, doorverwijzen naar login pagina');
    }
  } catch (err) {
    console.error('Error checking admin access:', err);
    isAdmin.value = false;
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
  checkAdminAccess();
  if (isAdmin.value) {
    fetchData();
  }
});
</script>

<style scoped>
.admin-dashboard {
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

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
}

.stat-card {
  background-color: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  text-align: center;
}

.stat-card h3 {
  margin-bottom: 10px;
  color: #666;
}

.stat-value {
  font-size: 24px;
  font-weight: bold;
  color: #3066f6;
}

.admin-sections {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
}

.admin-section-card {
  background-color: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.admin-section-card h2 {
  margin-bottom: 10px;
  color: #333;
}

.admin-section-card p {
  margin-bottom: 15px;
  color: #666;
}

.action-button {
  padding: 10px 15px;
  background-color: #3066f6;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.action-button:hover {
  background-color: #2555d5;
}

.recent-data {
  display: grid;
  grid-template-columns: 1fr;
  gap: 30px;
}

@media (min-width: 768px) {
  .recent-data {
    grid-template-columns: 1fr 1fr;
  }
}

.recent-orders, .recent-tickets {
  background-color: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.recent-orders h2, .recent-tickets h2 {
  margin-bottom: 15px;
  color: #333;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th, td {
  padding: 10px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}

th {
  background-color: #f5f5f5;
  font-weight: bold;
}

tr:hover {
  background-color: #f9f9f9;
}

/* Toegang geweigerd */
.access-denied {
  text-align: center;
  padding: 50px 20px;
}

.login-link {
  display: inline-block;
  background-color: #3066f6;
  color: white;
  padding: 10px 20px;
  border-radius: 4px;
  margin-top: 20px;
  text-decoration: none;
}
</style> 