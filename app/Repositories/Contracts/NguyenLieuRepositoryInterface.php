<?php

namespace App\Repositories\Contracts;

use App\Models\NguyenLieu;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface NguyenLieuRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator;
    public function nhaCungCaps(): Collection;
    public function all(): Collection;
    public function create(array $data): NguyenLieu;
    public function update(NguyenLieu $nguyenLieu, array $data): NguyenLieu;
    public function delete(NguyenLieu $nguyenLieu): void;
    public function allForSearch(): Collection;
}
