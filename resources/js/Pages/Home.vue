<script setup>
import Cards from '@/Components/Cards.vue'
import Map from '@/Components/Map.vue'
import Filters from '@/Components/Filters.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import SidebarColumn from '@/Components/SidebarColumn.vue'

defineProps({
  cards: Array,
  filters: Object,
  cities: {
    type: Array,
    default: () => []
  }
})
defineOptions({ layout: AppLayout })
</script>

<template>
  <div class="flex gap-8">
    <!-- Левая колонка — теперь через SidebarColumn -->
    <aside class="w-[300px] shrink-0">
      <SidebarColumn>
        <Filters :filters="filters" :cities="cities" />
      </SidebarColumn>
    </aside>
    <!-- Правая колонка — карта и карточки -->
    <section class="flex-1 space-y-6">
      <div class="rounded-xl shadow mb-2 flex items-center justify-center min-h-[200px] bg-white">
        <Map :cards="cards" />
      </div>
      <Cards :cards="cards" />
    </section>
  </div>
</template>

<style scoped>
/* ничего не менял */
</style>
