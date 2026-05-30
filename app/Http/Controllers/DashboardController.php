<?php

namespace App\Http\Controllers;

use App\Models\NguoiDung;
use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        return match (auth()->user()?->chuc_vu) {
            NguoiDung::CHUC_VU_CHU_CUA_HANG => redirect()->route('bao-cao.index'),
            NguoiDung::CHUC_VU_NHAN_VIEN_ORDER => redirect()->route('order.index'),
            NguoiDung::CHUC_VU_NHAN_VIEN_PHA_CHE => redirect()->route('pha-che.index'),
            default => redirect()->route('login'),
        };
    }
}
