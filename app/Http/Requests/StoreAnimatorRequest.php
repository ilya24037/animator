<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnimatorRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'details.title' => 'required|string|min:3|max:255',
            'details.description' => 'required|string|min:10',
            'workFormat.type' => 'required|string',
            'workFormat.specialization' => 'required|string',
            'price.value' => 'required|numeric|min:0',
            'geo.city' => 'required|string',
            'contacts.phone' => 'required|string',
            'status' => 'required|in:draft,published',
            // 'media_files.*' => 'file|mimes:jpg,jpeg,png' (если надо)
        ];
    }

    public function messages()
    {
        return [
            'details.title.required' => 'Укажите название объявления',
            'details.description.required' => 'Добавьте описание',
            // ... другие сообщения
        ];
    }
}
