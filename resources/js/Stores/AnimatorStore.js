import { defineStore } from 'pinia';
import axios from 'axios';

export const useAnimatorStore = defineStore('animator', {
    state: () => ({
        draftId: null,
        formData: {
            zones: 'city',
            name: '',
            description: '',
            services: [],
            heroes: '',
            photos: [],
            price: '',
            about: '',
            quick_booking: false,
            terms_accepted: false,
            city_id: 1,
            status: 'draft'
        }
    }),
    
    actions: {
        resetForm() {
            this.draftId = null;
            this.formData = {
                zones: 'city',
                name: '',
                description: '',
                services: [],
                heroes: '',
                photos: [],
                price: '',
                about: '',
                quick_booking: false,
                terms_accepted: false,
                city_id: 1,
                status: 'draft'
            };
        },
        
        async loadDraft(id) {
            try {
                const response = await axios.get(`/animators/${id}/draft`);
                if (response.data.success) {
                    this.draftId = id;
                    const draft = response.data.animator;
                    
                    // Заполняем данные формы из черновика
                    this.formData = {
                        zones: draft.zones || 'city',
                        name: draft.name || '',
                        description: draft.description || '',
                        services: draft.services || [],
                        heroes: draft.heroes || '',
                        photos: [], // Фотографии нужно загружать отдельно
                        price: draft.price || '',
                        about: draft.about || '',
                        quick_booking: draft.quick_booking || false,
                        terms_accepted: draft.terms_accepted || false,
                        city_id: draft.city_id || 1,
                        status: draft.status || 'draft'
                    };
                }
            } catch (error) {
                console.error('Error loading draft:', error);
                throw error;
            }
        },
        
        async saveDraft() {
            try {
                const formData = new FormData();
                
                // Добавляем основные данные
                Object.keys(this.formData).forEach(key => {
                    if (key === 'photos') {
                        // Фотографии обрабатываем отдельно
                        this.formData.photos.forEach((photo, index) => {
                            if (photo.file) {
                                formData.append(`photos[${index}]`, photo.file);
                            }
                        });
                    } else if (key === 'services') {
                        // Сервисы передаем как массив
                        this.formData.services.forEach((service, index) => {
                            formData.append(`services[${index}]`, service);
                        });
                    } else {
                        formData.append(key, this.formData[key]);
                    }
                });
                
                // Указываем что это черновик
                formData.append('is_draft', true);
                
                let response;
                if (this.draftId) {
                    // Обновляем существующий черновик
                    formData.append('_method', 'PUT');
                    response = await axios.post(`/animators/${this.draftId}`, formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    });
                } else {
                    // Создаем новый черновик
                    response = await axios.post('/animators', formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    });
                    
                    if (response.data.success && response.data.animator) {
                        this.draftId = response.data.animator.id;
                    }
                }
                
                return response.data.success;
            } catch (error) {
                console.error('Error saving draft:', error);
                throw error;
            }
        },
        
        async publish() {
            try {
                const formData = new FormData();
                
                // Добавляем все данные
                Object.keys(this.formData).forEach(key => {
                    if (key === 'photos') {
                        this.formData.photos.forEach((photo, index) => {
                            if (photo.file) {
                                formData.append(`photos[${index}]`, photo.file);
                            }
                        });
                    } else if (key === 'services') {
                        this.formData.services.forEach((service, index) => {
                            formData.append(`services[${index}]`, service);
                        });
                    } else {
                        formData.append(key, this.formData[key]);
                    }
                });
                
                // Указываем что это публикация
                formData.append('is_draft', false);
                formData.append('status', 'pending'); // Для модерации
                
                let response;
                if (this.draftId) {
                    formData.append('_method', 'PUT');
                    response = await axios.post(`/animators/${this.draftId}`, formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    });
                } else {
                    response = await axios.post('/animators', formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    });
                }
                
                if (response.data.success) {
                    this.resetForm();
                }
                
                return response.data.success;
            } catch (error) {
                console.error('Error publishing:', error);
                throw error;
            }
        }
    }
});