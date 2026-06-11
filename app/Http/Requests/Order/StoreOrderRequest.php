<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }

    public function rules(): array
    {
        return [
            'phuong_thuc_thanh_toan' => ['required', Rule::in(['tien_mat', 'chuyen_khoan'])],
            'items' => ['required', 'array', 'min:1'],
            'items.*.ma_mon' => ['nullable', 'exists:mon,ma_mon'],
            'items.*.size' => ['nullable', Rule::in(['S', 'M', 'L'])],
            'items.*.so_luong' => ['nullable', 'integer', 'min:1'],
            'items.*.che_do' => ['nullable', Rule::in(['nong', 'lanh', 'chi_nong', 'chi_lanh'])],
            'items.*.ghi_chu' => ['nullable', 'string', 'max:255'],
            'items.*.tuy_chinh' => ['nullable', 'array'],
            'items.*.tuy_chinh.*' => ['nullable', 'integer', Rule::in([0, 25, 50, 75, 100])],
            'items.*.toppings' => ['nullable', 'array'],
            'items.*.toppings.*.ma_mon' => ['nullable', 'exists:mon,ma_mon'],
            'items.*.toppings.*.so_luong' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
