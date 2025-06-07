// resources/js/Pages/Animators/Create.vue - исправленная версия

import { router } from '@inertiajs/vue3'

// ❌ Старый подход с axios
async function saveDraftOld() {
  try {
    const response = await axios.post('/animators/draft', {
      ...form,
      draft_id: currentDraftId.value
    })
    // ...
  } catch (error) {
    console.error('Ошибка:', error)
  }
}

// ✅ Новый подход с Inertia
function saveDraft(silent = false) {
  router.post('/animators/draft', 
    {
      ...form,
      draft_id: currentDraftId.value
    },
    {
      preserveState: true,
      preserveScroll: true,
      only: ['animator', 'flash'], // Обновляем только нужные данные
      onSuccess: (page) => {
        if (page.props.animator?.id) {
          currentDraftId.value = page.props.animator.id
        }
        lastSavedData.value = JSON.stringify(form)
        
        if (!silent && page.props.flash?.success) {
          // Flash сообщения автоматически подхватит ToastNotifications
        }
      },
      onError: (errors) => {
        console.error('Ошибка сохранения:', errors)
      }
    }
  )
}

// resources/js/Services/api.js - сервис для работы с API через Inertia
export const api = {
  // Получение данных
  get(url, options = {}) {
    return new Promise((resolve, reject) => {
      router.get(url, {}, {
        preserveState: true,
        preserveScroll: true,
        only: ['data'],
        ...options,
        onSuccess: (page) => resolve(page.props.data),
        onError: (errors) => reject(errors)
      })
    })
  },

  // Отправка данных
  post(url, data = {}, options = {}) {
    return new Promise((resolve, reject) => {
      router.post(url, data, {
        preserveState: true,
        preserveScroll: true,
        ...options,
        onSuccess: (page) => resolve(page.props),
        onError: (errors) => reject(errors)
      })
    })
  },

  // Обновление
  put(url, data = {}, options = {}) {
    return new Promise((resolve, reject) => {
      router.put(url, data, {
        preserveState: true,
        preserveScroll: true,
        ...options,
        onSuccess: (page) => resolve(page.props),
        onError: (errors) => reject(errors)
      })
    })
  },

  // Удаление
  delete(url, options = {}) {
    return new Promise((resolve, reject) => {
      router.delete(url, {
        preserveState: true,
        preserveScroll: true,
        ...options,
        onSuccess: (page) => resolve(page.props),
        onError: (errors) => reject(errors)
      })
    })
  }
}

// Пример использования в компоненте
import { api } from '@/Services/api'

// В компоненте Categories
async function loadCategories() {
  try {
    loading.value = true
    const data = await api.get('/api/categories')
    categories.value = data.categories
  } catch (error) {
    console.error('Не удалось загрузить категории', error)
  } finally {
    loading.value = false
  }
}

// Для загрузки файлов используем router.post с FormData
function uploadFiles() {
  const formData = new FormData()
  
  form.media.files.forEach((file, index) => {
    formData.append(`files[${index}]`, file)
  })
  
  // Добавляем остальные данные
  Object.keys(form).forEach(key => {
    if (key !== 'media') {
      formData.append(key, JSON.stringify(form[key]))
    }
  })
  
  router.post('/animators', formData, {
    forceFormData: true, // Важно для файлов!
    preserveState: false,
    onSuccess: () => {
      console.log('Файлы загружены')
    }
  })
}

// app/Http/Controllers/AnimatorController.php - изменения на бэкенде
public function store(Request $request)
{
    // ... валидация и логика ...
    
    // Для Inertia возвращаем redirect с данными
    return redirect()
        ->route('profile.items', ['tab' => 'draft'])
        ->with('success', 'Объявление сохранено')
        ->with('animator', $animator); // Данные будут в page.props
}

// Или для AJAX-подобных запросов
public function apiStore(Request $request) 
{
    // ... логика ...
    
    return Inertia::render('DummyPage', [
        'data' => [
            'animator' => $animator,
            'success' => true
        ]
    ]);
}