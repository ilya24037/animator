import { defineStore } from 'pinia'
import axios from 'axios'

export const useAnimatorFormStore = defineStore('animatorForm', {
  state: () => ({
    // ID черновика
    draftId: null,
    
    // Состояние формы
    form: {
      details: { 
        title: '', 
        description: '' 
      },
      workFormat: {
        specialization: '',
        type: 'private', // Значение по умолчанию!
        clients: [],
        workFormats: [],
        serviceProviders: [],
        experience: ''
      },
      priceList: { 
        priceItems: [] 
      },
      price: { 
        value: '', 
        unit: 'за час', 
        isBasePrice: false 
      },
      actions: { 
        discount: null, 
        gift: '' 
      },
      media: { 
        files: [], 
        videoUrl: '' 
      },
      geo: { 
        city: 'Москва', // Значение по умолчанию
        address: '', 
        visitType: 'all_city' // Значение по умолчанию
      },
      contacts: { 
        phone: '', 
        email: '', 
        contactWays: ['any'] 
      }
    },
    
    // Статусы
    isSaving: false,
    lastSaved: null,
    saveError: null
  }),
  
  getters: {
    // Проверка заполненности формы
    isFormEmpty: (state) => {
      return !state.form.details.title && 
             !state.form.details.description &&
             state.form.priceList.priceItems.length === 0
    },
    
    // Статус сохранения для UI
    saveStatus: (state) => {
      if (state.isSaving) return 'saving'
      if (state.saveError) return 'error'
      if (state.lastSaved) return 'saved'
      return 'idle'
    }
  },
  
  actions: {
    // Инициализация формы
    initForm(draftData = null) {
      if (draftData) {
        this.draftId = draftData.id
        // Восстанавливаем данные с проверкой
        this.form.details.title = draftData.title || ''
        this.form.details.description = draftData.description || ''
        
        if (draftData.work_format) {
          Object.assign(this.form.workFormat, draftData.work_format)
        }
        
        if (draftData.price_list) {
          this.form.priceList = draftData.price_list
        }
        
        // ... остальные поля
      }
    },
    
    // Автосохранение черновика
    async autoSave() {
      // Не сохраняем пустую форму
      if (this.isFormEmpty) return
      
      this.isSaving = true
      this.saveError = null
      
      try {
        const response = await axios.post('/api/animators/draft', {
          draft_id: this.draftId,
          ...this.form
        })
        
        if (response.data.success) {
          this.draftId = response.data.animator.id
          this.lastSaved = new Date()
        }
      } catch (error) {
        console.error('Ошибка автосохранения:', error)
        this.saveError = 'Не удалось сохранить черновик'
      } finally {
        this.isSaving = false
      }
    },
    
    // Публикация объявления
    async publish() {
      // Валидация перед отправкой
      const errors = this.validateForm()
      if (Object.keys(errors).length > 0) {
        return { success: false, errors }
      }
      
      try {
        const response = await axios.post('/api/animators/publish', {
          draft_id: this.draftId,
          ...this.form
        })
        
        return { success: true, data: response.data }
      } catch (error) {
        return { 
          success: false, 
          errors: error.response?.data?.errors || {}
        }
      }
    },
    
    // Валидация формы
    validateForm() {
      const errors = {}
      
      if (!this.form.details.title) {
        errors['details.title'] = 'Укажите название объявления'
      }
      
      if (!this.form.workFormat.type) {
        errors['workFormat.type'] = 'Выберите формат работы'
      }
      
      // ... остальные проверки
      
      return errors
    }
  }
})