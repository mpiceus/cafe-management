<?php

namespace App\Services;

use App\Repositories\Contracts\BaoCaoRepositoryInterface;
use Illuminate\Support\Collection;

class BaoCaoService
{
    public function __construct(private readonly BaoCaoRepositoryInterface $repository) {}

    public function duLieu(array $filters = []): array
    {
        $tuNgay = $filters['tu_ngay'] ?? null;
        $denNgay = $filters['den_ngay'] ?? null;
        $tongQuan = $this->repository->tongQuan($tuNgay, $denNgay);
        $monBanChay = $this->repository->monBanChay($tuNgay, $denNgay);

        $aiTuNgay = $tuNgay ?: now()->subDays(7)->toDateString();
        $aiDenNgay = $denNgay ?: now()->toDateString();
        $monBanChayAI = $this->repository->monBanChay($aiTuNgay, $aiDenNgay);

        return [
            'tongQuan' => $tongQuan,
            'monBanChay' => $monBanChay,
            'nguyenLieuSapHet' => $this->repository->nguyenLieuSapHet(),
            'nguyenLieuTonNhieu' => $this->repository->nguyenLieuTonNhieu(),
            'doanhThuTheoNgay' => $this->repository->doanhThuTheoNgay($tuNgay, $denNgay),
            'doanhThuTheoThang' => $this->repository->doanhThuTheoThang($tuNgay, $denNgay),
            'filters' => ['tu_ngay' => $tuNgay, 'den_ngay' => $denNgay],
            'goiYNhapKho' => $this->goiYNhapKho($monBanChayAI),
            'goiYKhoRange' => ['tu_ngay' => $aiTuNgay, 'den_ngay' => $aiDenNgay],
        ];
    }

    private function goiYNhapKho(Collection $monBanChay): Collection
    {
        $usage = collect();

        foreach ($monBanChay as $item) {
            $mon = $item->mon;
            if (! $mon) {
                continue;
            }

            foreach ($mon->congThucs as $congThuc) {
                $nguyenLieu = $congThuc->nguyenLieu;
                if (! $nguyenLieu) {
                    continue;
                }

                $key = $nguyenLieu->ma_nguyen_lieu;
                $usage[$key] = ($usage[$key] ?? 0) + ((float) $congThuc->so_luong * (float) $item->tong_so_luong);
            }
        }

        return collect($usage)
            ->map(function ($soLuong, $maNguyenLieu) use ($monBanChay) {
                $nguyenLieu = $monBanChay
                    ->flatMap(fn ($item) => $item->mon?->congThucs ?? [])
                    ->firstWhere('ma_nguyen_lieu', (int) $maNguyenLieu)?->nguyenLieu;

                if (! $nguyenLieu) {
                    return null;
                }

                $tonKho = (float) $nguyenLieu->ton_kho;
                $deXuat = max(0, (float) $soLuong - $tonKho);

                if ($deXuat <= 0) {
                    return null;
                }

                return [
                    'ten' => $nguyenLieu->ten_nguyen_lieu,
                    'ton_kho' => $tonKho,
                    'de_xuat' => $deXuat,
                    'don_vi' => $nguyenLieu->don_vi_tinh,
                ];
            })
            ->filter()
            ->values();
    }
}
