<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const props = defineProps({
  form: Object,
  errors: Object
})

const categories = ref([])
const loading = ref(false)

onMounted(async () => {
  try {
    loading.value = true
    const res = await axios.get('/api/categories')
    categories.value = res.data
  } catch (e) {
    console.error('Не удалось загрузить категории', e)
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div>
    <h2 class="text-2xl font-semibold mb-4">Выберите категорию</h2>

    <div v-if="loading" class="text-gray-500">Загрузка...</div>

    <div v-else class="space-y-4">
      <div
        v-for="cat in categories"
        :key="cat.id"
        @click="form.category_id = cat.id"
        class="p-4 border rounded-xl cursor-pointer"
        :class="form.category_id === cat.id ? 'border-blue-500 bg-blue-50' : 'hover:bg-gray-50'"
      >
        {{ cat.name }}
      </div>
      <div v-if="errors['category_id']" class="text-red-500 text-sm">
        {{ errors['category_id'] }}
      </div>
    </div>
  </div>
</template>

<style scoped>
/* минимальные стили */
</style>
