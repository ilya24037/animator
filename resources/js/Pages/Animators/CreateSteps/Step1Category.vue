<template>
  <div class="space-y-6">
    <div>
      <h2 class="text-2xl font-bold mb-2">Выберите категорию</h2>
      <p class="text-gray-600">Это поможет покупателям быстрее найти ваше объявление</p>
    </div>

    <!-- Поиск по категориям -->
    <div class="relative">
      <input
        v-model="searchQuery"
        type="text"
        placeholder="Поиск по категориям..."
        class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
      >
      <svg class="absolute right-3 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
      </svg>
    </div>

    <!-- Категории -->
    <div v-if="!selectedCategory" class="grid grid-cols-2 gap-4">
      <div
        v-for="category in filteredCategories"
        :key="category.id"
        @click="selectCategory(category)"
        class="group cursor-pointer border border-gray-200 rounded-xl p-6 hover:border-blue-500 hover:shadow-md transition-all"
      >
        <div class="flex items-start gap-4">
          <div class="w-12 h-12 rounded-lg bg-blue-50 flex items-center justify-center group-hover:bg-blue-100 transition">
            <component :is="getCategoryIcon(category.icon)" class="w-6 h-6 text-blue-600" />
          </div>
          <div class="flex-1">
            <h3 class="font-semibold text-lg group-hover:text-blue-600 transition">
              {{ category.name }}
            </h3>
            <p class="text-sm text-gray-500 mt-1">
              {{ category.description }}
            </p>
            <p class="text-xs text-gray-400 mt-2">
              {{ category.count }} объявлений
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Подкатегории -->
    <div v-else class="space-y-4">
      <div class="flex items-center gap-2">
        <button
          @click="selectedCategory = null"
          class="text-blue-600 hover:text-blue-700 flex items-center gap-1"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
          Назад
        </button>
        <span class="text-gray-400">/</span>
        <span class="font-medium">{{ selectedCategory.name }}</span>
      </div>

      <div class="grid gap-3">
        <div
          v-for="subcategory in selectedCategory.subcategories"
          :key="subcategory.id"
          @click="selectSubcategory(subcategory)"
          class="flex items-center justify-between p-4 border rounded-xl cursor-pointer transition-all"
          :class="[
            form.subcategory_id === subcategory.id 
              ? 'border-blue-500 bg-blue-50' 
              : 'border-gray-200 hover:border-gray-300'
          ]"
        >
          <div>
            <h4 class="font-medium">{{ subcategory.name }}</h4>
            <p class="text-sm text-gray-500">{{ subcategory.count }} объявлений</p>
          </div>
          <div 
            class="w-5 h-5 rounded-full border-2 transition-all"
            :class="[
              form.subcategory_id === subcategory.id
                ? 'border-blue-500 bg-blue-500'
                : 'border-gray-300'
            ]"
          >
            <svg 
              v-if="form.subcategory_id === subcategory.id"
              class="w-full h-full text-white p-0.5" 
              fill="currentColor" 
              viewBox="0 0 20 20"
            >
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Популярные категории -->
    <div v-if="!selectedCategory && popularCategories.length > 0" class="border-t pt-6">
      <h3 class="font-medium mb-3 text-gray-700">Популярные категории</h3>
      <div class="flex flex-wrap gap-2">
        <button
          v-for="category in popularCategories"
          :key="category.id"
          @click="selectCategory(category)"
          class="px-4 py-2 bg-gray-100 rounded-lg text-sm hover:bg-gray-200 transition"
        >
          {{ category.name }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

// Иконки для категорий
import { 
  SparklesIcon,
  ScissorsIcon,
  HeartIcon,
  HomeIcon,
  AcademicCapIcon,
  CameraIcon,
  MusicalNoteIcon,
  TruckIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  form: {
    type: Object,
    required: true
  },
  errors: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['update:form'])

// Локальное состояние
const searchQuery = ref('')
const categories = ref([])
const selectedCategory = ref(null)
const loading = ref(false)

// Computed
const filteredCategories = computed(() => {
  if (!searchQuery.value) return categories.value
  
  const query = searchQuery.value.toLowerCase()
  return categories.value.filter(category => 
    category.name.toLowerCase().includes(query) ||
    category.description?.toLowerCase().includes(query)
  )
})

const popularCategories = computed(() => {
  return categories.value
    .filter(c => c.is_popular)
    .slice(0, 6)
})

// Методы
async function loadCategories() {
  loading.value = true
  try {
    const response = await axios.get('/api/categories')
    categories.value = response.data
    
    // Если уже выбрана категория, восстанавливаем состояние
    if (props.form.category_id) {
      const category = categories.value.find(c => c.id === props.form.category_id)
      if (category) {
        selectedCategory.value = category
      }
    }
  } catch (error) {
    console.error('Ошибка загрузки категорий:', error)
  } finally {
    loading.value = false
  }
}

function selectCategory(category) {
  selectedCategory.value = category
  props.form.category_id = category.id
  
  // Если в категории только одна подкатегория, выбираем её автоматически
  if (category.subcategories?.length === 1) {
    selectSubcategory(category.subcategories[0])
  }
}

function selectSubcategory(subcategory) {
  props.form.subcategory_id = subcategory.id
}

function getCategoryIcon(iconName) {
  const icons = {
    sparkles: SparklesIcon,
    scissors: ScissorsIcon,
    heart: HeartIcon,
    home: HomeIcon,
    academic: AcademicCapIcon,
    camera: CameraIcon,
    music: MusicalNoteIcon,
    truck: TruckIcon
  }
  return icons[iconName] || SparklesIcon
}

// Lifecycle
onMounted(() => {
  loadCategories()
})
</script>