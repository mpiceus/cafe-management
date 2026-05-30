<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface BaoCaoRepositoryInterface
{
    public function tongQuan(): array;
    public function monBanChay(): Collection;
    public function nguyenLieuSapHet(): Collection;
    public function nguyenLieuTonNhieu(): Collection;
}
