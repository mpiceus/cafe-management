<?php

namespace App\Repositories\Contracts;

use App\Models\NhaCungCap;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface NhaCungCapRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    public function create(array $data): NhaCungCap;

    public function update(NhaCungCap $nhaCungCap, array $data): NhaCungCap;

    public function delete(NhaCungCap $nhaCungCap): void;

    public function allForSearch(): Collection;
}
