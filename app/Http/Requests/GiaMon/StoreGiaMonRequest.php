<?php

namespace App\Http\Requests\GiaMon;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGiaMonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isChuCuaHang();
    }

    public function rules(): array
    {
        return [
            'size' => ['required', Rule::in(['S', 'M', 'L'])],
            'gia' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'ngay_ap_dung' => ['required', 'date'],
        ];
    }
}
