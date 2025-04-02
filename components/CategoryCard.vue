<template>
  <div class="category-card" @click="navigateToCategory">
    <div class="category-icon">{{ icon }}</div>
    <p>{{ title }}</p>
  </div>
</template>

<script setup>
const props = defineProps({
  icon: {
    type: String,
    default: '💊'
  },
  title: {
    type: String,
    required: true
  },
  categoryId: {
    type: String,
    default: ''
  }
});

const navigateToCategory = () => {
  // Could be implemented with router navigation
  console.log(`Navigate to category: ${props.title}`);
};
</script>

<style scoped>
.category-card {
  flex: 1;
  background-color: white;
  border-radius: 8px;
  padding: 15px;
  text-align: center;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s;
  cursor: pointer;
}

.category-card:hover {
  transform: translateY(-5px);
}

.category-icon {
  font-size: 24px;
  margin-bottom: 10px;
}

.category-card p {
  margin: 0;
  font-weight: 500;
}
</style> 