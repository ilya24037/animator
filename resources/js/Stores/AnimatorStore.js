import { defineStore } from 'pinia'
import axios from 'axios'
import { router } from '@inertiajs/vue3'

export const useAnimatorStore = defineStore('animator', {
  state: () => ({
    draftId: null,
    isSaving: false,
    lastSaved: null,
    saveError: null,
    formData: {
      // Категория
      category_id: null,
      subcategory_id: null,
      
      // Основная информация
      name: '',
      description: '',
      
      // Услуги
      services: [],
      specialization: '',
      
      // Медиа
      photos: [],
      youtube_url: null,
      
      // Цена
      price: null,
      price_type: 'fixed', // fixed, negotiable, free
      price_unit: 'service', // service, hour, day, month
      
      // Локация
      city_id: null,
      address: '',
      zones: 'city', // city, district, custom
      districts: [],
      service_radius: null,
      
      // Контакты
      phone: '',
      contact_name: '',
      contact_methods: ['phone', 'message'], // phone, message, whatsapp
      show_in_messages: true,
      
      // Дополнительно
      quick_booking: false,
      online_service: false,
      home_visit: true,
      
      // Рабочее время
      schedule: {
        enabled: false,
        days: {
          mon: { enabled: true, from: '09:00', to: '18:00' },
          tue: { enabled: true, from: '09:00', to: '18:00' },
          wed: { enabled: true, from: '09:00', to: '18:00' },
          thu: { enabled: true, from: '09:00', to: '18:00' },
          fri: { enabled: true, from: '09:00', to: '18:00' },
          sat: { enabled: false, from: '10:00', to: '16:00' },
          sun: { enabled: false, from: '10:00', to: '16:00' }
        }
      },
      
      // Системные
      status: 'draft',
      terms_accepted: false
    }
  }),
  
  getters: {
    // Проверка заполненности формы
    isFormEmpty: (state) => {
      return !state.formData.name && 
             !state.formData.description && 
             state.formData.services.length === 0
    },
    
    // Прогресс заполнения
    completionProgress: (state) => {
      let completed = 0
      const total = 8
      
      if (state.formData.category_id && state.formData.subcategory_id) completed++
      if (state.formData.name && state.formData.description) completed++
      if (state.formData.services.length > 0) completed++
      if (state.formData.photos.length > 0) completed++
      if (state.formData.price) completed++
      if (state.formData.city_id) completed++
      if (state.formData.phone) completed++
      if (state.formData.terms_accepted) completed++
      
      return Math.round((completed / total) * 100)
    },
    
    // Готовность к публикации
    canPublish: (state) => {
      return state.formData.category_id &&
             state.formData.subcategory_id &&
             state.formData.name &&
             state.formData.description &&
             state.formData.services.length > 0 &&
             state.formData.price &&
             state.formData.city_id &&
             state.formData.phone &&
             state.formData.terms_accepted
    }
  },
  
  actions: {
    // Сброс формы
    resetForm() {
      this.draftId = null
      this.isSaving = false
      this.lastSaved = null
      this.saveError = null
      
      // Очистка медиафайлов
      this.formData.photos.forEach(photo => {
        if (photo.preview) {
          URL.revokeObjectURL(photo.preview)
        }
      })
      
      // Сброс данных формы
      Object.assign(this.formData, this.$state.formData)
    },
    
    // Загрузка черновика
    async loadDraft(id) {
      try {
        const response = await axios.get(`/api/animators/draft/${id}`)
        if (response.data.success) {
          this.draftId = id
          const draft = response.data.data
          
          // Заполнение формы данными черновика
          Object.keys(draft).forEach(key => {
            if (key in this.formData) {
              this.formData[key] = draft[key]
            }
          })
          
          // Восстановление превью фото
          if (this.formData.photos?.length > 0) {
            this.formData.photos = this.formData.photos.map(photo => ({
              ...photo,
              preview: photo.url || photo.preview
            }))
          }
          
          return true
        }
      } catch (error) {
        console.error('Ошибка загрузки черновика:', error)
        this.saveError = 'Не удалось загрузить черновик'
        return false
      }
    },
    
    // Сохранение черновика
    async saveDraft() {
      if (this.isSaving || this.isFormEmpty) return false
      
      this.isSaving = true
      this.saveError = null
      
      try {
        const formData = this.prepareFormData()
        formData.append('is_draft', 'true')
        formData.append('status', 'draft')
        
        let response
        if (this.draftId) {
          formData.append('_method', 'PUT')
          response = await axios.post(`/api/animators/${this.draftId}`, formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
          })
        } else {
          response = await axios.post('/api/animators', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
          })
        }
        
        if (response.data.success) {
          if (!this.draftId) {
            this.draftId = response.data.data.id
          }
          this.lastSaved = new Date()
          return true
        }
      } catch (error) {
        console.error('Ошибка сохранения черновика:', error)
        this.saveError = 'Ошибка автосохранения'
        return false
      } finally {
        this.isSaving = false
      }
    },
    
    // Публикация объявления
    async publish() {
      if (!this.canPublish) return false
      
      this.isSaving = true
      this.saveError = null
      
      try {
        const formData = this.prepareFormData()
        formData.append('is_draft', 'false')
        formData.append('status', 'pending')
        
        let response
        if (this.draftId) {
          formData.append('_method', 'PUT')
          response = await axios.post(`/api/animators/${this.draftId}`, formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
          })
        } else {
          response = await axios.post('/api/animators', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
          })
        }
        
        if (response.data.success) {
          this.resetForm()
          return true
        }
      } catch (error) {
        console.error('Ошибка публикации:', error)
        if (error.response?.data?.errors) {
          throw error.response.data.errors
        }
        this.saveError = 'Ошибка при публикации'
        return false
      } finally {
        this.isSaving = false
      }
    },
    
    // Подготовка данных для отправки
    prepareFormData() {
      const formData = new FormData()
      
      // Простые поля
      const simpleFields = [
        'category_id', 'subcategory_id', 'name', 'description',
        'price', 'price_type', 'price_unit', 'city_id', 'address',
        'zones', 'service_radius', 'phone', 'contact_name',
        'quick_booking', 'online_service', 'home_visit',
        'youtube_url', 'specialization'
      ]
      
      simpleFields.forEach(field => {
        if (this.formData[field] !== null && this.formData[field] !== undefined) {
          formData.append(field, this.formData[field])
        }
      })
      
      // Массивы
      if (this.formData.services.length > 0) {
        this.formData.services.forEach((service, index) => {
          if (typeof service === 'object') {
            formData.append(`services[${index}][name]`, service.name)
            formData.append(`services[${index}][price]`, service.price || '')
          } else {
            formData.append(`services[${index}]`, service)
          }
        })
      }
      
      if (this.formData.districts.length > 0) {
        this.formData.districts.forEach((district, index) => {
          formData.append(`districts[${index}]`, district)
        })
      }
      
      if (this.formData.contact_methods.length > 0) {
        this.formData.contact_methods.forEach((method, index) => {
          formData.append(`contact_methods[${index}]`, method)
        })
      }
      
      // Фотографии
      if (this.formData.photos.length > 0) {
        this.formData.photos.forEach((photo, index) => {
          if (photo.file) {
            formData.append(`photos[${index}]`, photo.file)
          } else if (photo.id) {
            formData.append(`existing_photos[${index}]`, photo.id)
          }
        })
      }
      
      // Расписание
      if (this.formData.schedule.enabled) {
        formData.append('schedule[enabled]', '1')
        Object.keys(this.formData.schedule.days).forEach(day => {
          const dayData = this.formData.schedule.days[day]
          formData.append(`schedule[days][${day}][enabled]`, dayData.enabled ? '1' : '0')
          formData.append(`schedule[days][${day}][from]`, dayData.from)
          formData.append(`schedule[days][${day}][to]`, dayData.to)
        })
      }
      
      // Флаги
      formData.append('show_in_messages', this.formData.show_in_messages ? '1' : '0')
      formData.append('terms_accepted', this.formData.terms_accepted ? '1' : '0')
      
      return formData
    },
    
    // Валидация шага
    validateStep(stepNumber) {
      const errors = {}
      
      switch (stepNumber) {
        case 1: // Категория
          if (!this.formData.category_id) errors.category_id = 'Выберите категорию'
          if (!this.formData.subcategory_id) errors.subcategory_id = 'Выберите подкатегорию'
          break
          
        case 2: // Описание
          if (!this.formData.name) errors.name = 'Укажите название'
          if (this.formData.name && this.formData.name.length < 3) {
            errors.name = 'Название слишком короткое'
          }
          if (!this.formData.description) errors.description = 'Добавьте описание'
          if (this.formData.description && this.formData.description.length < 10) {
            errors.description = 'Описание слишком короткое'
          }
          break
          
        case 3: // Услуги
          if (this.formData.services.length === 0) {
            errors.services = 'Добавьте хотя бы одну услугу'
          }
          break
          
        case 5: // Цена
          if (!this.formData.price && this.formData.price_type !== 'free') {
            errors.price = 'Укажите цену'
          }
          break
          
        case 6: // Локация
          if (!this.formData.city_id) errors.city_id = 'Выберите город'
          break
          
        case 7: // Контакты
          if (!this.formData.phone) errors.phone = 'Укажите телефон'
          if (this.formData.phone && !this.validatePhone(this.formData.phone)) {
            errors.phone = 'Неверный формат телефона'
          }
          break
      }
      
      return errors
    },
    
    // Валидация телефона
    validatePhone(phone) {
      const cleaned = phone.replace(/\D/g, '')
      return cleaned.length === 11 && cleaned.startsWith('7')
    }
  }
})