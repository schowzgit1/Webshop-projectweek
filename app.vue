<template>
    <div class="app">
        <header class="header">
            <div class="container header-container">
                <div class="logo">
                    <h1><NuxtLink to="/landingpage">ApotheCare</NuxtLink></h1>
                </div>
                <div class="nav-links">
                    <NuxtLink to="/contact">Contact ons</NuxtLink>
                    <NuxtLink to="/medicijnen">Medicijnen</NuxtLink>
                    <template v-if="isLoggedIn">
                        <NuxtLink v-if="isAdmin" to="/admin" class="admin-link">Admin Dashboard</NuxtLink>
                        <NuxtLink v-else to="/profile" class="profile-link">Mijn Profiel</NuxtLink>
                        <button @click="handleLogout" class="logout-btn">Uitloggen</button>
                    </template>
                    <NuxtLink v-else to="/login" class="login-btn">Inloggen</NuxtLink>
                </div>
            </div>
        </header>

        <main>
            <NuxtPage />
        </main>

<<<<<<< Updated upstream
    <footer>
      <div class="container footer-container">
        <div class="footer-section">
          <h3>Over Ons</h3>
          <ul>
            <li><a href="#">Wie zijn wij</a></li>
            <li><a href="#">Onze missie</a></li>
            <li><a href="#">Locaties</a></li>
          </ul>
        </div>
        <div class="footer-section">
          <h3>Klantenservice</h3>
          <ul>
            <li><NuxtLink to="/contact">Contact ons</NuxtLink></li>
            <li><a href="#">Veelgestelde vragen</a></li>
            <li><a href="#">Verzending</a></li>
            <li><a href="#">Retourneren</a></li>
          </ul>
        </div>
        <div class="footer-section">
          <h3>Mijn Account</h3>
          <ul>
            <li><a href="#">Inloggen</a></li>
            <li><a href="#">Registreren</a></li>
            <li><a href="#">Mijn gegevens</a></li>
          </ul>
        </div>
        <div class="footer-section">
          <h3>Volg Ons</h3>
          <div class="social-links">
            <a href="#" aria-label="Twitter">
              <img src="/assets/images/twitter.jpg" alt="Twitter" class="social-icon">
            </a>
            <a href="#" aria-label="Facebook">
              <img src="/assets/images/facebook.jpg" alt="Facebook" class="social-icon">
            </a>
            <a href="#" aria-label="Instagram">
              <img src="/assets/images/instagram.jpg" alt="Instagram" class="social-icon">
            </a>
          </div>
        </div>
      </div>
      <div class="copyright">
        <p>© 2025 ApotheCare. Alle rechten voorbehouden.</p>
      </div>
    </footer>
=======
        <footer>
            <div class="container footer-container">
                <div class="footer-section">
                    <h3>Over Ons</h3>
                    <ul>
                        <li><a href="#" @click.prevent>Wie zijn wij</a></li>
                        <li><a href="#" @click.prevent>Onze missie</a></li>
                        <li><a href="#" @click.prevent>Locaties</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Klantenservice</h3>
                    <ul>
                        <li><NuxtLink to="/contact">Contact</NuxtLink></li>
                        <li><a href="#" @click.prevent>Veelgestelde vragen</a></li>
                        <li><a href="#" @click.prevent>Verzending</a></li>
                        <li><a href="#" @click.prevent>Retourneren</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Mijn Account</h3>
                    <ul>
                        <li v-if="!isLoggedIn"><NuxtLink to="/login">Inloggen</NuxtLink></li>
                        <li v-if="!isLoggedIn"><NuxtLink to="/register">Registreren</NuxtLink></li>
                        <li v-if="isLoggedIn"><NuxtLink to="/profile">Mijn profiel</NuxtLink></li>
                        <li v-if="isLoggedIn"><a href="#" @click.prevent="handleLogout">Uitloggen</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Volg Ons</h3>
                    <div class="social-links">
                        <a href="#" aria-label="Twitter">
                            <img src="/assets/images/twitter.jpg" alt="Twitter" class="social-icon" />
                        </a>
                        <a href="#" aria-label="Facebook">
                            <img src="/assets/images/facebook.jpg" alt="Facebook" class="social-icon" />
                        </a>
                        <a href="#" aria-label="Instagram">
                            <img src="/assets/images/instagram.jpg" alt="Instagram" class="social-icon" />
                        </a>
                    </div>
                </div>
            </div>
            <div class="copyright">
                <p>© 2025 ApotheCare. Alle rechten voorbehouden.</p>
            </div>
        </footer>
>>>>>>> Stashed changes

        <!-- Chat Bot Component -->
        <ChatBot />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import ChatBot from "~/components/ChatBot.vue";

const router = useRouter();
const isLoggedIn = ref(false);
const isAdmin = ref(false);
const user = ref(null);

function checkLoginStatus() {
    try {
        const storedUser = localStorage.getItem('user');
        if (storedUser) {
            const userData = JSON.parse(storedUser);
            isLoggedIn.value = true;
            isAdmin.value = userData.role === 'admin';
            user.value = userData;
        } else {
            isLoggedIn.value = false;
            isAdmin.value = false;
            user.value = null;
        }
    } catch (err) {
        console.error('Error checking login status:', err);
        isLoggedIn.value = false;
        isAdmin.value = false;
        user.value = null;
    }
}

async function handleLogout() {
    try {
        localStorage.removeItem('user');
        isLoggedIn.value = false;
        isAdmin.value = false;
        user.value = null;
        await router.push('/landingpage');
        window.location.reload();
    } catch (err) {
        console.error('Error logging out:', err);
    }
}

// Check login status when component mounts and on route changes
onMounted(() => {
    checkLoginStatus();
    window.addEventListener('storage', checkLoginStatus);
});
</script>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

.app {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

.container {
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 15px;
}

/* Header Styles */
.header {
    background-color: white;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    padding: 15px 0;
}

.header-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo h1 {
    font-size: 1.5rem;
    font-weight: bold;
    color: #3066f6;
}

.logo h1 a {
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
}

.login-btn, .logout-btn, .admin-link, .profile-link {
    background-color: #3066f6;
    color: white !important;
    border: none;
    padding: 8px 15px;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    text-decoration: none;
}

.logout-btn {
    background-color: #f44336;
}

.admin-link {
    background-color: #4caf50;
}

.profile-link {
    background-color: #ff9800;
}

/* Search Bar */
.search-container {
    margin: 20px 0;
    display: flex;
    width: 100%;
}

.search-input {
    flex: 1;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 5px 0 0 5px;
    font-size: 1rem;
}

.search-btn {
    background-color: #3066f6;
    color: white;
    border: none;
    padding: 0 15px;
    border-radius: 0 5px 5px 0;
    cursor: pointer;
}

/* Categories */
.categories {
    display: flex;
    justify-content: space-between;
    gap: 15px;
    margin-bottom: 30px;
}

.category-card {
    flex: 1;
    background-color: white;
    border-radius: 8px;
    padding: 15px;
    text-align: center;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s;
}

.category-card:hover {
    transform: translateY(-5px);
}

.category-icon {
    font-size: 24px;
    margin-bottom: 10px;
}

/* Products */
.products {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.product-card {
    background-color: white;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
}

.product-image {
    margin-bottom: 15px;
    text-align: center;
}

.product-image img {
    max-width: 100%;
    height: auto;
}

.product-card h3 {
    font-size: 16px;
    margin-bottom: 5px;
}

.product-quantity {
    color: #666;
    font-size: 14px;
    margin-bottom: 15px;
}

.product-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: auto;
}

.product-price {
    font-weight: bold;
    color: #3066f6;
}

.add-to-cart-btn {
    background-color: #3066f6;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 4px;
    cursor: pointer;
}

/* Footer */
footer {
    background-color: #f5f5f5;
    padding: 40px 0 20px;
    margin-top: auto;
}

.footer-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 30px;
}

.footer-section h3 {
    margin-bottom: 15px;
    color: #333;
}

.footer-section ul {
    list-style: none;
}

.footer-section ul li {
    margin-bottom: 8px;
}

.footer-section ul li a {
    color: #666;
    text-decoration: none;
}

.footer-section ul li a:hover {
    color: #3066f6;
}

.social-links {
    display: flex;
    gap: 10px;
}

.social-icon {
    width: 30px;
    height: 30px;
    border-radius: 50%;
}

.copyright {
    text-align: center;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #ddd;
    color: #666;
}

/* Responsive Styles */
@media (max-width: 768px) {
    .categories {
        flex-wrap: wrap;
    }

    .category-card {
        flex: 1 1 45%;
        margin-bottom: 15px;
    }

    .footer-container {
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 480px) {
    .nav-links {
        gap: 10px;
    }

    .category-card {
        flex: 1 1 100%;
    }

    .products {
        grid-template-columns: 1fr;
    }

    .footer-container {
        grid-template-columns: 1fr;
    }
}
</style>
