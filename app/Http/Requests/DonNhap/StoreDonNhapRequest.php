<?php

namespace App\Http\Requests\DonNhap;

use Illuminate\Foundation\Http\FormRequest;

class StoreDonNhapRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->isChuCuaHang(); }

    public function rules(): array
    {
        return [
            'ma_nha_cung_cap' => ['required', 'exists:nha_cung_cap,ma_nha_cung_cap'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.ma_nguyen_lieu' => ['nullable', 'exists:nguyen_lieu,ma_nguyen_lieu'],
            'items.*.don_vi_mua' => ['nullable', 'in:g,kg,ml,l'],
            'items.*.so_luong_mua' => ['nullable', 'numeric', 'min:0.01'],
            'items.*.don_gia' => ['nullable', 'numeric', 'min:0', 'multiple_of:100'],
        ];
    }
}
