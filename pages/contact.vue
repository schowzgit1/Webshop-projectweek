<!--
    ============================================
    Author: Apothecare Team
    Description: Contact page
    ============================================
-->
<template>
  <div class="contact-page">

    <!-- content -->
    <main class="contact-content">
      <h1>Neem Contact Op</h1>
      <p>Heeft u vragen over onze diensten? Ons team staat klaar om u te helpen.</p>

      <div class="contact-container">
        <!-- form -->
        <form @submit.prevent="submitForm" class="contact-form">
          <h2>Stuur ons een bericht</h2>

          <div class="form-group">
            <label for="name">Naam</label>
            <input id="name" v-model="name" type="text" required class="form-input" />
          </div>

          <div class="form-group">
            <label for="email">E-mail</label>
            <input id="email" v-model="email" type="email" required class="form-input" />
          </div>

          <div class="form-group">
            <label for="subject">Onderwerp</label>
            <select id="subject" v-model="subject" class="form-input">
              <option>Algemene vraag</option>
              <option>Support</option>
              <option>Feedback</option>
            </select>
          </div>

          <div class="form-group">
            <label for="message">Bericht</label>
            <textarea id="message" v-model="message" required class="form-input"></textarea>
          </div>

          <button type="submit" :disabled="loading" class="btn-primary">
            {{ loading ? "Versturen..." : "Verstuur bericht" }}
          </button>

          <div v-if="responseMessage" :class="['response-message', responseClass]">
            {{ responseMessage }}
          </div>
        </form>

        <!-- contact information -->
        <div class="contact-info">
          <div class="info-block">
            <h3>Direct contact</h3>
            <p>📞 +31 (0)20 123 4567</p>
            <p>📧 info@apothecare.nl</p>
            <p>📍 Hoofdstraat 123, 1234 AB Amsterdam</p>
          </div>

          <div class="info-block">
            <h3>Openingstijden</h3>
            <p>Maandag - Vrijdag: 08:00 - 18:00</p>
            <p>Zaterdag: 09:00 - 17:00</p>
            <p>Zondag: Gesloten</p>
          </div>

          <div class="info-block chatbot">
            <h3>24/7 Chatbot Assistent</h3>
            <p>Onze virtuele assistent staat 24/7 klaar om uw vragen te beantwoorden.</p>
            <button class="btn-secondary">Start chat</button>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<style scoped>
/* main styles */
.contact-page { 
  font-family: Arial, sans-serif; 
  color: #333; 
  padding: 20px 0;
}

h1 { 
  margin-bottom: 10px; 
  font-weight: bold; 
  font-size: 36px;
  color: #3066f6;
}

h2, h3, h4 { 
  margin-bottom: 10px; 
  font-weight: bold; 
}

/* content */
.contact-content { 
  text-align: center; 
  padding: 20px; 
  max-width: 1200px;
  margin: 0 auto;
}

.contact-container {
  display: flex; 
  justify-content: space-between; 
  align-items: flex-start;
  margin: 40px auto 0; 
  gap: 30px;
}

/* form */
.contact-form {
  flex: 1; 
  background: white; 
  padding: 25px; 
  border-radius: 8px;
  display: flex; 
  flex-direction: column; 
  text-align: left;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 6px;
  font-weight: 500;
  color: #333;
}

.form-input {
  width: 100%;
  padding: 12px 15px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
  transition: border-color 0.3s;
}

.form-input:focus {
  border-color: #3066f6;
  outline: none;
}

/* buttons */
.btn-primary {
  width: 100%;
  background-color: #3066f6;
  color: white;
  border: none;
  padding: 12px 20px;
  border-radius: 4px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s;
  margin-top: 10px;
}

.btn-primary:hover {
  background-color: #2050c8;
}

.btn-primary:disabled {
  background-color: #a0aec0;
  cursor: not-allowed;
}

.btn-secondary {
  background-color: #3066f6;
  color: white;
  border: none;
  padding: 8px 15px;
  border-radius: 4px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.3s;
  margin-top: 10px;
}

.btn-secondary:hover {
  background-color: #2050c8;
}

/* contact information */
.contact-info {
  flex: 1; 
  display: flex; 
  flex-direction: column; 
  gap: 20px;
}

.info-block {
  background: white; 
  padding: 20px; 
  border-radius: 8px; 
  text-align: left;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

/* response message styling */
.response-message {
  margin-top: 15px;
  padding: 10px 15px;
  border-radius: 4px;
  text-align: center;
  font-weight: 500;
}

.success {
  background-color: #e6fffa;
  color: #047857;
  border: 1px solid #047857;
}

.error {
  background-color: #ffe5e5;
  color: #e53e3e;
  border: 1px solid #e53e3e;
}

/* Responsive styling */
@media (max-width: 768px) {
  .contact-container {
    flex-direction: column;
  }
  
  .contact-form, .contact-info {
    width: 100%;
  }
  
  h1 {
    font-size: 28px;
  }
}
</style>

<script setup>
import { ref } from "vue";

// Form fields
const name = ref("");
const email = ref("");
const subject = ref("Algemene vraag");
const message = ref("");

// UI state
const responseMessage = ref("");
const responseClass = ref("");
const loading = ref(false);

// Email check
const isValidEmail = (email) => {
  const regex = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
  return regex.test(email);
};

// Submit logic
const submitForm = async () => {
  responseMessage.value = "";
  responseClass.value = "";

  if (!isValidEmail(email.value)) {
    responseMessage.value = "Voer een geldig e-mailadres in.";
    responseClass.value = "error";
    return;
  }

  loading.value = true;

  try {
    const response = await fetch("http://localhost:8000/api/contact.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        name: name.value,
        email: email.value,
        subject: subject.value,
        message: message.value,
      }),
    });

    const data = await response.json();

    if (data.success) {
      responseMessage.value = "Uw bericht is verzonden!";
      responseClass.value = "success";
      // Reset form fields on success
      name.value = "";
      email.value = "";
      subject.value = "Algemene vraag";
      message.value = "";
    } else {
      responseMessage.value = "Fout: " + (data.error || "Onbekend probleem.");
      responseClass.value = "error";
    }
  } catch (error) {
    console.error("Connection error:", error);
    responseMessage.value = "Kan geen verbinding maken met de server. Controleer of de server draait.";
    responseClass.value = "error";
  } finally {
    loading.value = false;
  }
};
</script>