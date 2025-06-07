<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnimatorRequest extends FormRequest
{
    /**
     * Определяем, может ли пользователь делать этот запрос
     */
    public function authorize(): bool
    {
        return auth()->check(); // Только авторизованные пользователи
    }

    /**
     * Правила валидации для формы
     */
    public function rules(): array
    {
        return [
            // Основные детали
            'details.title'       => 'required|string|max:255',
            'details.description' => 'nullable|string|max:5000',
            
            // Формат работы
            'workFormat.specialization'   => 'nullable|string|max:255',
            'workFormat.type'             => 'nullable|string|max:100',
            'workFormat.clients'          => 'nullable|array',
            'workFormat.clients.*'        => 'string|max:100',
            'workFormat.workFormats'      => 'nullable|array',
            'workFormat.workFormats.*'    => 'string|max:100',
            'workFormat.serviceProviders' => 'nullable|array',
            'workFormat.serviceProviders.*' => 'string|max:100',
            'workFormat.experience'       => 'nullable|string|max:100',
            
            // Прайс-лист
            'priceList.priceItems' => 'nullable|array',
            'priceList.priceItems.*.name' => 'required_with:priceList.priceItems|string|max:255',
            'priceList.priceItems.*.price' => 'nullable|numeric|min:0|max:999999',
            'priceList.priceItems.*.unit' => 'nullable|string|max:50',
            'priceList.priceItems.*.duration' => 'nullable|string|max:50',
            
            // Основная цена
            'price.value' => 'nullable|numeric|min:0|max:999999',
            'price.unit'  => 'nullable|string|max:50',
            'price.isBasePrice' => 'nullable|boolean',
            
            // Акции
            'actions.discount' => 'nullable|numeric|min:0|max:100',
            'actions.gift'     => 'nullable|string|max:500',
            
            // География
            'geo.city'       => 'nullable|string|max:255',
            'geo.address'    => 'nullable|string|max:500',
            'geo.visitType'  => 'nullable|string|in:no_visit,all_city,zones',
            
            // Контакты
            'contacts.phone'       => 'nullable|string|max:20',
            'contacts.email'       => 'nullable|email|max:255',
            'contacts.contactWays' => 'nullable|array',
            
            // Статус
            'status' => 'nullable|string|in:draft,pending,published',
        ];
    }

    /**
     * Сообщения об ошибках на русском языке
     */
    public function messages(): array
    {
        return [
            'details.title.required' => 'Название объявления обязательно для заполнения',
            'details.title.max' => 'Название объявления не должно превышать 255 символов',
            'details.description.max' => 'Описание не должно превышать 5000 символов',
            
            'price.value.numeric' => 'Цена должна быть числом',
            'price.value.min' => 'Цена не может быть отрицательной',
            
            'actions.discount.numeric' => 'Скидка должна быть числом',
            'actions.discount.max' => 'Скидка не может превышать 100%',
            
            'contacts.phone.max' => 'Телефон не должен превышать 20 символов',
            'contacts.email.email' => 'Некорректный email адрес',
            
            'status.in' => 'Некорректный статус объявления',
        ];
    }
}