<?php

namespace App\Repositories\Contracts;

use App\Models\Mon;
use Illuminate\Support\Collection;

interface CongThucRepositoryInterface
{
    public function mons(array $filters = []): Collection;
    public function nguyenLieus(): Collection;
    public function congThuc(Mon $mon): Collection;
    public function replace(Mon $mon, array $items): void;
}
