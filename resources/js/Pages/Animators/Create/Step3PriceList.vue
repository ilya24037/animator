<script setup lang="ts">
import { ref, computed, watch } from 'vue'

const props = defineProps<{ form: any, errors?: Record<string, any> }>()

const search        = ref('')
const showCustom    = ref(false)
const customService = ref('')
const services = ref([
  { id:1, name:'Кинезиотейпирование',           selected:false, price:'', unit:'за услугу', duration:'' },
  { id:2, name:'Тейпирование',                  selected:false, price:'', unit:'за услугу', duration:'' },
  { id:3, name:'Лимфодренажный массаж лица',    selected:false, price:'', unit:'за услугу', duration:'' },
  { id:4, name:'Мануальный массаж',             selected:false, price:'', unit:'за услугу', duration:'' },
  { id:5, name:'Точечный массаж',               selected:false, price:'', unit:'за услугу', duration:'' },
  { id:6, name:'Огненный массаж',               selected:false, price:'', unit:'за услугу', duration:'' },
  { id:7, name:'Стоун-массаж',                  selected:false, price:'', unit:'за услугу', duration:'' },
  { id:8, name:'Юмейхо-терапия',                selected:false, price:'', unit:'за услугу', duration:'' },
  { id:9, name:'Выезд',                         selected:false, price:'', unit:'за услугу', duration:'' },
])

// Если priceItems уже есть (например, редактирование) — восстановить
if (Array.isArray(props.form.priceItems) && props.form.priceItems.length > 0) {
  // Сохраняем уникальные id для кастомных услуг
  let maxId = Math.max(...services.value.map(s => s.id), 10)
  props.form.priceItems.forEach((item: any) => {
    let found = services.value.find(s => s.name === item.name)
    if (found) {
      Object.assign(found, item)
      found.selected = true
    } else {
      maxId++
      services.value.push({
        ...item,
        id: maxId,
        selected: true,
        unit: item.unit || 'за услугу',
        duration: item.duration || ''
      })
    }
  })
}

// Автоматически держим form.priceItems актуальным
watch(
  services,
  () => {
    props.form.priceItems = services.value.filter(s => s.selected)
  },
  { deep: true }
)

const filteredServices = computed(() =>
  services.value.filter(s => s.name.toLowerCase().includes(search.value.toLowerCase()) || s.selected)
)
const selectedCount = computed(() => services.value.filter(s => s.selected).length)

function clearSelected() {
  services.value.forEach(s => { s.selected = false; s.price = ''; s.unit = 'за услугу'; s.duration = '' })
}
function addCustomService() {
  if (!customService.value.trim()) return
  services.value.push({ id: Date.now(), name: customService.value.trim(), selected: true, price: '', unit: 'за услугу', duration: '' })
  customService.value = ''; showCustom.value = false
}
</script>

<template>
  <section>
    <h2 class="text-2xl font-bold mb-4">Прайс-лист</h2>
    <p class="mb-6 text-gray-500">Клиенты увидят ваше объявление при поиске любой услуги из списка.</p>

    <input v-model="search" placeholder="Найти услугу..." class="mb-4 w-full p-2 border rounded" />

    <div class="space-y-2 max-h-96 overflow-y-auto">
      <div
        v-for="(service, idx) in filteredServices"
        :key="service.id"
        class="flex items-center bg-gray-50 rounded-lg px-3 py-2"
      >
        <input type="checkbox" v-model="service.selected" class="mr-3 accent-black" />
        <span class="w-52">{{ service.name }}</span>
        <template v-if="service.selected">
          <input
            v-model.number="service.price" v-error-path="`priceList.priceItems.${idx}.price`"
            type="number"
            min="0"
            placeholder="от"
            :class="[
              'mx-2 w-20 p-1 border rounded',
              props.errors?.[`priceList.priceItems.${idx}.price`] ? 'border-red-500' : 'border-gray-200'
            ]"
          />
          <span class="mr-1">₽</span>
          <select v-model="service.unit" class="mx-2 p-1 border rounded">
            <option value="за услугу">за услугу</option>
            <option value="за час">за час</option>
            <option value="за единицу">за единицу</option>
            <option value="за день">за день</option>
            <option value="за месяц">за месяц</option>
            <option value="за минуту">за минуту</option>
          </select>
          <select v-model="service.duration" class="mx-2 p-1 border rounded">
            <option value="">Время</option>
            <option>15 мин.</option>
            <option>30 мин.</option>
            <option>45 мин.</option>
            <option>1 ч</option>
            <option>1 ч 15 мин.</option>
            <option>1 ч 30 мин.</option>
            <option>2 ч</option>
            <option>3 ч</option>
            <option>4 ч</option>
            <option>5 ч</option>
            <option>6 ч</option>
          </select>
        </template>
      </div>
    </div>

    <!-- Ошибка по всему прайсу -->
    <span v-if="props.errors?.['priceList.priceItems']" class="text-xs text-red-500 block mt-2">
      {{ props.errors['priceList.priceItems'] }}
    </span>

    <div class="mt-4">
      <button @click="showCustom=!showCustom" class="text-blue-600 underline text-sm">{{ showCustom ? 'Скрыть' : 'Свои услуги' }}</button>
      <div v-if="showCustom" class="mt-2 flex items-center gap-2">
        <input v-model="customService" placeholder="Ваша услуга" class="p-1 border rounded" />
        <button @click="addCustomService" class="bg-green-500 text-white rounded px-3 py-1 hover:bg-green-600">Добавить</button>
      </div>
    </div>

    <div class="flex justify-between items-center mt-6">
      <div>
        <span class="font-bold">Вы добавили {{ selectedCount }} услуг</span>
        <button @click="clearSelected" class="ml-3 text-red-500 underline text-sm">Очистить список</button>
      </div>
    </div>
  </section>
</template>