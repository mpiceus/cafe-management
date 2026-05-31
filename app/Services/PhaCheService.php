<?php

namespace App\Services;

use App\Models\ChiTietHoaDon;
use App\Repositories\Contracts\PhaCheRepositoryInterface;
use Illuminate\Support\Collection;

class PhaCheService
{
    public function __construct(private readonly PhaCheRepositoryInterface $repository) {}

    public function danhSachCho(): Collection
    {
        return $this->repository->danhSachCho();
    }

    public function donChoPhaChe(): Collection
    {
        return $this->repository->donChoPhaChe()->map(function ($hoaDon) {
            $hoaDon->setAttribute('thoi_gian_tao_iso', $hoaDon->thoi_gian_tao?->toISOString());
            $hoaDon->setAttribute('thoi_gian_tao_text', $hoaDon->thoi_gian_tao?->format('d/m/Y H:i'));
            $hoaDon->chiTiets->transform(function ($chiTiet) {
                $ingredients = collect();

                foreach ($chiTiet->mon->congThucs as $congThuc) {
                    $tiLe = optional($chiTiet->tuyChinhs->firstWhere('ma_nguyen_lieu', $congThuc->ma_nguyen_lieu))->ti_le ?? 100;
                    $ingredients->push([
                        'ten' => $congThuc->nguyenLieu?->ten_nguyen_lieu,
                        'so_luong' => (float) $congThuc->so_luong * $chiTiet->so_luong * ((int) $tiLe / 100),
                        'don_vi' => $congThuc->nguyenLieu?->don_vi_tinh,
                    ]);
                }

                foreach ($chiTiet->toppings as $topping) {
                    foreach ($topping->mon->congThucs as $congThuc) {
                        $ingredients->push([
                            'ten' => $congThuc->nguyenLieu?->ten_nguyen_lieu,
                            'so_luong' => (float) $congThuc->so_luong * $topping->so_luong,
                            'don_vi' => $congThuc->nguyenLieu?->don_vi_tinh,
                        ]);
                    }
                }

                $chiTiet->setAttribute('cong_thuc_thuc_te', $ingredients
                    ->groupBy(fn ($item) => $item['ten'].'|'.$item['don_vi'])
                    ->map(fn ($rows) => [
                        'ten' => $rows->first()['ten'],
                        'so_luong' => $rows->sum('so_luong'),
                        'don_vi' => $rows->first()['don_vi'],
                    ])->values());

                return $chiTiet;
            });

            return $hoaDon;
        });
    }

    public function capNhatTrangThai(ChiTietHoaDon $chiTiet, string $trangThai): ChiTietHoaDon
    {
        return $this->repository->capNhatTrangThai($chiTiet, $trangThai);
    }
}
