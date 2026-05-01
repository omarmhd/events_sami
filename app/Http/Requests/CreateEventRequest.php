<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_type' => 'required|in:conference,meeting,workshop,social,training',
            'registration_mode' => 'required|in:open,invitation_only,closed',
            'start_datetime' => 'required|date_format:Y-m-d H:i|after:now',
            'end_datetime' => 'required|date_format:Y-m-d H:i|after:start_datetime',
            'location_name' => 'required|string|max:255',
            'google_map_url' => 'nullable|url',
            'capacity' => 'required|integer|min:1|max:10000',
            'requires_manual_approval' => 'boolean',
            'allow_reentry' => 'boolean',
            'header_image' => 'nullable|image|max:5120',
            'footer_image' => 'nullable|image|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم الحدث مطلوب',
            'start_datetime.after' => 'يجب أن يكون تاريخ البدء في المستقبل',
            'end_datetime.after' => 'يجب أن يكون تاريخ الانتهاء بعد تاريخ البدء',
            'capacity.required' => 'السعة مطلوبة',
            'capacity.min' => 'يجب أن تكون السعة على الأقل 1',
        ];
    }
}
