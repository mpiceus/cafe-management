<?php

namespace App\Repositories\Contracts;

use App\Models\DonNhap;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface DonNhapRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator;
    public function findById(int $maDonNhap): ?DonNhap;
    public function nhaCungCaps(): Collection;
    public function nguyenLieus(): Collection;
    public function createWithDetails(array $data, array $items): DonNhap;
}
