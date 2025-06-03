import { defineStore } from 'pinia'

export const useLocationStore = defineStore('location', {
  state: () => ({
    city: localStorage.getItem('selectedCity') || 'Москва'
  }),
  actions: {
    setCity(newCity) {
      this.city = newCity
      localStorage.setItem('selectedCity', newCity)
    }
  }
})
