<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTableRequest extends FormRequest
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
        $tableId = $this->route('id');

        return [
            'name' => 'sometimes|required|string|max:255',
            'number' => 'sometimes|required|string|max:50|unique:tables,number,' . $tableId,
            'capacity' => 'sometimes|required|integer|min:1|max:100',
            'area' => 'nullable|string|max:255',
            'status' => 'nullable|in:available,reserved,occupied,maintenance',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ];
    }
}
