<template>
  <AppLayout>
    <div class="min-h-screen bg-gray-50 py-6">
      <div class="max-w-5xl mx-auto">
        <!-- Прогресс-бар -->
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
          <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold">Разместить объявление</h1>
            <button 
              v-if="store.draftId" 
              @click="deleteDraft"
              class="text-sm text-gray-500 hover:text-red-500"
            >
              Удалить черновик
            </button>
          </div>
          
          <StepProgress :current-step="currentStep" :steps="steps" />
        </div>

        <!-- Форма -->
        <div class="grid grid-cols-3 gap-6">
          <!-- Левая колонка - шаги -->
          <div class="col-span-2">
            <div class="bg-white rounded-2xl shadow-sm p-8">
              <!-- Индикатор автосохранения -->
              <AutoSaveIndicator 
                :saving="store.isSaving" 
                :last-saved="store.lastSaved"
                :error="store.saveError"
              />

              <!-- Динамический компонент шага -->
              <transition name="fade" mode="out-in">
                <component 
                  :is="currentStepComponent" 
                  v-model:form="store.form"
                  :errors="errors"
                  ref="stepRef"
                />
              </transition>

              <!-- Навигация -->
              <div class="flex justify-between mt-10 pt-6 border-t">
                <button
                  v-if="currentStep > 1"
                  @click="prevStep"
                  class="flex items-center gap-2 px-6 py-3 text-gray-600 hover:text-black transition"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                  </svg>
                  Назад
                </button>
                <div v-else></div>

                <div class="flex gap-4">
                  <button
                    @click="saveDraft"
                    :disabled="store.isSaving"
                    class="px-6 py-3 border border-gray-300 rounded-xl hover:bg-gray-50 transition"
                  >
                    Сохранить черновик
                  </button>
                  
                  <button
                    v-if="currentStep < steps.length"
                    @click="nextStep"
                    :disabled="!canProceed"
                    class="flex items-center gap-2 px-8 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition"
                  >
                    Далее
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                  </button>
                  
                  <button
                    v-else
                    @click="publish"
                    :disabled="store.isSaving || !isFormValid"
                    class="px-8 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition"
                  >
                    Опубликовать
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Правая колонка - превью и подсказки -->
          <div class="space-y-6">
            <!-- Превью объявления -->
            <AdPreview :data="store.form" />
            
            <!-- Подсказки -->
            <HelpBlock :step="currentStep" />
            
            <!-- Информация о модерации -->
            <ModerationInfo />
          </div>
        </div>
      </div>
    </div>

    <!-- Модалка подтверждения выхода -->
    <ExitConfirmModal 
      :show="showExitModal"
      @confirm="confirmExit"
      @cancel="showExitModal = false"
    />
  </AppLayout>
</template>

<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue'
import { useAnimatorStore } from '@/Stores/AnimatorStore'
import { router } from '@inertiajs/vue3'
import { debounce } from 'lodash'
import AppLayout from '@/Layouts/AppLayout.vue'
import StepProgress from '@/Components/CreateAd/StepProgress.vue'
import AutoSaveIndicator from '@/Components/CreateAd/AutoSaveIndicator.vue'
import AdPreview from '@/Components/CreateAd/AdPreview.vue'
import HelpBlock from '@/Components/CreateAd/HelpBlock.vue'
import ModerationInfo from '@/Components/CreateAd/ModerationInfo.vue'
import ExitConfirmModal from '@/Components/CreateAd/ExitConfirmModal.vue'

// Импорт шагов
import Step1Category from './CreateSteps/Step1Category.vue'
import Step2Details from './CreateSteps/Step2Details.vue'
import Step3Services from './CreateSteps/Step3Services.vue'
import Step4Photos from './CreateSteps/Step4Photos.vue'
import Step5Price from './CreateSteps/Step5Price.vue'
import Step6Location from './CreateSteps/Step6Location.vue'
import Step7Contacts from './CreateSteps/Step7Contacts.vue'
import Step8Review from './CreateSteps/Step8Review.vue'

// Store
const store = useAnimatorStore()

// Props
const props = defineProps({
  draftId: String
})

// Состояние
const currentStep = ref(1)
const errors = ref({})
const showExitModal = ref(false)
const stepRef = ref(null)
const isSubmitting = ref(false)

// Конфигурация шагов
const steps = [
  { id: 1, name: 'Категория', component: Step1Category, required: true },
  { id: 2, name: 'Описание', component: Step2Details, required: true },
  { id: 3, name: 'Услуги', component: Step3Services, required: true },
  { id: 4, name: 'Фото и видео', component: Step4Photos, required: false },
  { id: 5, name: 'Цена', component: Step5Price, required: true },
  { id: 6, name: 'Место оказания услуг', component: Step6Location, required: true },
  { id: 7, name: 'Контакты', component: Step7Contacts, required: true },
  { id: 8, name: 'Проверка и публикация', component: Step8Review, required: false }
]

// Computed
const currentStepComponent = computed(() => {
  return steps.find(s => s.id === currentStep.value)?.component
})

const canProceed = computed(() => {
  const step = steps.find(s => s.id === currentStep.value)
  if (!step.required) return true
  
  // Валидация в зависимости от шага
  switch (currentStep.value) {
    case 1: return store.form.category_id && store.form.subcategory_id
    case 2: return store.form.name && store.form.description
    case 3: return store.form.services?.length > 0
    case 5: return store.form.price > 0
    case 6: return store.form.city_id && store.form.zones
    case 7: return store.form.phone
    default: return true
  }
})

const isFormValid = computed(() => {
  return store.form.name && 
         store.form.description && 
         store.form.services?.length > 0 &&
         store.form.price > 0 &&
         store.form.city_id &&
         store.form.phone &&
         store.form.terms_accepted
})

// Автосохранение
const debouncedSave = debounce(() => {
  if (!isSubmitting.value) {
    store.saveDraft()
  }
}, 2000)

// Watchers
watch(() => store.form, () => {
  debouncedSave()
}, { deep: true })

// Навигация по шагам
function nextStep() {
  if (canProceed.value && currentStep.value < steps.length) {
    // Валидация текущего шага
    if (validateCurrentStep()) {
      currentStep.value++
      window.scrollTo({ top: 0, behavior: 'smooth' })
    }
  }
}

function prevStep() {
  if (currentStep.value > 1) {
    currentStep.value--
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

function validateCurrentStep() {
  // Здесь можно добавить специфичную валидацию для каждого шага
  return true
}

// Действия
async function saveDraft() {
  const success = await store.saveDraft()
  if (success) {
    showNotification('success', 'Черновик сохранен')
  }
}

async function publish() {
  if (!isFormValid.value) {
    showNotification('error', 'Заполните все обязательные поля')
    return
  }
  
  isSubmitting.value = true
  try {
    const success = await store.publish()
    if (success) {
      router.visit('/profile/items/pending/all')
    }
  } finally {
    isSubmitting.value = false
  }
}

async function deleteDraft() {
  if (confirm('Удалить черновик? Это действие нельзя отменить.')) {
    // Реализация удаления
    store.resetForm()
    router.visit('/profile/items/draft/all')
  }
}

function confirmExit() {
  store.saveDraft()
  router.visit('/profile/items/draft/all')
}

// Перехват закрытия страницы
function handleBeforeUnload(e) {
  if (store.form.name || store.form.description) {
    e.preventDefault()
    e.returnValue = ''
  }
}

// Уведомления
function showNotification(type, message) {
  // Используем inject из AppLayout
  const toast = inject('showToast')
  if (toast) {
    toast(type, message)
  }
}

// Lifecycle
onMounted(() => {
  // Загрузка черновика если есть
  if (props.draftId) {
    store.loadDraft(props.draftId)
  }
  
  // Перехват закрытия
  window.addEventListener('beforeunload', handleBeforeUnload)
  
  // Горячие клавиши
  document.addEventListener('keydown', handleKeyboard)
})

onBeforeUnmount(() => {
  window.removeEventListener('beforeunload', handleBeforeUnload)
  document.removeEventListener('keydown', handleKeyboard)
  
  // Финальное сохранение
  store.saveDraft()
})

// Горячие клавиши
function handleKeyboard(e) {
  if (e.ctrlKey || e.metaKey) {
    if (e.key === 's') {
      e.preventDefault()
      saveDraft()
    }
  }
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>