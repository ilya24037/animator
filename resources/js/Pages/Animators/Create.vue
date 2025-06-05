<script setup lang="ts">
/* eslint-disable no-console */
console.log('Create.vue loaded!');

import { ref, reactive, nextTick, getCurrentInstance, computed } from 'vue'
import { useValidator } from '@/utils/useValidator.js'
import errorPath from '@/directives/errorPath.js'
import { Inertia } from '@inertiajs/inertia'
import { route } from 'ziggy-js'
import { usePage } from '@inertiajs/vue3'

/* ─── Регистрируем директиву error-path только один раз ─── */
const app = getCurrentInstance()?.appContext.app
if (app && !app.directive('error-path')) {
  app.directive('error-path', errorPath)
}

/* Импортируем компоненты шагов */
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

/* ───────────────────────────────────────────────────────────── */
const step1DetailsRef = ref<HTMLDivElement | null>(null)

/* Получаем props от Inertia */
const page = usePage()

/*
 * В контроллере мы делаем `->with('success', 'Анкета сохранена')`.
 * Поэтому здесь читаем именно page.props.success.
 */
const successMessage = computed(() => page.props.success || '')

/* Модель всей формы (вложенные объекты) */
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
  priceList: { priceItems: [] as { name: string; amount: number }[] },
  price:     { value: '' },
  actions:   { items: [] as string[] },
  media:     { files: [] as File[] },
  geo:       { city: '', address: '' },
  contacts:  { phone: '', email: '', method: '' },
  review:    { text: '' },
  status:    'draft'
})

/* Правила валидации для вложенных полей через dot-notation */
const { errors, validate } = useValidator(form, {
  'details.title':               v => v ? '' : 'Укажите название объявления',
  'workFormat.specialization':   v => v ? '' : 'Укажите специальность',
  'details.description':         v => v ? '' : 'Добавьте описание',
  'workFormat.type':             v => v ? '' : 'Выберите формат работы',
  'workFormat.clients':          v => Array.isArray(v) && v.length ? '' : 'Выберите хотя бы одного клиента',
  'workFormat.workFormats':      v => Array.isArray(v) && v.length ? '' : 'Укажите хотя бы один вариант',
  'workFormat.serviceProviders': v => Array.isArray(v) && v.length ? '' : 'Укажите, кто оказывает услуги',
  'workFormat.experience':       v => v ? '' : 'Укажите опыт работы',
  'price.value':                 v => v ? '' : 'Укажите цену',
  'actions.items':               v => Array.isArray(v) && v.length ? '' : 'Выберите услуги',
  'geo.city':                    v => v ? '' : 'Выберите город',
  'contacts.phone':              v => v ? '' : 'Введите телефон'
})

/* ─────────── «Разместить» (pending) ─────────── */
function onPlace () {
  form.status = 'pending'
  const { ok } = validate()
  if (ok) {
    submitForm()
    return
  }
  /* прокрутка к первому полю с ошибкой */
  nextTick(() => {
    if (errors['details.title'] && step1DetailsRef.value?.titleInput) {
      step1DetailsRef.value.titleInput.focus()
      step1DetailsRef.value.titleInput.scrollIntoView({ behavior: 'smooth', block: 'center' })
      return
    }
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

/* ─────────── «Сохранить и выйти» (draft) ─────────── */
function saveAndExit () {
  form.status = 'draft'
  Inertia.post(route('animators.store'), form, {
    preserveScroll: true,        // Страница не перезагружается полностью, flash придёт в новый props
    onError: (err) => {
      alert('Ошибка: ' + JSON.stringify(err))
    }
  })
}

/* ─────────── Обычная отправка (например, «Разместить») ─────────── */
function submitForm () {
  Inertia.post(route('animators.store'), form, {
    onError: (err) => {
      // Здесь можно обработать ошибки, если нужно
    },
  })
}
</script>

<template>
  <div class="max-w-2xl mx-auto p-6 bg-white rounded-2xl shadow">
    <form @submit.prevent="submitForm">
      <!-- 1) Показ flash-сообщения, если оно задано контроллером -->
      <div
        v-if="successMessage"
        class="mb-4 p-3 rounded bg-green-100 text-green-800 text-center shadow"
      >
        {{ successMessage }}
      </div>

      <!-- 2) Блок со всеми 10 шагами -->
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

      <!-- 3) Кнопки «Разместить» и «Сохранить и выйти» -->
      <div class="flex gap-4 mt-10 justify-center">
        <button
          type="button"
          class="px-14 py-5 rounded-2xl font-semibold text-white text-xl bg-black"
          @click="onPlace"
        >
          Разместить
        </button>
        <button
          type="button"
          class="px-10 py-5 rounded-2xl font-semibold text-black text-xl bg-gray-100"
          @click="saveAndExit"
        >
          Сохранить и выйти
        </button>
      </div>

      <!-- 4) Текст про правила Авито -->
      <p class="mt-4 text-center text-gray-500 text-base leading-tight max-w-xl">
        Вы публикуете объявление и данные в&nbsp;нём, чтобы их мог посмотреть кто&nbsp;угодно в&nbsp;интернете.<br>
        Вы также соглашаетесь
        <a
          href="https://www.avito.ru/legal/rules"
          target="_blank"
          class="underline"
        >с&nbsp;правилами Авито</a>.
      </p>
    </form>
  </div>
</template>

<style scoped>
/* Ваши стили оставлены без изменений */
</style>
