<?php

namespace App\Services;

use App\Models\Mon;
use App\Repositories\Contracts\CongThucRepositoryInterface;
use Illuminate\Support\Collection;

class CongThucService
{
    public function __construct(private readonly CongThucRepositoryInterface $repository) {}

    public function mons(array $filters = []): Collection { return $this->repository->mons($filters); }
    public function nguyenLieus(): Collection { return $this->repository->nguyenLieus(); }
    public function congThuc(Mon $mon): Collection { return $this->repository->congThuc($mon); }

    public function capNhat(Mon $mon, array $data): void
    {
        $this->repository->replace($mon, $data['items'] ?? []);
    }
}
