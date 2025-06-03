<template>
  <div class="space-y-6 text-sm w-full">
    <!-- Переключатели -->
    <div class="space-y-4">
      <div class="flex items-center justify-between">
        <span class="font-medium">Работает сейчас</span>
        <button @click="isOnline = !isOnline"
          :class="['relative w-11 h-6 rounded-full transition-colors duration-300', isOnline ? 'bg-blue-600' : 'bg-gray-300']">
          <span
            :class="['absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow-md transform transition-transform duration-300', isOnline ? 'translate-x-5' : '']" />
        </button>
      </div>

      <div class="flex items-center justify-between">
        <span class="font-medium">Проверенные исполнители</span>
        <button @click="isVerified = !isVerified"
          :class="['relative w-11 h-6 rounded-full transition-colors duration-300', isVerified ? 'bg-blue-600' : 'bg-gray-300']">
          <span
            :class="['absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow-md transform transition-transform duration-300', isVerified ? 'translate-x-5' : '']" />
        </button>
      </div>

      <div class="space-y-2">
        <span class="font-medium">Кто оказывает услуги</span>
        <label class="flex items-center gap-2">
          <input type="checkbox" v-model="isPrivate" />
          Частный исполнитель
        </label>
        <label class="flex items-center gap-2">
          <input type="checkbox" v-model="isCompany" />
          Компания
        </label>
      </div>
    </div>

    <!-- Слайдер цены -->
    <div>
      <div class="font-medium mb-2">Цена за час</div>
      <VueSlider v-model="priceRange" :min="0" :max="30000" :step="100" :dot-size="20" :height="6" :tooltip="false" class="mb-4" />
      <div class="flex justify-between gap-2">
        <input type="number" v-model.number="priceRange[0]" class="w-24 border border-gray-300 rounded px-2 py-1 text-center shadow-sm" />
        <input type="number" v-model.number="priceRange[1]" class="w-24 border border-gray-300 rounded px-2 py-1 text-center shadow-sm" />
      </div>
    </div>

    <!-- Кнопка -->
    <div>
      <button class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded" @click="applyFilters">
        Применить фильтры
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import VueSlider from '@vueform/slider'
import '@vueform/slider/themes/default.css'

const isOnline = ref(false)
const isVerified = ref(false)
const isPrivate = ref(false)
const isCompany = ref(false)
const priceRange = ref([0, 30000])

const applyFilters = () => {
  const filters = {
    is_online: isOnline.value,
    is_verified: isVerified.value,
    type: isPrivate.value && !isCompany.value ? 'private'
         : isCompany.value && !isPrivate.value ? 'company'
         : null,
    min_price: priceRange.value[0],
    max_price: priceRange.value[1],
  }

  router.get('/', filters, {
    preserveScroll: true,
    preserveState: true,
  })
}
</script>
