// utils/useAnimatorForm.js

import { reactive } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { useValidator } from '@/utils/useValidator.js'
import { route } from 'ziggy-js'

export function useAnimatorForm() {
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

  const validateStep = () => validate().ok

  const submitForm = () => {
    form.status = 'pending'
    Inertia.post(route('animators.store'), form, {
      onSuccess: () => alert('Объявление отправлено!'),
      onError: () => alert('Ошибка при отправке')
    })
  }

  const saveDraft = () => {
    form.status = 'draft'
    Inertia.post(route('animators.store'), form, {
      onSuccess: () => alert('Черновик сохранён!'),
      onError: () => alert('Ошибка при сохранении')
    })
  }

  return { form, errors, validateStep, submitForm, saveDraft }
}
