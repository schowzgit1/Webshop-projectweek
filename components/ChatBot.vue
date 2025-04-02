<template>
  <div class="chat-container" :class="{ 'chat-open': isChatOpen }">
    <div class="chat-header" @click="toggleChat">
      <h3>ApotheCare Hulp</h3>
      <button class="close-btn" v-if="isChatOpen" @click.stop="toggleChat">×</button>
    </div>
    
    <div class="chat-body" v-if="isChatOpen">
      <div class="chat-messages" ref="messagesContainer">
        <div 
          v-for="(message, index) in messages" 
          :key="index" 
          class="message" 
          :class="message.sender"
          @click="message.action ? handleActionClick(message.action) : null"
          :style="{ cursor: message.action ? 'pointer' : 'default' }"
        >
          <div class="message-content">
            <p>{{ message.text }}</p>
          </div>
        </div>
      </div>
      
      <div class="chat-input">
        <input 
          type="text" 
          v-model="userInput" 
          placeholder="Typ uw vraag..." 
          @keyup.enter="sendMessage"
        />
        <button @click="sendMessage">
          <span>➤</span>
        </button>
      </div>
    </div>
    
    <button v-if="!isChatOpen" class="chat-button" @click="toggleChat">
      <span>💬</span>
    </button>
  </div>
</template>

<script setup>
import { ref, watch, nextTick } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const isChatOpen = ref(false);
const userInput = ref('');
const messages = ref([
  { text: 'Hallo! Hoe kan ik u helpen bij het vinden van medicijnen of gezondheidsproducten?', sender: 'bot' }
]);
const messagesContainer = ref(null);

// Predefined responses based on keywords with navigation links
const responses = {
  'ibuprofen': {
    text: 'Ibuprofen 400mg is beschikbaar in onze pijnstillers categorie. U kunt deze hier bekijken of klik op de link om er direct naartoe te gaan.',
    action: {
      type: 'navigate',
      path: '/medicijnen',
      query: { category: 'pijnstillers' }
    }
  },
  'paracetamol': {
    text: 'Paracetamol 500mg is beschikbaar in onze pijnstillers categorie. U kunt deze hier bekijken of klik op de link om er direct naartoe te gaan.',
    action: {
      type: 'navigate',
      path: '/medicijnen',
      query: { category: 'pijnstillers' }
    }
  },
  'vitamine': {
    text: 'We hebben diverse vitamines, waaronder Vitamine C en multivitamines. Bekijk ons assortiment hier door op de link te klikken.',
    action: {
      type: 'navigate',
      path: '/medicijnen',
      query: { category: 'vitamines' }
    }
  },
  'vitamine c': {
    text: 'Vitamine C 1000mg is beschikbaar in onze vitamines categorie. Klik op de link om direct naar dit product te gaan.',
    action: {
      type: 'navigate',
      path: '/medicijnen',
      query: { search: 'Vitamine C' }
    }
  },
  'multivitamine': {
    text: 'Ons Multivitamine Complex vindt u in onze vitamines categorie. Klik op de link om direct naar dit product te gaan.',
    action: {
      type: 'navigate',
      path: '/medicijnen',
      query: { search: 'Multivitamine' }
    }
  },
  'pijn': {
    text: 'Voor pijnstillers zoals Paracetamol en Ibuprofen kunt u in onze pijnstillers categorie kijken. Klik op de link om er direct naartoe te gaan.',
    action: {
      type: 'navigate',
      path: '/medicijnen',
      query: { category: 'pijnstillers' }
    }
  },
  'koorts': {
    text: 'Bij koorts worden vaak Paracetamol of Ibuprofen aangeraden. Deze vindt u in onze pijnstillers categorie. Klik op de link om er direct naartoe te gaan.',
    action: {
      type: 'navigate',
      path: '/medicijnen',
      query: { category: 'pijnstillers' }
    }
  },
  'verkoudheid': {
    text: 'Voor verkoudheidsklachten is Vitamine C vaak aan te raden. Bekijk onze vitamines door op de link te klikken.',
    action: {
      type: 'navigate',
      path: '/medicijnen',
      query: { category: 'vitamines' }
    }
  },
  'bestellen': {
    text: 'U kunt medicijnen bestellen door ze toe te voegen aan uw winkelwagen en vervolgens af te rekenen. Heeft u hulp nodig bij het bestellen?',
    action: null
  },
  'betalen': {
    text: 'Wij accepteren diverse betaalmethoden, waaronder iDEAL, creditcard en PayPal.',
    action: null
  },
  'levering': {
    text: 'Bestellingen worden doorgaans binnen 1-2 werkdagen geleverd. Voor bestellingen boven €20 is de verzending gratis.',
    action: null
  },
  'contact': {
    text: 'U kunt contact met ons opnemen via info@apothecare.nl of telefonisch op 020-123-4567 tijdens kantooruren.',
    action: null
  },
  'help': {
    text: 'Ik kan u helpen met: het zoeken van medicijnen (paracetamol, ibuprofen, vitamines), informatie over bestellen, betalen, levering of contact. Wat wilt u weten?',
    action: null
  },
  'medicijnen': {
    text: 'U kunt al onze medicijnen bekijken door op de link te klikken.',
    action: {
      type: 'navigate',
      path: '/medicijnen'
    }
  }
};

// Default fallback responses
const fallbackResponses = [
  'Dat is een goede vraag. Kan ik u helpen bij het vinden van specifieke medicijnen zoals paracetamol of ibuprofen?',
  'Excuses, ik begrijp uw vraag niet helemaal. Kunt u specifieker zijn over welk medicijn u zoekt? Bijvoorbeeld "paracetamol" of "vitamine c"?',
  'Ik kan u helpen bij het vinden van medicijnen, informatie over bestellingen of algemene vragen over gezondheidsproducten. Typ "help" voor meer informatie.',
  'Ik wil u graag helpen. Kunt u uw vraag anders formuleren? U kunt bijvoorbeeld vragen naar "paracetamol", "ibuprofen" of "vitamines".'
];

const toggleChat = () => {
  isChatOpen.value = !isChatOpen.value;
};

const sendMessage = async () => {
  if (!userInput.value.trim()) return;
  
  // Add user message
  messages.value.push({ text: userInput.value, sender: 'user' });
  
  // Generate response based on user input
  const response = generateResponse(userInput.value);
  
  // Clear input
  userInput.value = '';
  
  // Scroll to bottom
  await nextTick();
  scrollToBottom();
  
  // Add some delay to simulate thinking
  setTimeout(() => {
    // Add bot's text response
    messages.value.push({ 
      text: response.text, 
      sender: 'bot',
      action: response.action
    });
    
    // If there's a navigation action, add a clickable link
    if (response.action && response.action.type === 'navigate') {
      const linkText = 'Klik hier om direct naar de pagina te gaan';
      messages.value.push({
        text: linkText,
        sender: 'bot-link',
        action: response.action
      });
    }
    
    nextTick(() => scrollToBottom());
  }, 500);
};

const generateResponse = (input) => {
  const lowercaseInput = input.toLowerCase();
  
  // Check for keyword matches
  for (const keyword in responses) {
    if (lowercaseInput.includes(keyword)) {
      return responses[keyword];
    }
  }
  
  // Use fallback response if no keywords match
  const fallbackText = fallbackResponses[Math.floor(Math.random() * fallbackResponses.length)];
  return { text: fallbackText, action: null };
};

const handleActionClick = (action) => {
  if (action && action.type === 'navigate') {
    // Send a message to confirm navigation
    messages.value.push({ 
      text: 'Ik navigeer u nu naar de juiste pagina...', 
      sender: 'bot' 
    });
    
    // Wait a moment and then navigate
    setTimeout(() => {
      router.push({
        path: action.path,
        query: action.query || {}
      });
      
      // Close the chat after navigation
      isChatOpen.value = false;
    }, 1000);
  }
};

const scrollToBottom = () => {
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
  }
};

// Scroll to bottom when chat is opened or messages change
watch(isChatOpen, (newValue) => {
  if (newValue) {
    nextTick(() => scrollToBottom());
  }
});

watch(messages, () => {
  nextTick(() => scrollToBottom());
}, { deep: true });
</script>

<style scoped>
.chat-container {
  position: fixed;
  bottom: 20px;
  right: 20px;
  z-index: 1000;
}

.chat-button {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background-color: #3066f6;
  color: white;
  border: none;
  font-size: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
  transition: transform 0.3s ease;
}

.chat-button:hover {
  transform: scale(1.05);
}

.chat-open {
  width: 350px;
  height: 450px;
  border-radius: 10px;
  background-color: white;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.chat-header {
  background-color: #3066f6;
  color: white;
  padding: 15px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  cursor: pointer;
}

.chat-header h3 {
  margin: 0;
  font-size: 16px;
}

.close-btn {
  background: none;
  border: none;
  color: white;
  font-size: 20px;
  cursor: pointer;
}

.chat-body {
  flex: 1;
  display: flex;
  flex-direction: column;
  height: calc(100% - 56px);
}

.chat-messages {
  flex: 1;
  padding: 15px;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.message {
  max-width: 80%;
  padding: 10px 15px;
  border-radius: 18px;
  margin-bottom: 5px;
  word-wrap: break-word;
}

.message.user {
  align-self: flex-end;
  background-color: #3066f6;
  color: white;
  border-bottom-right-radius: 5px;
}

.message.bot {
  align-self: flex-start;
  background-color: #f0f0f0;
  color: #333;
  border-bottom-left-radius: 5px;
}

.message.bot-link {
  align-self: flex-start;
  background-color: #e5f2ff;
  color: #3066f6;
  border: 1px solid #3066f6;
  border-bottom-left-radius: 5px;
  cursor: pointer;
  font-weight: bold;
}

.message.bot-link:hover {
  background-color: #d0e8ff;
}

.chat-input {
  padding: 10px;
  border-top: 1px solid #eee;
  display: flex;
}

.chat-input input {
  flex: 1;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 20px;
  outline: none;
}

.chat-input button {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  border: none;
  background-color: #3066f6;
  color: white;
  margin-left: 10px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}

@media (max-width: 480px) {
  .chat-open {
    width: 90vw;
    height: 70vh;
    bottom: 10px;
    right: 10px;
  }
}
</style> 