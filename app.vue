<template>
    <div class="app">
        <AppHeader />

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
            <li><NuxtLink to="/contact">Contact ons</NuxtLink></li>
            <li><a href="#">Veelgestelde vragen</a></li>
            <li><a href="#">Verzending</a></li>
            <li><a href="#">Retourneren</a></li>
          </ul>
        </div>
        <div class="footer-section">
          <h3>Mijn Account</h3>
          <ul>
            <li><NuxtLink to="/login">Inloggen</NuxtLink></li>
            <li><NuxtLink to="/register">Registreren</NuxtLink></li>
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
import ChatBot from "~/components/ChatBot.vue";
import AppHeader from "~/components/AppHeader.vue";
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
header {
    background-color: white;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    padding: 15px 0;
}

/* Main Content Styles */
main {
    flex: 1;
}

/* Footer Styles */
footer {
    background-color: #f5f5f5;
    padding: 40px 0 10px;
    margin-top: auto;
}

.footer-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    margin-bottom: 30px;
}

.footer-section {
    flex: 1;
    min-width: 200px;
    margin-bottom: 20px;
}

.footer-section h3 {
    font-size: 18px;
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
    text-decoration: none;
    color: #666;
    transition: color 0.3s;
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
    object-fit: cover;
    border-radius: 50%;
}

.copyright {
    text-align: center;
    padding-top: 20px;
    border-top: 1px solid #ddd;
    color: #666;
    font-size: 14px;
}

/* Button Styles */
button, .button {
    background-color: transparent;
    color: rgba(0, 0, 0, 0.5);
    border: none;
    padding: 10px 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
}

button.primary, .button.primary {
    background-color: #2466e1;
    color: white;
    border: 2px solid #2466e1;
    border-radius: 5px;
}

button:hover, .button:hover {
    background-color: rgba(0, 0, 0, 0.1);
}

button.primary:hover, .button.primary:hover {
    background-color: darkblue;
}
</style>
