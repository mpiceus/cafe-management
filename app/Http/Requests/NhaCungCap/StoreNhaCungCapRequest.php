<?php

namespace App\Http\Requests\NhaCungCap;

use Illuminate\Foundation\Http\FormRequest;

class StoreNhaCungCapRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isChuCuaHang();
    }

    public function rules(): array
    {
        return [
            'ten_nha_cung_cap' => ['required', 'string', 'max:100'],
            'so_dien_thoai' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
            'dia_chi' => ['nullable', 'string', 'max:255'],
        ];
    }
}
