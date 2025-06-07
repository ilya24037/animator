import { defineStore } from 'pinia'
import { ref, reactive } from 'vue'
import { router } from '@inertiajs/vue3'

export const useAnimatorFormStore = defineStore('animatorForm', () => {
  const form = reactive({
    details: {},
    workFormat: {},
    // ... остальные поля ...
    status: 'draft',
  })

  const isSaving = ref(false)
  const lastSaved = ref(null)
  const saveError = ref(null)

  function initForm(draft) {
    Object.assign(form, draft)
    form.status = draft.status || 'draft'
  }

  // Метод автосохранения (PUT или POST в зависимости от draft)
  async function autoSave() {
    isSaving.value = true
    saveError.value = null

    try {
      // если есть id — обновить, иначе создать черновик
      if (form.id) {
        await router.put(`/animators/${form.id}`, { ...form, status: 'draft' }, { preserveState: true })
      } else {
        const res = await router.post('/animators', { ...form, status: 'draft' }, { preserveState: true })
        if (res?.props?.item?.id) {
          form.id = res.props.item.id // сохраняем id черновика
        }
      }
      lastSaved.value = new Date()
      return { success: true }
    } catch (e) {
      saveError.value = 'Ошибка автосохранения'
      return { success: false, errors: e?.response?.data?.errors || {} }
    } finally {
      isSaving.value = false
    }
  }

  // Главный метод публикации (publish)
  async function publish(status = 'published') {
    isSaving.value = true
    saveError.value = null

    try {
      let response
      // Если уже есть id — обновить (PUT), иначе создать (POST)
      if (form.id) {
        response = await router.put(`/animators/${form.id}`, { ...form, status }, { preserveState: true })
      } else {
        response = await router.post('/animators', { ...form, status }, { preserveState: true })
        if (response?.props?.item?.id) {
          form.id = response.props.item.id
        }
      }
      lastSaved.value = new Date()
      return { success: true }
    } catch (e) {
      saveError.value = 'Ошибка публикации'
      return { success: false, errors: e?.response?.data?.errors || {} }
    } finally {
      isSaving.value = false
    }
  }

  return {
    form,
    isSaving,
    lastSaved,
    saveError,
    initForm,
    autoSave,
    publish,
  }
})
