<template>
  <div class="fixed bottom-4 right-4 z-50">
    <button
      @click="toggleChat"
      class="bg-indigo-600 text-white p-4 rounded-full shadow-lg hover:bg-indigo-700 transition-colors"
    >
      <svg
        v-if="!isOpen"
        class="w-6 h-6"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"
        />
      </svg>
      <svg
        v-else
        class="w-6 h-6"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M6 18L18 6M6 6l12 12"
        />
      </svg>
    </button>

    <div
      v-if="isOpen"
      class="absolute bottom-16 right-0 w-96 bg-white rounded-lg shadow-xl"
    >
      <div class="p-4 border-b">
        <h3 class="text-lg font-semibold">AI Assistent</h3>
        <p class="text-sm text-gray-500">
          Ik ben een AI-assistent. Hoe kan ik je helpen?
        </p>
      </div>

      <div class="h-96 overflow-y-auto p-4">
        <div
          v-for="(message, index) in messages"
          :key="index"
          :class="[
            'mb-4',
            message.role === 'user'
              ? 'text-right'
              : 'text-left bg-gray-100 rounded-lg p-3',
          ]"
        >
          <p class="text-sm">{{ message.content }}</p>
        </div>
        <div v-if="isLoading" class="text-center">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
        </div>
      </div>

      <div class="p-4 border-t">
        <form @submit.prevent="sendMessage" class="flex gap-2">
          <input
            v-model="newMessage"
            type="text"
            placeholder="Type je bericht..."
            class="flex-1 p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
          />
          <button
            type="submit"
            class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors"
          >
            Verstuur
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      isOpen: false,
      messages: [],
      newMessage: "",
      isLoading: false,
    };
  },
  methods: {
    toggleChat() {
      this.isOpen = !this.isOpen;
      if (this.isOpen && this.messages.length === 0) {
        this.messages.push({
          role: "assistant",
          content:
            "Hallo! Ik ben een AI-assistent. Ik kan je helpen met vragen over onze producten, bestellingen of andere informatie. Wat kan ik voor je doen?",
        });
      }
    },
    async sendMessage() {
      if (!this.newMessage.trim()) return;

      this.messages.push({
        role: "user",
        content: this.newMessage,
      });

      const userMessage = this.newMessage;
      this.newMessage = "";
      this.isLoading = true;

      try {
        const response = await fetch("http://localhost/server/api/chat.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            message: userMessage,
          }),
        });

        const data = await response.json();

        if (data.success) {
          this.messages.push({
            role: "assistant",
            content: data.response,
          });
        } else {
          this.messages.push({
            role: "assistant",
            content: "Sorry, er is een fout opgetreden. Probeer het later opnieuw.",
          });
        }
      } catch (error) {
        this.messages.push({
          role: "assistant",
          content: "Sorry, er is een fout opgetreden. Probeer het later opnieuw.",
        });
      } finally {
        this.isLoading = false;
      }
    },
  },
};
</script>

<style scoped>
.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
</style> 