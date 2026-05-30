<?php

namespace App\Repositories\Contracts;

use App\Models\ChiTietHoaDon;
use Illuminate\Support\Collection;

interface PhaCheRepositoryInterface
{
    public function danhSachCho(): Collection;
    public function donChoPhaChe(): Collection;
    public function capNhatTrangThai(ChiTietHoaDon $chiTiet, string $trangThai): ChiTietHoaDon;
}
