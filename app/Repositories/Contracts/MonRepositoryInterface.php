<?php

namespace App\Repositories\Contracts;

use App\Models\Mon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface MonRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    public function find(int $id): ?Mon;

    public function create(array $data): Mon;

    public function update(Mon $mon, array $data): Mon;

    public function loaiMons(): Collection;

    public function allForSearch(): Collection;
}
