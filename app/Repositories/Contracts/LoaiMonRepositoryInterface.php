<?php

namespace App\Repositories\Contracts;

use App\Models\LoaiMon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface LoaiMonRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    public function create(array $data): LoaiMon;

    public function update(LoaiMon $loaiMon, array $data): LoaiMon;

    public function allForSearch(): Collection;
}
