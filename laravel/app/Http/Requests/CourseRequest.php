<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $courseId = $this->route('course') ? $this->route('course')->id : null;
        return [
            'title' => 'required|string|max:255|unique:courses,title,' . $courseId,
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => '課程標題為必填項目。',
            'title.unique' => '此課程標題已存在。',
            'category_id.required' => '請選擇一個課程分類。',
            'category_id.exists' => '選擇的課程分類不存在。',
            'thumbnail.image' => '上傳的檔案必須是圖片。',
            'thumbnail.mimes' => '圖片格式必須是 jpeg, png, jpg, gif 或 svg。',
            'thumbnail.max' => '圖片大小不可超過 2MB。',
        ];
    }
}
