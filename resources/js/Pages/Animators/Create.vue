<template>
  <AppLayout>
    <div class="max-w-2xl mx-auto p-6 bg-white rounded-2xl shadow">
      <!-- Индикатор автосохранения как на Avito -->
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Новое объявление</h1>
        <div class="text-sm text-gray-500">
          <span v-if="formStore.isSaving" class="flex items-center gap-2">
            <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" />
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
            </svg>
            Сохранение...
          </span>
          <span v-else-if="formStore.lastSaved" class="text-green-600">
            ✓ Сохранено {{ formatTime(formStore.lastSaved) }}
          </span>
          <span v-else-if="formStore.saveError" class="text-red-600">
            ⚠ {{ formStore.saveError }}
          </span>
        </div>
      </div>

      <form @submit.prevent="handleSubmit">
        <!-- Шаги формы с v-model на store -->
        <Step1Details v-model:form="formStore.form.details" :errors="errors" />
        <Step2WorkFormat v-model:form="formStore.form.workFormat" :errors="errors" />
        <!-- ... остальные шаги ... -->
        
        <!-- Кнопки действий -->
        <div class="flex gap-4 mt-10 justify-center">
          <button
            type="submit"
            :disabled="isSubmitting"
            class="px-14 py-5 rounded-2xl font-semibold text-white text-xl bg-black hover:opacity-90 disabled:opacity-50"
          >
            {{ isSubmitting ? '⏳ Размещаем...' : '🚀 Разместить' }}
          </button>
          <button
            type="button"
            @click="saveAndExit"
            :disabled="isSubmitting"
            class="px-10 py-5 rounded-2xl font-semibold text-black text-xl bg-gray-100 hover:bg-gray-200"
          >
            💾 Сохранить и выйти
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue'
import { useAnimatorFormStore } from '@/stores/useAnimatorFormStore'
import { router } from '@inertiajs/vue3'
import { debounce } from 'lodash'
import AppLayout from '@/Layouts/AppLayout.vue'

// Импорт шагов
import Step1Details from './Create/Step1Details.vue'
import Step2WorkFormat from './Create/Step2WorkFormat.vue'
// ... остальные импорты

// Props
const props = defineProps({
  draft: Object
})

// Store
const formStore = useAnimatorFormStore()

// Локальное состояние
const isSubmitting = ref(false)
const errors = ref({})

// Инициализация
onMounted(() => {
  // Загружаем черновик если есть
  if (props.draft) {
    formStore.initForm(props.draft)
  }
  
  // Запускаем автосохранение
  startAutoSave()
})

// Автосохранение при изменениях
const debouncedAutoSave = debounce(() => {
  formStore.autoSave()
}, 3000) // Сохраняем через 3 секунды после последнего изменения

// Следим за изменениями формы
watch(() => formStore.form, () => {
  debouncedAutoSave()
}, { deep: true })

// Таймер для периодического сохранения
let autoSaveInterval = null

function startAutoSave() {
  // Сохраняем каждые 30 секунд
  autoSaveInterval = setInterval(() => {
    formStore.autoSave()
  }, 30000)
}

// Очистка при размонтировании
onUnmounted(() => {
  if (autoSaveInterval) {
    clearInterval(autoSaveInterval)
  }
  // Сохраняем перед уходом
  formStore.autoSave()
})

// Обработка отправки формы
async function handleSubmit() {
  isSubmitting.value = true
  errors.value = {}
  
  const result = await formStore.publish()
  
  if (result.success) {
    router.visit('/profile/items/draft/all', {
      preserveState: false
    })
  } else {
    errors.value = result.errors
    scrollToFirstError()
  }
  
  isSubmitting.value = false
}

// Сохранить и выйти
async function saveAndExit() {
  await formStore.autoSave()
  router.visit('/profile/items/draft/all')
}

// Форматирование времени
function formatTime(date) {
  const now = new Date()
  const diff = Math.floor((now - date) / 1000)
  
  if (diff < 60) return 'только что'
  if (diff < 3600) return `${Math.floor(diff / 60)} мин. назад`
  return date.toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit' })
}

// Скролл к ошибке
function scrollToFirstError() {
  // ... ваша логика скролла
}
</script>