<?php

namespace App\Repositories;

use App\Models\LoaiMon;
use App\Repositories\Contracts\LoaiMonRepositoryInterface;
use App\Support\TextNormalizer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as PaginatorInstance;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class LoaiMonRepository implements LoaiMonRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $items = LoaiMon::query()
            ->withCount('mons')
            ->where('ma_loai_mon', '<>', LoaiMon::MA_TOPPING)
            ->orderBy('ten_loai_mon')
            ->get();

        if (! empty($filters['tu_khoa'])) {
            $items = $items->filter(function (LoaiMon $loaiMon) use ($filters) {
                return TextNormalizer::contains($loaiMon->ten_loai_mon, $filters['tu_khoa'])
                    || TextNormalizer::contains($loaiMon->mo_ta, $filters['tu_khoa']);
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

    public function create(array $data): LoaiMon
    {
        return LoaiMon::query()->create($data);
    }

    public function update(LoaiMon $loaiMon, array $data): LoaiMon
    {
        $loaiMon->fill($data)->save();

        return $loaiMon->refresh();
    }

    public function delete(LoaiMon $loaiMon): void
    {
        $loaiMon->delete();
    }

    public function allForSearch(): Collection
    {
        return LoaiMon::query()->get();
    }
}
