<?php

namespace App\Repositories\Contracts;

use App\Models\HoaDon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface OrderRepositoryInterface
{
    public function hoaDons(array $filters = []): LengthAwarePaginator;
    public function monsDangBan(): Collection;
    public function toppingsDangBan(): Collection;
    public function nguyenLieuTuyChinh(): Collection;
    public function taoHoaDon(array $items, int $maNguoiDung, string $phuongThuc): HoaDon;
}
