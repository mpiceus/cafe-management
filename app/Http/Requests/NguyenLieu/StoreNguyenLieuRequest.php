<?php

namespace App\Http\Requests\NguyenLieu;

use Illuminate\Foundation\Http\FormRequest;

class StoreNguyenLieuRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->isChuCuaHang(); }

    public function rules(): array
    {
        return [
            'ten_nguyen_lieu' => ['required', 'string', 'max:100'],
            'don_vi_tinh' => ['required', 'in:g,ml'],
            'ton_kho' => ['required', 'numeric', 'min:0'],
            'so_luong_toi_thieu' => ['required', 'numeric', 'min:0'],
            'ma_nha_cung_cap' => ['required', 'exists:nha_cung_cap,ma_nha_cung_cap'],
            'ti_le_su_dung' => ['required', 'numeric', 'min:0.01', 'max:1'],
            'duoc_tuy_chinh' => ['nullable', 'boolean'],
            'duoc_su_dung' => ['nullable', 'boolean'],
        ];
    }
}
