<!-- resources/js/Pages/Catalog.vue -->
<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import Cards from '@/Components/Cards.vue'
defineProps({ cards: Array })

defineOptions({
  layout: AppLayout,
})
</script>

<template>
  <div class="py-6">
    <Cards :cards="cards" />
  </div>
</template>
