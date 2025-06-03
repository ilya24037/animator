<script setup lang="ts">
console.log('Create.vue loaded!');

import { ref, reactive, nextTick, getCurrentInstance } from 'vue'
import { useValidator } from '@/utils/useValidator.js'
import errorPath from '@/directives/errorPath.js'

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

import { Inertia } from '@inertiajs/inertia'
import { route } from 'ziggy-js'

const app = getCurrentInstance()?.appContext.app
app && app.directive('error-path', errorPath)

const step1DetailsRef = ref(null)

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
  price:     { value: '' },
  actions:   { items: [] },
  media:     { files: [] },
  geo:       { city: '', address: '' },
  contacts:  { phone: '', email: '', method: '' },
  review:    { text: '' },
  status:    'draft'
})

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

// "Разместить"
function onPlace () {
  form.status = 'pending'
  const { ok } = validate()
  if (ok) {
    submitForm()
    return
  }
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

// "Сохранить и выйти" (черновик)

function saveAndExit () {
  alert('saveAndExit called!');
  form.status = 'draft'
  submitForm()
}

// Отправка формы на сервер через Inertia

function submitForm () {
alert('submitForm called!');
  Inertia.post(route('animators.store'), form, {
    onSuccess: () => {},
    onError: (err) => {
      // Можете добавить alert(JSON.stringify(err)) для отладки
    },
  })
}
</script>

<template>
  <div class="max-w-2xl mx-auto p-6 bg-white rounded-2xl shadow">
    <form @submit.prevent="submitForm">
      <Step1Details     ref="step1DetailsRef"     v-model:form="form.details"    :errors="errors" />
      <Step2WorkFormat                          v-model:form="form.workFormat"  :errors="errors" />
      <Step3PriceList                           v-model:form="form.priceList"   :errors="errors" />
      <Step4Description                         v-model:form="form.details"     :errors="errors" />
      <Step5Price                               v-model:form="form.price"       :errors="errors" />
      <Step6Actions                             v-model:form="form.actions"     :errors="errors" />
      <Step7Media                               v-model:form="form.media"       :errors="errors" />
      <Step8Geo                                 v-model:form="form.geo"         :errors="errors" />
      <Step9Contacts                            v-model:form="form.contacts"    :errors="errors" />
      <Step10Review                             v-model:form="form.review"      :errors="errors" />

      <div class="flex gap-4 mt-10 justify-center">
        <button
          class="px-14 py-5 rounded-2xl font-semibold text-white text-xl bg-black"
          type="button"
          @click="onPlace"
        >Разместить</button>
        <button
          class="px-10 py-5 rounded-2xl font-semibold text-black text-xl bg-gray-100"
          type="button"
          @click="saveAndExit"
        >123 Сохранить и выйти</button>
      </div>
      <div class="mt-4 text-center text-gray-500 text-base leading-tight max-w-xl">
        Вы публикуете объявление и данные в нём, чтобы их мог посмотреть кто угодно в интернете.<br>
        Вы также соглашаетесь
        <a href="https://www.avito.ru/legal/rules" target="_blank" class="underline">
          с правилами Авито
        </a>.
      </div>
    </form>
  </div>
</template>

<style scoped>
/* Ваши стили без изменений */
</style>
