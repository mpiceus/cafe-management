<?php

namespace App\Http\Requests\NguoiDung;

use App\Models\NguoiDung;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateNguoiDungRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->chuc_vu === NguoiDung::CHUC_VU_CHU_CUA_HANG;
    }

    public function rules(): array
    {
        $nguoiDung = $this->route('nguoiDung');

        return [
            'ho_ten' => ['required', 'string', 'max:100'],
            'ten_dang_nhap' => [
                'required',
                'string',
                'max:50',
                Rule::unique('nguoi_dung', 'ten_dang_nhap')->ignore($nguoiDung?->ma_nguoi_dung, 'ma_nguoi_dung'),
            ],
            'mat_khau' => ['nullable', 'string', 'min:6', 'max:255'],
            'chuc_vu' => ['required', Rule::in([
                NguoiDung::CHUC_VU_CHU_CUA_HANG,
                NguoiDung::CHUC_VU_NHAN_VIEN_ORDER,
                NguoiDung::CHUC_VU_NHAN_VIEN_PHA_CHE,
            ])],
            'trang_thai' => ['required', Rule::in([
                NguoiDung::TRANG_THAI_HOAT_DONG,
                NguoiDung::TRANG_THAI_NGUNG_HOAT_DONG,
            ])],
        ];
    }
}
