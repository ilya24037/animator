<template>
  <div class="bg-white rounded-2xl shadow-sm p-6">
    <h3 class="text-lg font-semibold mb-4">Предпросмотр объявления</h3>
    
    <div class="space-y-4">
      <!-- Фото -->
      <div class="aspect-video bg-gray-100 rounded-lg overflow-hidden">
        <img 
          v-if="mainPhoto" 
          :src="mainPhoto" 
          alt="Главное фото"
          class="w-full h-full object-cover"
        >
        <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
          <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
        </div>
      </div>
      
      <!-- Заголовок -->
      <div>
        <h4 class="font-semibold text-lg">
          {{ data.name || 'Название объявления' }}
        </h4>
        <p class="text-2xl font-bold text-blue-600 mt-1">
          {{ formatPrice(data.price) }}
        </p>
      </div>
      
      <!-- Описание -->
      <p class="text-gray-600 text-sm line-clamp-3">
        {{ data.description || 'Описание вашей услуги появится здесь' }}
      </p>
      
      <!-- Метаданные -->
      <div class="flex items-center gap-4 text-sm text-gray-500">
        <div class="flex items-center gap-1">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          <span>{{ cityName }}</span>
        </div>
        
        <div class="flex items-center gap-1">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>Сегодня</span>
        </div>
      </div>
      
      <!-- Услуги -->
      <div v-if="data.services?.length > 0" class="border-t pt-4">
        <h5 class="font-medium mb-2">Услуги:</h5>
        <div class="space-y-1">
          <div 
            v-for="(service, index) in data.services.slice(0, 3)" 
            :key="index"
            class="text-sm text-gray-600"
          >
            • {{ service.name || service }}
          </div>
          <div v-if="data.services.length > 3" class="text-sm text-gray-400">
            и еще {{ data.services.length - 3 }}...
          </div>
        </div>
      </div>
      
      <!-- Кнопки действий -->
      <div class="border-t pt-4 space-y-2">
        <button class="w-full py-2 bg-green-500 text-white rounded-lg font-medium">
          Позвонить
        </button>
        <button class="w-full py-2 border border-gray-300 rounded-lg font-medium">
          Написать
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  data: {
    type: Object,
    default: () => ({})
  }
})

const mainPhoto = computed(() => {
  if (props.data.photos?.length > 0) {
    const firstPhoto = props.data.photos[0]
    if (firstPhoto.preview) return firstPhoto.preview
    if (firstPhoto.file) return URL.createObjectURL(firstPhoto.file)
  }
  return null
})

const cityName = computed(() => {
  // Здесь можно подключить справочник городов
  const cities = {
    1: 'Москва',
    2: 'Санкт-Петербург',
    3: 'Казань',
    // ...
  }
  return cities[props.data.city_id] || 'Не указан'
})

function formatPrice(price) {
  if (!price) return 'Цена не указана'
  return new Intl.NumberFormat('ru-RU', {
    style: 'currency',
    currency: 'RUB',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(price)
}
</script>

<style scoped>
.line-clamp-3 {
  overflow: hidden;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 3;
}
</style>