<?php

namespace App\Repositories;

use App\Models\LoaiMon;
use App\Models\Mon;
use App\Repositories\Contracts\MonRepositoryInterface;
use App\Support\TextNormalizer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as PaginatorInstance;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class MonRepository implements MonRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $items = Mon::query()
            ->with(['loaiMon', 'giaMoiNhat', 'congThucs.nguyenLieu'])
            ->where('ma_loai_mon', '<>', LoaiMon::MA_TOPPING)
            ->when($filters['ma_loai_mon'] ?? null, fn ($query, string $maLoaiMon) => $query->where('ma_loai_mon', $maLoaiMon))
            ->when($filters['trang_thai'] ?? null, fn ($query, string $trangThai) => $query->where('trang_thai', $trangThai))
            ->orderByDesc('ma_mon')
            ->get();
        /*
        $query = Mon::query();
        $query->with(['loaiMon', 'giaMoiNhat', 'congThucs.nguyenLieu']);
        if (!empty($filters['ma_loai_mon'])) {
            $query->where('ma_loai_mon', $filters['ma_loai_mon']);
        }
        if (!empty($filters['trang_thai'])) {
            $query->where('trang_thai', $filters['trang_thai']);
        }
        $query->orderByDesc('ma_mon');
        $items = $query->get();
         */
        if (! empty($filters['tu_khoa'])) {
            $items = $items->filter(fn (Mon $mon) => TextNormalizer::contains($mon->ten_mon, $filters['tu_khoa']));
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

    public function find(int $id): ?Mon
    {
        return Mon::query()
            ->with(['loaiMon', 'giaMoiNhat', 'congThucs.nguyenLieu'])
            ->find($id);
    }

    public function create(array $data): Mon
    {
        return Mon::query()->create($data);
    }

    public function update(Mon $mon, array $data): Mon
    {
        $mon->fill($data);
        $mon->save();

        return $mon->refresh();
    }

    public function delete(Mon $mon): void
    {
        $mon->delete();
    }

    public function loaiMons(): Collection
    {
        return LoaiMon::query()
            ->where('ma_loai_mon', '<>', LoaiMon::MA_TOPPING)
            ->orderBy('ten_loai_mon')
            ->get();
    }

    public function allForSearch(): Collection
    {
        return Mon::query()->with(['congThucs.nguyenLieu', 'giaMoiNhat', 'loaiMon'])->get();
    }
}
