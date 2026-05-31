<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface BaoCaoRepositoryInterface
{
    public function tongQuan(?string $tuNgay = null, ?string $denNgay = null): array;
    public function monBanChay(?string $tuNgay = null, ?string $denNgay = null): Collection;
    public function nguyenLieuSapHet(): Collection;
    public function nguyenLieuTonNhieu(): Collection;
    public function doanhThuTheoNgay(?string $tuNgay = null, ?string $denNgay = null): Collection;
    public function doanhThuTheoThang(?string $tuNgay = null, ?string $denNgay = null): Collection;
}
