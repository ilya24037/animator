<template>
  <div class="max-w-2xl mx-auto p-6 bg-white rounded-2xl shadow">
    <form @submit.prevent="submitForm">
      <!-- ✅ ДОБАВЛЕНО: Сообщения об успехе/ошибке -->
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

      <!-- ✅ ИСПРАВЛЕНО: Кнопки с блокировкой во время отправки -->
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
      <p class="mt-4 text-center text-gray-500 text-base leading-tight max-w-xl">
        Вы публикуете объявление и данные в нём, чтобы их мог посмотреть кто угодно в интернете.<br>
        Вы также соглашаетесь с правилами сервиса.
      </p>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, nextTick, getCurrentInstance, computed } from 'vue'
import { useValidator } from '@/utils/useValidator.js'
import errorPath from '@/directives/errorPath.js'
import { Inertia } from '@inertiajs/inertia'
import { route } from 'ziggy-js'
import { usePage } from '@inertiajs/vue3'

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

// Ссылка на первый шаг для скролла к ошибкам
const step1DetailsRef = ref<InstanceType<typeof Step1Details> | null>(null)

// Получаем Inertia-пропс с flash сообщениями
const page = usePage()
const successMessage = computed(() => {
  return (page.props.flash as any)?.success || ''
})

const errorMessage = computed(() => {
  return (page.props.flash as any)?.error || ''
})

// ✅ ИСПРАВЛЕНО: Состояние загрузки для кнопок
const isSubmitting = ref(false)

// Модель всей формы (многослойная структура)
const form = reactive({
  details:  { title: '', description: '' },
  workFormat: {
    specialization: '',
    type: '',
    clients: [] as string[],
    workFormats: [] as string[],
    serviceProviders: [] as string[],
    experience: ''
  },
  priceList: { priceItems: [] as { name: string; price: number; unit: string; duration: string }[] },
  price:     { value: '', unit: 'за час', isBasePrice: false },
  actions:   { discount: null, gift: '' },
  media:     { files: [] as File[], videoUrl: '' },
  geo:       { city: '', address: '', visitType: '' },
  contacts:  { phone: '', email: '', contactWays: ['any'] },
  review:    { text: '' },
  status:    'draft'
})

// Правила валидации (упрощенные для новичков)
const { errors, validate } = useValidator(form, {
  'details.title': v => v ? '' : 'Укажите название объявления',
})

/**
 * 🚀 «Разместить» (ставит status = pending, выполняет валидацию и отправляет форму)
 */
function onPlace() {
  console.log('🚀 Нажата кнопка "Разместить"')
  form.status = 'pending'
  const { ok } = validate()
  if (ok) {
    submitForm()
    return
  }
  scrollToFirstError()
}

/**
 * 💾 «Сохранить и выйти» (сохраняем как draft)
 */
function saveAndExit() {
  console.log('💾 Нажата кнопка "Сохранить и выйти"')
  form.status = 'draft'
  submitForm()
}

/**
 * 📤 Фактическая отправка формы
 */
function submitForm() {
  if (isSubmitting.value) {
    console.log('⏳ Форма уже отправляется, ждите...')
    return
  }
  
  isSubmitting.value = true
  
  console.log('📤 Отправляем данные формы:', form)
  
  Inertia.post(route('animators.store'), form, {
    preserveState: false,  // Обновляем состояние страницы
    preserveScroll: false, // Скроллим в начало при ошибках
    onStart: () => {
      console.log('🔄 Начинаем отправку формы...')
    },
    onSuccess: (page) => {
      console.log('✅ Форма успешно отправлена!')
      isSubmitting.value = false
    },
    onError: (errors) => {
      console.error('❌ Ошибки при отправке формы:', errors)
      isSubmitting.value = false
      scrollToFirstError()
    },
    onFinish: () => {
      console.log('🏁 Завершили отправку формы')
      isSubmitting.value = false
    }
  })
}

/**
 * 📍 Скролл к первой ошибке валидации
 */
function scrollToFirstError() {
  nextTick(() => {
    // Если ошибка в заголовке первого шага – сразу к нему
    if (errors['details.title'] && step1DetailsRef.value?.titleInput) {
      step1DetailsRef.value.titleInput.focus()
      step1DetailsRef.value.titleInput.scrollIntoView({ behavior: 'smooth', block: 'center' })
      return
    }
    
    // Иначе ищем все элементы с data-path, у которых есть ошибки
    const candidates: { el: HTMLElement; top: number }[] = []
    document.querySelectorAll('[data-path]').forEach(el => {
      const path = el.getAttribute('data-path') || ''
      if (path && errors[path]) {
        candidates.push({ el: el as HTMLElement, top: el.getBoundingClientRect().top + window.scrollY })
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