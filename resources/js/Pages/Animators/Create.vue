<template>
  <AppLayout>
    <div class="max-w-2xl mx-auto p-6 bg-white rounded-2xl shadow">
      <form @submit.prevent="submitForm">
        <!-- Сообщения об успехе/ошибке -->
        <div v-if="successMessage" class="mb-4 p-3 rounded bg-green-100 text-green-800 text-center shadow">
          ✅ {{ successMessage }}
        </div>
        
        <div v-if="errorMessage" class="mb-4 p-3 rounded bg-red-100 text-red-800 text-center shadow">
          ❌ {{ errorMessage }}
        </div>

        <!-- Все 10 шагов формы -->
        <Step1Details     ref="step1DetailsRef"     v-model:form="form.details"   :errors="errors" />
        <Step2WorkFormat                       v-model:form="form.workFormat"  :errors="errors" />
        <Step3PriceList                        v-model:form="form.priceList"   :errors="errors" />
        <Step4Description                      v-model:form="form.details"     :errors="errors" />
        <Step5Price                            v-model:form="form.price"       :errors="errors" />
        <Step6Actions                          v-model:form="form.actions"     :errors="errors" />
        <Step7Media                            v-model:form="form.media"       :errors="errors" />
        <Step8Geo                              v-model:form="form.geo"         :errors="errors" />
        <Step9Contacts                         v-model:form="form.contacts"    :errors="errors" />
        <Step10Review                          v-model:form="form.review"      :errors="errors" />

        <!-- Кнопки с блокировкой во время отправки -->
        <div class="flex gap-4 mt-10 justify-center">
          <button
            type="button"
            :disabled="isSubmitting"
            class="px-14 py-5 rounded-2xl font-semibold text-white text-xl bg-black hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed"
            @click="onPlace"
          >
            {{ isSubmitting ? '⏳ Размещаем...' : '🚀 Разместить' }}
          </button>
          <button
            type="button"
            :disabled="isSubmitting"
            class="px-10 py-5 rounded-2xl font-semibold text-black text-xl bg-gray-100 hover:bg-gray-200 disabled:opacity-50 disabled:cursor-not-allowed"
            @click="saveAndExit"
          >
            {{ isSubmitting ? '⏳ Сохраняем...' : '💾 Сохранить и выйти' }}
          </button>
        </div>

        <!-- Текст про правила -->
        <p class="mt-4 text-center text-gray-500 text-base leading-tight max-w-xl mx-auto">
          Вы публикуете объявление и данные в нём, чтобы их мог посмотреть кто угодно в интернете.<br>
          Вы также соглашаетесь с правилами сервиса.
        </p>
      </form>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, nextTick, getCurrentInstance, computed, onMounted, onBeforeUnmount, watch, inject } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import axios from 'axios'

// Импорты утилит и директив
import { useValidator } from '@/utils/useValidator.js'
import errorPath from '@/directives/errorPath.js'

// Импорт layout
import AppLayout from '@/Layouts/AppLayout.vue'

// Регистрируем директиву error-path
const app = getCurrentInstance()?.appContext.app
if (app && !app.directive('error-path')) {
  app.directive('error-path', errorPath)
}

// Импорт компонентов шагов
import Step1Details     from './Create/Step1Details.vue'
import Step2WorkFormat  from './Create/Step2WorkFormat.vue'
import Step3PriceList   from './Create/Step3PriceList.vue'
import Step4Description from './Create/Step4Description.vue'
import Step5Price       from './Create/Step5Price.vue'
import Step6Actions     from './Create/Step6Actions.vue'
import Step7Media       from './Create/Step7Media.vue'
import Step8Geo         from './Create/Step8Geo.vue'
import Step9Contacts    from './Create/Step9Contacts.vue'
import Step10Review     from './Create/Step10Review.vue'

// Props
const props = defineProps({
  draftId: {
    type: Number,
    default: null
  }
})

// Refs
const step1DetailsRef = ref(null)
const isSubmitting = ref(false)
const autoSaveTimer = ref(null)
const lastSavedData = ref('')

// Получаем flash сообщения
const page = usePage()
const successMessage = computed(() => page.props.flash?.success || '')
const errorMessage = computed(() => page.props.flash?.error || '')

// ID черновика для обновления
const currentDraftId = ref(props.draftId)

// Модель формы
const form = reactive({
  details:  { title: '', description: '' },
  workFormat: {
    specialization: '',
    type: '',
    clients: [],
    workFormats: [],
    serviceProviders: [],
    experience: ''
  },
  priceList: { priceItems: [] },
  price:     { value: '', unit: 'за час', isBasePrice: false },
  actions:   { discount: null, gift: '' },
  media:     { files: [], videoUrl: '' },
  geo:       { city: '', address: '', visitType: '' },
  contacts:  { phone: '', email: '', contactWays: ['any'] },
  review:    { text: '' },
  status:    'draft'
})

// Правила валидации
const { errors, validate } = useValidator(form, {
  'details.title': v => v ? '' : 'Укажите название объявления',
})

// Загрузка черновика при монтировании
onMounted(async () => {
  if (currentDraftId.value) {
    await loadDraft()
  }
  
  // Запускаем автосохранение каждые 30 секунд
  startAutoSave()
})

// Останавливаем автосохранение при размонтировании
onBeforeUnmount(() => {
  stopAutoSave()
})

// Следим за изменениями формы
watch(form, () => {
  // Будет вызывать автосохранение через 30 секунд после последнего изменения
}, { deep: true })

// Загрузка черновика
async function loadDraft() {
  try {
    const response = await axios.get(`/animators/draft/${currentDraftId.value}`)
    if (response.data.success && response.data.animator) {
      const draft = response.data.animator
      
      // Восстанавливаем данные формы
      form.details.title = draft.title || ''
      form.details.description = draft.description || ''
      
      // Парсим JSON поля
      if (draft.work_format) {
        Object.assign(form.workFormat, draft.work_format)
      }
      if (draft.price_list) {
        form.priceList = draft.price_list
      }
      if (draft.actions_data) {
        Object.assign(form.actions, draft.actions_data)
      }
      if (draft.geo_data) {
        Object.assign(form.geo, draft.geo_data)
      }
      if (draft.contacts_data) {
        Object.assign(form.contacts, draft.contacts_data)
      }
      
      form.price.value = draft.price || ''
      
      lastSavedData.value = JSON.stringify(form)
    }
  } catch (error) {
    console.error('Ошибка загрузки черновика:', error)
  }
}

// Автосохранение
function startAutoSave() {
  autoSaveTimer.value = setInterval(() => {
    const currentData = JSON.stringify(form)
    if (currentData !== lastSavedData.value) {
      saveDraft(true) // silent save
    }
  }, 30000) // 30 секунд
}

function stopAutoSave() {
  if (autoSaveTimer.value) {
    clearInterval(autoSaveTimer.value)
    autoSaveTimer.value = null
  }
}

// Получаем функцию показа уведомлений
const showToast = inject('showToast')

// Сохранение черновика
async function saveDraft(silent = false) {
  try {
    const response = await axios.post('/animators/draft', {
      ...form,
      draft_id: currentDraftId.value
    })
    
    if (response.data.success) {
      if (response.data.animator?.id) {
        currentDraftId.value = response.data.animator.id
      }
      lastSavedData.value = JSON.stringify(form)
      
      if (!silent && showToast) {
        showToast('success', 'Черновик сохранен')
      }
    }
  } catch (error) {
    console.error('Ошибка сохранения черновика:', error)
    if (!silent && showToast) {
      showToast('error', 'Ошибка при сохранении черновика')
    }
  }
}

// «Разместить»
function onPlace() {
  console.log('🚀 Нажата кнопка "Разместить"')
  form.status = 'pending'
  const result = validate()
  if (result) {
    submitForm()
  } else {
    scrollToFirstError()
  }
}

// «Сохранить и выйти»
async function saveAndExit() {
  console.log('💾 Нажата кнопка "Сохранить и выйти"')
  form.status = 'draft'
  
  if (isSubmitting.value) return
  
  isSubmitting.value = true
  
  try {
    await saveDraft()
    
    // Перенаправляем на страницу с черновиками
    router.get('/profile/items/draft/all')
  } catch (error) {
    console.error('Ошибка:', error)
  } finally {
    isSubmitting.value = false
  }
}

// Отправка формы
function submitForm() {
  if (isSubmitting.value) {
    console.log('⏳ Форма уже отправляется...')
    return
  }
  
  isSubmitting.value = true
  
  console.log('📤 Отправляем данные формы:', form)
  
  // Используем обычный POST через Inertia router
  router.post('/animators', form, {
    preserveState: false,
    preserveScroll: false,
    onStart: () => {
      console.log('🔄 Начинаем отправку формы...')
    },
    onSuccess: () => {
      console.log('✅ Форма успешно отправлена!')
      stopAutoSave()
    },
    onError: (errors) => {
      console.error('❌ Ошибки при отправке формы:', errors)
      scrollToFirstError()
    },
    onFinish: () => {
      console.log('🏁 Завершили отправку формы')
      isSubmitting.value = false
    }
  })
}

// Скролл к первой ошибке
function scrollToFirstError() {
  nextTick(() => {
    if (errors['details.title'] && step1DetailsRef.value?.titleInput) {
      step1DetailsRef.value.titleInput.focus()
      step1DetailsRef.value.titleInput.scrollIntoView({ behavior: 'smooth', block: 'center' })
      return
    }
    
    const candidates = []
    document.querySelectorAll('[data-path]').forEach(el => {
      const path = el.getAttribute('data-path') || ''
      if (path && errors[path]) {
        candidates.push({ el, top: el.getBoundingClientRect().top + window.scrollY })
      }
    })
    
    if (!candidates.length) return
    
    candidates.sort((a, b) => a.top - b.top)
    const target = candidates[0].el
    target.scrollIntoView({ behavior: 'smooth', block: 'center' })
    target.focus?.()
    target.classList.add('animate-pulse')
    setTimeout(() => target.classList.remove('animate-pulse'), 1200)
  })
}
</script>