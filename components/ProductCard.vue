<template>
  <div class="product-card">
    <div class="product-image">
      <img :src="product.image" :alt="product.name">
    </div>
    <div class="product-info">
      <h3>{{ product.name }}</h3>
      <p class="product-description">{{ product.description }}</p>
      <p class="product-dosage">Dosering: {{ product.dosage }}</p>
      <p class="product-quantity">{{ product.quantity }}</p>
      <p class="product-price">€{{ product.price.toFixed(2) }}</p>
      <button class="add-to-cart-btn" @click="$emit('add-to-cart', product)">
        In winkelwagen
      </button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  product: {
    type: Object,
    required: true
  }
});

defineEmits(['add-to-cart']);
</script>

<style scoped>
.product-card {
  border: 1px solid #eee;
  border-radius: 10px;
  overflow: hidden;
  transition: transform 0.3s, box-shadow 0.3s;
  height: 100%;
  display: flex;
  flex-direction: column;
  background-color: white;
}

.product-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.product-image {
  height: 200px;
  overflow: hidden;
}

.product-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s;
}

.product-card:hover .product-image img {
  transform: scale(1.05);
}

.product-info {
  padding: 20px;
  display: flex;
  flex-direction: column;
  flex-grow: 1;
}

h3 {
  font-size: 18px;
  margin-bottom: 10px;
  color: #333;
}

.product-description {
  color: #666;
  margin-bottom: 10px;
  font-size: 14px;
}

.product-dosage, .product-quantity {
  font-size: 14px;
  color: #666;
  margin-bottom: 5px;
}

.product-price {
  font-size: 20px;
  font-weight: bold;
  color: #3066f6;
  margin: 15px 0;
  margin-top: auto;
}

.add-to-cart-btn {
  width: 100%;
  padding: 12px;
  background-color: #3066f6;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 16px;
  transition: background-color 0.3s;
}

.add-to-cart-btn:hover {
  background-color: #2753c9;
}
</style> 