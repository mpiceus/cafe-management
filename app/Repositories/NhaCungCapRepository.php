<?php

namespace App\Repositories;

use App\Models\NhaCungCap;
use App\Repositories\Contracts\NhaCungCapRepositoryInterface;
use App\Support\TextNormalizer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as PaginatorInstance;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class NhaCungCapRepository implements NhaCungCapRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $items = NhaCungCap::query()
            ->withCount(['nguyenLieus', 'donNhaps'])
            ->orderBy('ten_nha_cung_cap')
            ->get();

        if (! empty($filters['tu_khoa'])) {
            $items = $items->filter(function (NhaCungCap $nhaCungCap) use ($filters) {
                return TextNormalizer::contains($nhaCungCap->ten_nha_cung_cap, $filters['tu_khoa'])
                    || TextNormalizer::contains($nhaCungCap->so_dien_thoai, $filters['tu_khoa'])
                    || TextNormalizer::contains($nhaCungCap->email, $filters['tu_khoa'])
                    || TextNormalizer::contains($nhaCungCap->dia_chi, $filters['tu_khoa']);
            });
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

    public function create(array $data): NhaCungCap
    {
        return NhaCungCap::query()->create($data);
    }

    public function update(NhaCungCap $nhaCungCap, array $data): NhaCungCap
    {
        $nhaCungCap->fill($data)->save();

        return $nhaCungCap->refresh();
    }

    public function delete(NhaCungCap $nhaCungCap): void
    {
        $nhaCungCap->delete();
    }

    public function allForSearch(): Collection
    {
        return NhaCungCap::query()->get();
    }
}
