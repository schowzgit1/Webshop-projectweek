<template>
    <div id="main">
        <div>
            <h1>Uw online hypotheek voor al uw medicijnen.</h1>
            <h3>
                Veilig en gemakkelijk uw medicijnen bestellen met persoonlijk
                advies
            </h3>
            <button>Inloggen</button>
            <button class="primary">Start met bestellen</button>
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
            <h1>Smart Bestelprocess</h1>
            Eenvouding bestelling met een klik via ons systeem.
        </div>
        <div class="footerTile">
            <img src="assets/images/pills.png" alt="alt" />
            <h1>24/7 Chat support</h1>
            Direct hulp via onze virtual assistant.
        </div>
        <div class="footerTile">
            <img src="assets/images/pills.png" alt="alt" />
            <h1>Bestelgeschiedenis</h1>
            Bekijk en herhaal eenvoudig meerdere bestellingen.
        </div>
    </div>
</template>

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

button {
    background-color: transparent;
    color: rgba(0, 0, 0, 0.5);
    border: none;
    padding: 10px 20px;
    cursor: pointer;
    transition: all 0.3s ease;
}

button.primary {
    background-color: #2466e1;
    color: white;
    border: 2px solid #2466e1;
    border-radius: 5px;
}

button:hover {
    background-color: rgba(0, 0, 0, 0.1);
}

button.primary:hover {
    background-color: darkblue;
}

#main div h1,
#main div h3 {
    margin: 10px 0;
}

#main div img {
    width: 100%;
    height: auto;
}
</style>
