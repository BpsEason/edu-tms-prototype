<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChapterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'order' => 'required|integer|min:0',
        ];
    }
    
    public function messages()
    {
        return [
            'title.required' => '章節標題為必填項目。',
            'order.required' => '章節順序為必填項目。',
            'order.integer' => '章節順序必須是整數。',
            'order.min' => '章節順序不能小於 0。',
        ];
    }
}
