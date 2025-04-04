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

    <label>Naam</label>
    <input v-model="name" type="text" required />

    <label>E-mail</label>
    <input v-model="email" type="email" required />

    <label>Onderwerp</label>
    <select v-model="subject">
      <option>Algemene vraag</option>
      <option>Support</option>
      <option>Feedback</option>
    </select>

    <label>Bericht</label>
    <textarea v-model="message" required></textarea>

    <button type="submit" :disabled="loading">
      {{ loading ? "Versturen..." : "Verstuur bericht" }}
    </button>

    <p v-if="responseMessage" :class="responseClass">{{ responseMessage }}</p>
  </form>

        <!-- contact information -->
        <div class="contact-info">
          <div class="info-block">
            <h3>Direct contact</h3>
            <p>📞 +31 (0)20 123 4567</p>
            <p>📧 info@smartapotheek.nl</p>
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
            <button>Start chat</button>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<style scoped>
/* main styles */
.contact-page { font-family: Arial, sans-serif; color: #333; }
h1 { margin-bottom: 10px; font-weight: bold; font-size: 40px;}
h2, h3, h4 { margin-bottom: 10px; font-weight: bold; }



/* content */
.contact-content { text-align: center; padding: 40px; }
.contact-container {
  display: flex; justify-content: space-between; align-items: flex-start;
  max-width: 900px; margin: 0 auto; gap: 30px;
}

/* form */
.contact-form {
  flex: 1; background: #f8f9fa; padding: 20px; border-radius: 8px;
  display: flex; flex-direction: column; text-align: left;
}
.contact-form input, .contact-form select, .contact-form textarea {
  width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc;
  border-radius: 5px;
}
.contact-form button {
  background: #236bde; color: white; padding: 10px; border: none;
  border-radius: 5px; cursor: pointer;
}

/* contact information */
.contact-info {
  flex: 1; display: flex; flex-direction: column; gap: 20px;
}
.info-block {
  background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: left;
}
.chatbot button {
  background: #236bde; color: white; padding: 5px 15px; border: none;
  border-radius: 5px; cursor: pointer; margin-top: 10px;
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

  const { data, error } = await useFetch("http://localhost/webshop-projectweek/server/api/contact.php", {
    method: "POST",
    body: {
      name: name.value,
      email: email.value,
      subject: subject.value,
      message: message.value,
    },
  });

  if (error.value) {
    responseMessage.value = "Kan geen verbinding maken met de server. Server is off";
    responseClass.value = "error";
  } else if (data.value?.success) {
    responseMessage.value = "Uw bericht is verzonden!";
    responseClass.value = "success";
    name.value = "";
    email.value = "";
    message.value = "";
  } else {
    responseMessage.value = "Fout: " + (data.value?.error || "Onbekend probleem.");
    responseClass.value = "error";
  }

  loading.value = false;
};
</script>