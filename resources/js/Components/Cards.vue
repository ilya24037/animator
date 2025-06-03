<template>
  <div class="cards-grid">
    <Card
      v-for="card in cards"
      :key="card.id"
      :card="card"
    />
  </div>
</template>

<script setup>
import Card from './Card.vue';

defineProps({
  cards: {
    type: Array,
    required: true,
  },
});
</script>

<style scoped>
.cards-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 16px;
}
</style>
