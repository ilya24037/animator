// resources/js/Stores/useLocationStore.js
import { defineStore } from 'pinia'
import { usePage } from '@inertiajs/vue3'

export const useLocationStore = defineStore('location', {
  state: () => ({
    city: 'Москва' // Дефолтное значение
  }),
  
  getters: {
    // Получаем город из пропсов Inertia или из состояния
    currentCity: (state) => {
      const page = usePage()
      return page.props.userCity || state.city
    }
  },
  
  actions: {
    setCity(newCity) {
      this.city = newCity
      
      // Отправляем на сервер для сохранения в сессии/куках
      if (typeof window !== 'undefined') {
        // Используем Inertia для консистентности
        window.$inertia.post('/api/user/city', { city: newCity }, {
          preserveState: true,
          preserveScroll: true,
          onSuccess: () => {
            console.log('Город сохранен:', newCity)
          }
        })
      }
    },
    
    // Инициализация из серверных данных
    initFromServer() {
      const page = usePage()
      if (page.props.userCity) {
        this.city = page.props.userCity
      }
    }
  }
})

// Альтернативный вариант с cookies (если нужна персистентность)
export const useLocationStoreWithCookies = defineStore('location', {
  state: () => ({
    city: 'Москва'
  }),
  
  actions: {
    setCity(newCity) {
      this.city = newCity
      
      // Сохраняем в cookie (работает везде)
      if (typeof document !== 'undefined') {
        document.cookie = `selectedCity=${encodeURIComponent(newCity)}; path=/; max-age=${60*60*24*365}`
      }
    },
    
    initFromCookie() {
      if (typeof document !== 'undefined') {
        const match = document.cookie.match(/selectedCity=([^;]+)/)
        if (match) {
          this.city = decodeURIComponent(match[1])
        }
      }
    }
  }
})