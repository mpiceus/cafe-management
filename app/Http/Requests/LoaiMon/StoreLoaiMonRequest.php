<?php

namespace App\Http\Requests\LoaiMon;

use Illuminate\Foundation\Http\FormRequest;

class StoreLoaiMonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isChuCuaHang();
    }

    public function rules(): array
    {
        return [
            'ten_loai_mon' => ['required', 'string', 'max:100'],
            'mo_ta' => ['nullable', 'string', 'max:255'],
        ];
    }
}
