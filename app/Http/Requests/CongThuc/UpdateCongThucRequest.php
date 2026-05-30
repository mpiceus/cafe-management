<?php

namespace App\Http\Requests\CongThuc;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCongThucRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->isChuCuaHang(); }

    public function rules(): array
    {
        return [
            'items' => ['nullable', 'array'],
            'items.*.ma_nguyen_lieu' => ['nullable', 'exists:nguyen_lieu,ma_nguyen_lieu'],
            'items.*.so_luong' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
