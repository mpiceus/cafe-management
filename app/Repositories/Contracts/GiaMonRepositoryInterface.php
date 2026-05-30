<?php

namespace App\Repositories\Contracts;

use App\Models\GiaMon;
use App\Models\Mon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface GiaMonRepositoryInterface
{
    public function monCoGiaMoiNhat(array $filters = []): LengthAwarePaginator;

    public function lichSuTheoMon(Mon $mon): Collection;

    public function create(array $data): GiaMon;
}
