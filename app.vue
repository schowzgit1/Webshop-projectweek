<template>
  <div class="app">
    <header class="header">
      <div class="container header-container">
        <div class="logo">
          <h1><NuxtLink to="/medicijnen">ApotheCare</NuxtLink></h1>
        </div>
        <div class="nav-links">
          <NuxtLink to="/medicijnen">Medicijnen</NuxtLink>
          <button class="login-btn">Inloggen</button>
        </div>
      </div>
    </header>

    <main>
      <NuxtPage />
    </main>

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
            <li><a href="#">Contact</a></li>
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

    <!-- Chat Bot Component -->
    <ChatBot />
  </div>
</template>

<script setup>
import ChatBot from '~/components/ChatBot.vue';
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

.login-btn {
  background-color: #3066f6;
  color: white;
  border: none;
  padding: 8px 15px;
  border-radius: 5px;
  cursor: pointer;
  font-weight: bold;
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
  font-size: 18px;
  color: #333;
}

.add-to-cart {
  background-color: #3066f6;
  color: white;
  border: none;
  padding: 6px 12px;
  border-radius: 5px;
  cursor: pointer;
  font-size: 14px;
}

/* Footer */
footer {
  background-color: #f8f9fa;
  padding: 40px 0 20px;
  margin-top: auto;
}

.footer-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 30px;
}

.footer-section h3 {
  margin-bottom: 15px;
  font-size: 16px;
}

.footer-section ul {
  list-style: none;
}

.footer-section li {
  margin-bottom: 8px;
}

.footer-section a {
  text-decoration: none;
  color: #666;
}

.social-links {
  display: flex;
  gap: 15px;
}

.social-icon {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  object-fit: cover;
}

.copyright {
  margin-top: 30px;
  text-align: center;
  padding-top: 20px;
  border-top: 1px solid #ddd;
  color: #666;
  font-size: 14px;
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
