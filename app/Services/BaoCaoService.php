<?php

namespace App\Services;

use App\Repositories\Contracts\BaoCaoRepositoryInterface;

class BaoCaoService
{
    public function __construct(private readonly BaoCaoRepositoryInterface $repository) {}

    public function duLieu(): array
    {
        return [
            'tongQuan' => $this->repository->tongQuan(),
            'monBanChay' => $this->repository->monBanChay(),
            'nguyenLieuSapHet' => $this->repository->nguyenLieuSapHet(),
            'nguyenLieuTonNhieu' => $this->repository->nguyenLieuTonNhieu(),
        ];
    }
}
