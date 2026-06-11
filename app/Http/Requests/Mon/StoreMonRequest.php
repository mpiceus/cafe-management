<?php

namespace App\Http\Requests\Mon;

use App\Models\Mon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isChuCuaHang();
    }

    public function rules(): array
    {
        return [
            'ma_loai_mon' => ['required', 'integer', 'exists:loai_mon,ma_loai_mon'],
            'ten_mon' => ['required', 'string', 'max:100'],
            'mo_ta' => ['nullable', 'string', 'max:255'],
            'hinh_anh_file' => ['nullable', 'image', 'max:2048'],
            'size' => ['required', Rule::in(['S', 'M', 'L'])],
            'gia' => ['required', 'numeric', 'min:0'],
            'che_do_phuc_vu' => ['required', Rule::in([
                Mon::CHE_DO_CA_HAI,
                Mon::CHE_DO_CHI_NONG,
                Mon::CHE_DO_CHI_LANH,
                Mon::CHE_DO_KHONG_AP_DUNG,
            ])],
            'cho_them_topping' => ['nullable', 'boolean'],
            'trang_thai' => ['required', Rule::in([
                Mon::TRANG_THAI_DANG_BAN,
                Mon::TRANG_THAI_DUNG_BAN,
            ])],
            'ngay_ap_dung' => ['nullable', 'date'],
        ];
    }
}
