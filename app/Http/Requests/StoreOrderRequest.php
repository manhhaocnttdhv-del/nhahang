<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'order_type' => 'required|in:dine_in,takeaway,delivery',
            'items' => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string|max:500',
            'table_id' => 'nullable|exists:tables,id|required_if:order_type,dine_in',
            'customer_name' => 'nullable|string|max:255|required_if:order_type,delivery,takeaway',
            'customer_phone' => 'nullable|string|max:20|required_if:order_type,delivery,takeaway',
            'customer_address' => 'nullable|string|max:500|required_if:order_type,delivery',
            'voucher_code' => 'nullable|string|exists:vouchers,code',
            'notes' => 'nullable|string|max:1000',
        ];
    }
}
