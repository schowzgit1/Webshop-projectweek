<template>
    <div></div>
</template>

<script setup>
import { onMounted } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();

// onMounted(() => {
//   router.push('/medicijnen');
//   router.push('/contact');
// });

onMounted(() => {
    router.replace("/landingpage");
});
</script>
