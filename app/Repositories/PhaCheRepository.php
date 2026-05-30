<?php

namespace App\Repositories;

use App\Models\ChiTietHoaDon;
use App\Models\HoaDon;
use App\Repositories\Contracts\PhaCheRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PhaCheRepository implements PhaCheRepositoryInterface
{
    public function danhSachCho(): Collection
    {
        return ChiTietHoaDon::query()
            ->with(['hoaDon', 'mon.congThucs.nguyenLieu', 'tuyChinhs.nguyenLieu', 'toppings.mon.congThucs.nguyenLieu'])
            ->whereHas('hoaDon', fn ($query) => $query->where('trang_thai', HoaDon::TRANG_THAI_DA_THANH_TOAN))
            ->where('trang_thai_pha_che', '!=', ChiTietHoaDon::PHA_CHE_XONG)
            ->orderBy('ma_chi_tiet')
            ->get();
    }

    public function donChoPhaChe(): Collection
    {
        return HoaDon::query()
            ->with([
                'chiTiets' => fn ($query) => $query
                    ->where('trang_thai_pha_che', '!=', ChiTietHoaDon::PHA_CHE_XONG)
                    ->with(['mon.congThucs.nguyenLieu', 'tuyChinhs.nguyenLieu', 'toppings.mon.congThucs.nguyenLieu']),
            ])
            ->where('trang_thai', HoaDon::TRANG_THAI_DA_THANH_TOAN)
            ->orderBy('thoi_gian_tao')
            ->get()
            ->filter(fn (HoaDon $hoaDon) => $hoaDon->chiTiets->isNotEmpty())
            ->values();
    }

    public function capNhatTrangThai(ChiTietHoaDon $chiTiet, string $trangThai): ChiTietHoaDon
    {
        return DB::transaction(function () use ($chiTiet, $trangThai) {
            $chiTiet->update(['trang_thai_pha_che' => $trangThai]);
            $hoaDon = $chiTiet->hoaDon()->with('chiTiets')->firstOrFail();

            if ($hoaDon->chiTiets->every(fn ($item) => $item->trang_thai_pha_che === ChiTietHoaDon::PHA_CHE_XONG)) {
                $hoaDon->update(['trang_thai' => HoaDon::TRANG_THAI_DA_HOAN_THANH]);
            }

            return $chiTiet->refresh();
        });
    }
}
