<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
            'user_id' => 'nullable|integer|exists:users,id',
            'sort_by' => 'nullable|string|in:id,total_amount,is_completed,created_at',
            'sort_dir' => 'nullable|string|in:asc,desc',
            'total_amount_from' => 'nullable|numeric|min:0',
            'total_amount_to' => 'nullable|numeric|min:0',
            'is_completed' => 'nullable|boolean',
        ];
    }
}
