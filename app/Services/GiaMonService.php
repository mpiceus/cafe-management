<?php

namespace App\Services;

use App\Models\GiaMon;
use App\Models\Mon;
use App\Repositories\Contracts\GiaMonRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class GiaMonService
{
    public function __construct(
        private readonly GiaMonRepositoryInterface $giaMonRepository
    ) {
    }

    public function danhSachMon(array $filters = []): LengthAwarePaginator
    {
        return $this->giaMonRepository->monCoGiaMoiNhat($filters);
    }

    public function lichSuTheoMon(Mon $mon): Collection
    {
        return $this->giaMonRepository->lichSuTheoMon($mon);
    }

    public function apDungGiaMoi(Mon $mon, array $data): GiaMon
    {
        return $this->giaMonRepository->create([
            'ma_mon' => $mon->ma_mon,
            'gia' => $data['gia'],
            'ngay_ap_dung' => $data['ngay_ap_dung'],
        ]);
    }
}
