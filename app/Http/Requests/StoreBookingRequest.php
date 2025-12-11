<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:booking_time',
            'number_of_guests' => 'required|integer|min:1|max:50',
            'location_preference' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'end_time.required' => 'Vui lòng chọn thời gian kết thúc',
            'end_time.after' => 'Thời gian kết thúc phải sau thời gian bắt đầu',
        ];
    }
}
