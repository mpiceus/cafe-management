<?php

namespace App\Repositories;

use App\Models\NguyenLieu;
use App\Models\NhaCungCap;
use App\Repositories\Contracts\NguyenLieuRepositoryInterface;
use App\Support\TextNormalizer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as PaginatorInstance;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class NguyenLieuRepository implements NguyenLieuRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $items = NguyenLieu::query()
            ->with('nhaCungCap')
            ->orderBy('ten_nguyen_lieu')
            ->get();

        if (! empty($filters['tu_khoa'])) {
            $items = $items->filter(fn (NguyenLieu $item) => TextNormalizer::contains($item->ten_nguyen_lieu, $filters['tu_khoa']));
        }

        if (($filters['sap_het'] ?? null) === '1') {
            $items = $items->filter(fn (NguyenLieu $item) => (float) $item->ton_kho <= (float) $item->so_luong_toi_thieu);
        }

        $page = Paginator::resolveCurrentPage() ?: 1;
        $results = $items->slice(($page - 1) * $perPage, $perPage)->values();

        return new PaginatorInstance(
            $results,
            $items->count(),
            $perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath(), 'query' => request()->query()]
        );
    }

    public function nhaCungCaps(): Collection
    {
        return NhaCungCap::query()->orderBy('ten_nha_cung_cap')->get();
    }

    public function all(): Collection
    {
        return NguyenLieu::query()->with('nhaCungCap')->orderBy('ten_nguyen_lieu')->get();
    }

    public function create(array $data): NguyenLieu
    {
        return NguyenLieu::query()->create($data);
    }

    public function update(NguyenLieu $nguyenLieu, array $data): NguyenLieu
    {
        $nguyenLieu->fill($data)->save();

        return $nguyenLieu->refresh();
    }

    public function allForSearch(): Collection
    {
        return NguyenLieu::query()->with('nhaCungCap')->get();
    }
}
