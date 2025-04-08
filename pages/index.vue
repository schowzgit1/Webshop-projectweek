<template>
  <div></div>
</template>

<script setup>
import { onMounted } from "vue";
import { useRouter } from "vue-router";
import { ref } from "vue";

const router = useRouter();
const selectedCategory = ref('');
const filteredProducts = ref([]);
const products = ref([
  {
    id: 1,
    name: 'Vitamine C 1000mg',
    price: 19.99,
    description: 'Hoogwaardige vitamine C supplement voor een sterke weerstand',
    category: 'vitamine',
    image: '/images/vitamine-c.jpg'
  },
  {
    id: 2,
    name: 'Magnesium Complex',
    price: 24.99,
    description: 'Essentieel mineraal voor spieren en zenuwen',
    category: 'mineraal',
    image: '/images/magnesium.jpg'
  },
  {
    id: 3,
    name: 'Visolie Omega-3',
    price: 29.99,
    description: 'Natuurlijke omega-3 vetzuren voor hart en hersenen',
    category: 'supplement',
    image: '/images/visolie.jpg'
  },
  {
    id: 4,
    name: 'Vitamine D3',
    price: 15.99,
    description: 'Belangrijk voor sterke botten en immuunsysteem',
    category: 'vitamine',
    image: '/images/vitamine-d.jpg'
  },
  {
    id: 5,
    name: 'Zink Complex',
    price: 18.99,
    description: 'Essentieel mineraal voor huid en immuunsysteem',
    category: 'mineraal',
    image: '/images/zink.jpg'
  },
  {
    id: 6,
    name: 'Probiotica Complex',
    price: 34.99,
    description: 'Bevordert een gezonde darmflora',
    category: 'supplement',
    image: '/images/probiotica.jpg'
  }
]);

// Initialize filtered products with all products
filteredProducts.value = products.value;

const addToCart = (product) => {
  // Get current cart from localStorage or initialize empty array
  const cart = JSON.parse(localStorage.getItem('cart') || '[]');
  
  // Add product to cart
  cart.push(product);
  
  // Save updated cart to localStorage
  localStorage.setItem('cart', JSON.stringify(cart));
  
  // Show success message
  alert(`${product.name} is toegevoegd aan je winkelwagen!`);
};

// Direct redirect on component creation
onMounted(() => {
  router.replace('/landingpage');
});

const handleCategoryClick = (category) => {
  selectedCategory.value = category;
  // Filter products based on category
  if (category === 'all') {
    filteredProducts.value = products.value;
  } else {
    filteredProducts.value = products.value.filter(product => 
      product.category.toLowerCase() === category.toLowerCase()
    );
  }
  // Scroll to products section
  const productsSection = document.getElementById('products');
  if (productsSection) {
    productsSection.scrollIntoView({ behavior: 'smooth' });
  }
};
</script>

<style scoped>
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

.hero {
  text-align: center;
  padding: 60px 0;
  background-color: #f5f5f5;
  margin-bottom: 40px;
}

.categories {
  margin-bottom: 40px;
}

.category-buttons {
  display: flex;
  justify-content: center;
  gap: 20px;
  margin-top: 20px;
}

.category-buttons button {
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  background-color: #f0f0f0;
  cursor: pointer;
  transition: all 0.3s ease;
}

.category-buttons button.active {
  background-color: #4CAF50;
  color: white;
}

.product-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 30px;
  margin-top: 30px;
}

.product-card {
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 15px;
  text-align: center;
  transition: transform 0.3s ease;
}

.product-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.product-card img {
  width: 100%;
  height: 200px;
  object-fit: cover;
  border-radius: 4px;
}

.product-card h3 {
  margin: 15px 0;
  font-size: 1.2em;
}

.price {
  color: #4CAF50;
  font-weight: bold;
  font-size: 1.1em;
  margin: 10px 0;
}

.description {
  color: #666;
  margin-bottom: 15px;
}

.add-to-cart {
  background-color: #4CAF50;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.add-to-cart:hover {
  background-color: #45a049;
}
</style>
