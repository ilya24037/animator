<template>
  <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold">Выберите город</h2>
        <button @click="$emit('close')" class="text-gray-400 hover:text-black">✕</button>
      </div>

      <input
        v-model="search"
        type="text"
        placeholder="Начните вводить город..."
        class="w-full border px-4 py-2 rounded-md mb-4 text-sm outline-none"
      />

      <ul class="space-y-2 max-h-[200px] overflow-y-auto">
        <li
          v-for="city in filtered"
          :key="city"
          @click="selectCity(city)"
          class="cursor-pointer px-4 py-2 rounded hover:bg-blue-50 text-sm"
        >
          {{ city }}
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useLocationStore } from '@/Stores/useLocationStore'

import { router } from '@inertiajs/vue3'
const emit = defineEmits(['close', 'select'])
const store = useLocationStore()

const search = ref('')
const selectedCity = ref(localStorage.getItem('selectedCity') || '')

const cities = [
  'Москва', 'Санкт-Петербург', 'Казань', 'Екатеринбург', 'Новосибирск',
  'Краснодар', 'Нижний Новгород', 'Ростов-на-Дону', 'Челябинск', 'Пермь', 'Самара'
]

const filtered = computed(() => {
  return cities.filter(c => c.toLowerCase().includes(search.value.toLowerCase()))
})

const selectCity = (city) => {
  localStorage.setItem('selectedCity', city)
  selectedCity.value = city
  store.setCity(city)
  emit('select', city)
  router.get('/', { city }, { preserveScroll: true, preserveState: true })
}

onMounted(() => {
  if (!selectedCity.value) {
    fetch('https://ipapi.co/json')
      .then(res => res.json())
      .then(data => {
        if (data.city) {
          selectedCity.value = data.city
          store.setCity(data.city)
          localStorage.setItem('selectedCity', data.city)
          emit('select', data.city)
        }
      })
  }
})
</script>

<style scoped>
</style>
