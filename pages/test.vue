<template>
  <div class="test-container">
    <h1>PHP Server Test</h1>
    
    <div v-if="loading">Loading...</div>
    
    <div v-else-if="error" class="error">
      <h2>Error:</h2>
      <pre>{{ error }}</pre>
    </div>
    
    <div v-else-if="result" class="result">
      <h2>Server Response:</h2>
      <pre>{{ JSON.stringify(result, null, 2) }}</pre>
    </div>
    
    <div class="actions">
      <button @click="testServer" :disabled="loading">Test PHP Server</button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const loading = ref(false);
const error = ref(null);
const result = ref(null);

async function testServer() {
  loading.value = true;
  error.value = null;
  result.value = null;
  
  try {
    const response = await fetch('/server/test.php');
    console.log('Raw response:', response);
    
    if (!response.ok) {
      throw new Error(`Server responded with status: ${response.status}`);
    }
    
    const contentType = response.headers.get('content-type');
    console.log('Content type:', contentType);
    
    if (contentType && contentType.includes('application/json')) {
      result.value = await response.json();
    } else {
      // If not JSON, get as text to see what's being returned
      const text = await response.text();
      console.log('Response text:', text);
      error.value = `Expected JSON but got: ${text.substring(0, 100)}...`;
    }
  } catch (err) {
    console.error('Test failed:', err);
    error.value = err.message;
  } finally {
    loading.value = false;
  }
}

// Run the test when the page loads
onMounted(() => {
  testServer();
});
</script>

<style scoped>
.test-container {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

.error {
  background-color: #ffebee;
  padding: 15px;
  border-radius: 4px;
  margin: 20px 0;
}

.result {
  background-color: #e8f5e9;
  padding: 15px;
  border-radius: 4px;
  margin: 20px 0;
}

pre {
  white-space: pre-wrap;
  word-break: break-word;
  background-color: #f5f5f5;
  padding: 10px;
  border-radius: 4px;
}

.actions {
  margin-top: 20px;
}

button {
  background-color: #3066f6;
  color: white;
  border: none;
  padding: 10px 15px;
  border-radius: 4px;
  cursor: pointer;
}

button:hover {
  background-color: #2555d5;
}

button:disabled {
  background-color: #a0b5e8;
  cursor: not-allowed;
}
</style> 