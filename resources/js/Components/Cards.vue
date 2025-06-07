<template>
  <div class="cards-grid">
    <Card
      v-for="(card, index) in validCards"
      :key="card.id || `card-${index}`"
      :card="card"
    />
  </div>
</template>

<script setup>
import { computed } from 'vue';
import Card from './Card.vue';

const props = defineProps({
  cards: {
    type: Array,
    default: () => [],
    validator(value) {
      // Проверяем, что это массив или null
      return Array.isArray(value) || value === null || value === undefined;
    }
  },
});

// Фильтруем только валидные карточки
const validCards = computed(() => {
  if (!props.cards || !Array.isArray(props.cards)) {
    return [];
  }
  return props.cards.filter(card => card !== null && card !== undefined);
});
</script>

<style scoped>
.cards-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 16px;
}
</style>