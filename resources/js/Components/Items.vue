<script setup>
import StatusTabs from '@/Components/StatusTabs.vue';
import ItemCard   from '@/Components/ItemCard.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
  items: Object,
  counts: Object,
  currentStatus: String,
});
</script>

<template>
  <div>
    <h1 class="text-2xl font-bold mb-4">Мои объявления</h1>

    <StatusTabs :counts="counts" :current="currentStatus" />

    <div v-if="items.data.length === 0" class="py-20 text-center text-gray-500">
      Пока пусто 🙂
    </div>

    <div v-else class="space-y-4">
      <ItemCard v-for="it in items.data" :key="it.id" :item="it" />
      <Pagination :links="items.links" class="mt-6" />
    </div>
  </div>
</template>