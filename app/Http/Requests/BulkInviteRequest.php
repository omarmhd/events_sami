<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkInviteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() &&
            $this->route('event')->company_id === auth()->user()->company_id;
    }

    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:csv,txt|max:5120',
            'send_immediately' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'الملف مطلوب',
            'file.mimes' => 'يجب أن يكون الملف من نوع CSV أو TXT',
            'file.max' => 'يجب ألا يتجاوز حجم الملف 5 MB',
        ];
    }
}
