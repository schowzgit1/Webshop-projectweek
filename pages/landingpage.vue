<!--
    ============================================
    Author: Apothecare Team
    Description: Landingspage Apothecare
    ============================================
-->

<template>
    <div id="main">
        <div>
            <div class="heading-1">
                Uw online hypotheek voor al uw medicijnen.
            </div>
            <div class="heading-3">
                Veilig en gemakkelijk uw medicijnen bestellen met persoonlijk
                advies
            </div>

            <div class="button-container">
                <NuxtLink v-if="!isLoggedIn" to="/login" class="button">Inloggen</NuxtLink>
                <NuxtLink v-else to="/medicijnen" class="button primary">Start met bestellen</NuxtLink>
            </div>
        </div>
        <div>
            <img
                src="https://thumbs.dreamstime.com/b/twee-apothekers-moderne-apotheek-62842762.jpg"
                alt="alt"
            />
        </div>
    </div>
    <div id="footer">
        <div class="footerTile">
            <img src="assets/images/pills.png" alt="alt" />
            <div class="heading-1">Smart Bestelprocess</div>
            <p>Eenvouding bestelling met een klik via ons systeem.</p>
        </div>
        <div class="footerTile">
            <img src="assets/images/pills.png" alt="alt" />
            <div class="heading-1">24/7 Chat support</div>
            <p>Direct hulp via onze virtual assistant.</p>
        </div>
        <div class="footerTile">
            <img src="assets/images/pills.png" alt="alt" />
            <div class="heading-1">Bestelgeschiedenis</div>
            <p>Bekijk en herhaal eenvoudig meerdere bestellingen.</p>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'

const isLoggedIn = ref(false)

const checkLoginStatus = () => {
    if (process.client) {
        const user = localStorage.getItem('user')
        isLoggedIn.value = !!user
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
* {
    font-family: Arial, Helvetica, sans-serif;
    text-align: center;
    margin: 0;
    padding: 0;
}

#header {
    height: 10vh;
    background-color: #ffffff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 20px;
}
.heading-1 {
    font-size: 2em;
    font-weight: bold;
    margin: 10px 0;
}

.heading-3 {
    font-size: 1.2em;
    font-weight: normal;
    margin: 10px 0;
}

#main {
    background-color: #eff6ff;
    height: 66vh;
    display: flex;
    justify-content: space-between;
    padding: 20px;
}

#main > div {
    width: 45%;
}

#main > div:first-child {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: 100%;
}

#main > div:last-child {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100%;
}

#footer {
    height: 20vh;
    background-color: #ffffff;
    display: flex;
    justify-content: space-around;
    align-items: center;
    padding: 20px;
}

.footerTile h1 {
    text-align: left;
}

.footerTile {
    background: white;
    padding: 15px;
    width: 25%;
    text-align: center;
    border-radius: 10px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    text-align: left;
}

#buttonContainer {
    display: flex;
    gap: 10px;
}

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

#main div h1 {
    margin: 10px 0;
    font-weight: bold;
    font-size: 50px;
}
#main div h3 {
    margin: 10px 0;
    font-size: 20px;
}

#main div img {
    width: 100%;
    height: auto;
}

.button-container {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}
</style>
