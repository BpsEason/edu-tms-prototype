<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MaterialRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'type' => 'required|in:pdf,video,url',
            'file' => 'nullable|file|mimes:pdf,mp4,mov,mkv|max:204800', # 200MB limit for files
            'url' => 'nullable|url',
        ];
    }
    
    public function messages()
    {
        return [
            'title.required' => '教材標題為必填項目。',
            'type.required' => '請選擇教材類型。',
            'type.in' => '教材類型無效。',
            'file.mimes' => '檔案格式不支援。請上傳 PDF 或影片（MP4, MOV, MKV）檔案。',
            'file.max' => '檔案大小不可超過 200MB。',
            'url.url' => '請輸入有效的 URL 格式。',
        ];
    }
}
