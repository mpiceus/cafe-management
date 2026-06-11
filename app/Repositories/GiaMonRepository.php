<?php

namespace App\Repositories;

use App\Models\GiaMon;
use App\Models\Mon;
use App\Repositories\Contracts\GiaMonRepositoryInterface;
use App\Support\TextNormalizer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as PaginatorInstance;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class GiaMonRepository implements GiaMonRepositoryInterface
{
    public function monCoGiaMoiNhat(array $filters = []): LengthAwarePaginator
    {
        $items = Mon::query()
            ->with(['loaiMon', 'giaMons'])
            ->orderBy('ten_mon')
            ->get();

        if (! empty($filters['tu_khoa'])) {
            $items = $items->filter(fn (Mon $mon) => TextNormalizer::contains($mon->ten_mon, $filters['tu_khoa']));
        }

        if (! empty($filters['ma_loai_mon'])) {
            $items = $items->where('ma_loai_mon', (int) $filters['ma_loai_mon']);
        }

        if (($filters['trang_thai_gia'] ?? null) === 'co_gia') {
            $items = $items->filter(fn (Mon $mon) => $mon->giaMons->isNotEmpty());
        }

        if (($filters['trang_thai_gia'] ?? null) === 'chua_co_gia') {
            $items = $items->filter(fn (Mon $mon) => $mon->giaMons->isEmpty());
        }

        $page = Paginator::resolveCurrentPage() ?: 1;
        $perPage = 12;
        $results = $items->slice(($page - 1) * $perPage, $perPage)->values();

        return new PaginatorInstance(
            $results,
            $items->count(),
            $perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath(), 'query' => request()->query()]
        );
    }

    public function lichSuTheoMon(Mon $mon): Collection
    {
        return GiaMon::query()
            ->where('ma_mon', $mon->ma_mon)
            ->orderBy('size')
            ->orderByDesc('ngay_ap_dung')
            ->get();
    }

    public function create(array $data): GiaMon
    {
        GiaMon::query()
            ->where('ma_mon', $data['ma_mon'])
            ->where('size', $data['size'])
            ->exists()
            ? GiaMon::query()
                ->where('ma_mon', $data['ma_mon'])
                ->where('size', $data['size'])
                ->update([
                    'gia' => $data['gia'],
                    'ngay_ap_dung' => $data['ngay_ap_dung'],
                ])
            : GiaMon::query()->insert([
                'ma_mon' => $data['ma_mon'],
                'size' => $data['size'],
                'gia' => $data['gia'],
                'ngay_ap_dung' => $data['ngay_ap_dung'],
            ]);

        return GiaMon::query()
            ->where('ma_mon', $data['ma_mon'])
            ->where('size', $data['size'])
            ->firstOrFail();
    }
}
