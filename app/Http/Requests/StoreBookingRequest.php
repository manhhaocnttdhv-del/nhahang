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
            'end_time' => [
                'required',
                'date_format:H:i',
                function ($attribute, $value, $fail) {
                    $bookingTime = $this->input('booking_time');
                    $bookingDate = $this->input('booking_date');
                    
                    if (!$bookingTime || !$bookingDate) {
                        return;
                    }
                    
                    try {
                        $start = \Carbon\Carbon::parse($bookingDate . ' ' . $bookingTime);
                        $end = \Carbon\Carbon::parse($bookingDate . ' ' . $value);
                        
                        // Kiểm tra end_time phải sau start_time
                        if ($end->lte($start)) {
                            $fail('Thời gian kết thúc phải sau thời gian bắt đầu.');
                            return;
                        }
                        
                        // Kiểm tra duration tối thiểu 30 phút
                        $durationMinutes = $start->diffInMinutes($end);
                        if ($durationMinutes < 30) {
                            $fail('Thời gian đặt bàn tối thiểu là 30 phút.');
                            return;
                        }
                        
                        // Kiểm tra duration tối đa 4 giờ (240 phút)
                        if ($durationMinutes > 240) {
                            $fail('Thời gian đặt bàn tối đa là 4 giờ.');
                            return;
                        }
                    } catch (\Exception $e) {
                        $fail('Thời gian không hợp lệ.');
                    }
                },
            ],
            'number_of_guests' => 'required|integer|min:1|max:50',
            'location_preference' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'end_time.required' => 'Vui lòng chọn thời gian kết thúc',
            'end_time.date_format' => 'Định dạng thời gian không hợp lệ',
        ];
    }
}
