<template>
  <div class="medicines-page">
    <div class="container">
      <h1 class="page-title">Onze Medicijnen</h1>
      
      <div class="search-container">
        <input 
          type="text" 
          placeholder="Zoek medicijnen..." 
          class="search-input" 
          v-model="searchQuery"
          @keyup.enter="searchMedicines"
        />
        <button class="search-btn" @click="searchMedicines">
          <span class="search-icon">🔍</span>
        </button>
      </div>

      <div class="categories">
        <div 
          v-for="category in categories" 
          :key="category.id"
          class="category-card"
          :class="{ active: selectedCategory === category.id }"
          @click="toggleCategory(category.id)"
        >
          <div class="category-icon">{{ category.icon }}</div>
          <p>{{ category.name }}</p>
        </div>
      </div>
      
      <div class="medicines-content">
        <div class="medicines-list" v-if="filteredMedicines.length > 0">
          <ProductCard 
            v-for="medicine in filteredMedicines" 
            :key="medicine.id" 
            :product="medicine"
            @add-to-cart="addToCart"
          />
        </div>
        
        <div class="no-medicines" v-else>
          <p>Geen medicijnen gevonden.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import ProductCard from '~/components/ProductCard.vue';

const route = useRoute();
const router = useRouter();

// Reactive state
const searchQuery = ref('');
const selectedCategory = ref('');
const isLoading = ref(false);

// Categories
const categories = [
  { id: 'pijnstillers', name: 'Pijnstillers' },
  { id: 'vitamines', name: 'Vitamines' },
  { id: 'alle', name: 'Alle Medicijnen' }
];

// Medicines data
const medicines = ref([
  {
    id: 1,
    name: 'Paracetamol 500mg',
    description: 'Pijnstiller en koortsverlagend medicijn',
    category: 'pijnstillers',
    dosage: '500mg',
    quantity: '30 tabletten',
    price: 4.95,
    image: '/assets/images/paracetamol.jpg'
  },
  {
    id: 2,
    name: 'Ibuprofen 400mg',
    description: 'Ontstekingsremmende pijnstiller',
    category: 'pijnstillers',
    dosage: '400mg',
    quantity: '20 tabletten',
    price: 5.95,
    image: '/assets/images/ibuprofen.jpg'
  },
  {
    id: 3,
    name: 'Vitamine C 1000mg',
    description: 'Ondersteunt het immuunsysteem',
    category: 'vitamines',
    dosage: '1000mg',
    quantity: '60 tabletten',
    price: 8.95,
    image: '/assets/images/vitamine c.jpg'
  },
  {
    id: 4,
    name: 'Multivitamine Complex',
    description: 'Complete formule met essentiële vitamines en mineralen',
    category: 'vitamines',
    dosage: 'Divers',
    quantity: '90 tabletten',
    price: 12.95,
    image: '/assets/images/multivitamine.jpg'
  }
]);

// Computed properties
const filteredMedicines = computed(() => {
  let result = medicines.value;
  
  // Filter by category
  if (selectedCategory.value && selectedCategory.value !== 'alle') {
    result = result.filter(med => med.category === selectedCategory.value);
  }
  
  // Filter by search
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(med => {
      return med.name.toLowerCase().includes(query) || 
             med.description.toLowerCase().includes(query);
    });
  }
  
  return result;
});

// Function to add items to cart
const addToCart = (medicine) => {
  alert(`${medicine.name} toegevoegd aan winkelwagen`);
  // Here you would implement actual cart functionality
};

// Function to toggle active category
const toggleCategory = (categoryId) => {
  // If we click the same category, toggle it off (show all)
  if (selectedCategory.value === categoryId) {
    selectedCategory.value = 'alle';
  } else {
    selectedCategory.value = categoryId;
  }
  
  // Update URL without reloading the page
  updateQueryParams();
};

// Function to set active category
const setCategory = (categoryId) => {
  selectedCategory.value = categoryId;
  
  // Update URL without reloading the page
  updateQueryParams();
};

// Function to update the URL query parameters
const updateQueryParams = () => {
  const query = {};
  
  if (searchQuery.value) {
    query.search = searchQuery.value;
  }
  
  if (selectedCategory.value && selectedCategory.value !== 'alle') {
    query.category = selectedCategory.value;
  }
  
  // Replace URL without reloading the page
  router.replace({ query });
};

// Watch for search input changes to update the URL
watch(searchQuery, () => {
  updateQueryParams();
});

// Apply query parameters from URL on mount
onMounted(() => {
  isLoading.value = true;
  
  // Get search query from URL
  if (route.query.search) {
    searchQuery.value = route.query.search.toString();
  }
  
  // Get category from URL
  if (route.query.category) {
    selectedCategory.value = route.query.category.toString();
  } else {
    selectedCategory.value = 'alle';
  }
  
  // Simulate loading
  setTimeout(() => {
    isLoading.value = false;
  }, 500);
});
</script>

<style scoped>
.medicines-page {
  padding: 40px 0;
  min-height: 80vh;
}

.page-title {
  margin-bottom: 30px;
  font-size: 2rem;
  color: #3066f6;
}

.search-container {
  margin-bottom: 30px;
  display: flex;
  width: 100%;
}

.search-input {
  flex: 1;
  padding: 12px 15px;
  border: 1px solid #ddd;
  border-radius: 5px 0 0 5px;
  font-size: 1rem;
}

.search-btn {
  background-color: #3066f6;
  color: white;
  border: none;
  padding: 0 20px;
  border-radius: 0 5px 5px 0;
  cursor: pointer;
  font-size: 1.2rem;
}

.categories {
  display: flex;
  gap: 15px;
  margin-bottom: 30px;
  flex-wrap: wrap;
}

.category-card {
  flex: 1;
  min-width: 150px;
  background-color: white;
  border-radius: 8px;
  padding: 15px;
  text-align: center;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  cursor: pointer;
  transition: all 0.3s ease;
}

.category-card:hover {
  transform: translateY(-5px);
}

.category-card.active {
  background-color: #3066f6;
  color: white;
}

.category-icon {
  font-size: 24px;
  margin-bottom: 10px;
}

.medicines-list {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
}

.medicine-card {
  display: flex;
  flex-direction: column;
  background-color: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease;
}

.medicine-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.medicine-image {
  height: 180px;
  overflow: hidden;
}

.medicine-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.medicine-card:hover .medicine-image img {
  transform: scale(1.05);
}

.medicine-details {
  padding: 15px;
  display: flex;
  flex-direction: column;
  flex: 1;
}

.medicine-details h3 {
  margin: 0 0 10px;
  font-size: 18px;
  color: #333;
}

.medicine-description {
  margin: 0 0 15px;
  color: #666;
  font-size: 14px;
  flex: 1;
}

.medicine-info {
  display: flex;
  justify-content: space-between;
  margin-bottom: 15px;
  font-size: 14px;
}

.medicine-dosage {
  background-color: #e6f7ff;
  color: #1890ff;
  padding: 3px 8px;
  border-radius: 4px;
}

.medicine-quantity {
  color: #666;
}

.medicine-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: auto;
}

.medicine-price {
  font-weight: bold;
  font-size: 18px;
  color: #333;
  margin: 0;
}

.add-to-cart-btn {
  background-color: #3066f6;
  color: white;
  border: none;
  padding: 8px 15px;
  border-radius: 5px;
  cursor: pointer;
  font-size: 14px;
  transition: background-color 0.3s;
}

.add-to-cart-btn:hover {
  background-color: #2555d3;
}

.no-medicines {
  background-color: white;
  border-radius: 8px;
  padding: 30px;
  text-align: center;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  color: #666;
}

@media (max-width: 768px) {
  .categories {
    flex-wrap: wrap;
  }
  
  .category-card {
    flex: 1 1 calc(50% - 10px);
  }
  
  .medicines-list {
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  }
}

@media (max-width: 480px) {
  .category-card {
    flex: 1 1 100%;
  }
  
  .medicines-list {
    grid-template-columns: 1fr;
  }
}
</style> 