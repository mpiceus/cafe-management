<?php

namespace App\Repositories;

use App\Models\CongThuc;
use App\Models\Mon;
use App\Models\NguyenLieu;
use App\Repositories\Contracts\CongThucRepositoryInterface;
use App\Support\TextNormalizer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CongThucRepository implements CongThucRepositoryInterface
{
    public function mons(array $filters = []): Collection
    {
        $items = Mon::query()
            ->with(['loaiMon', 'congThucs.nguyenLieu'])
            ->orderBy('ten_mon')
            ->get();

        if (! empty($filters['tu_khoa'])) {
            $items = $items->filter(function (Mon $mon) use ($filters) {
                if (TextNormalizer::contains($mon->ten_mon, $filters['tu_khoa'])) {
                    return true;
                }

                return $mon->congThucs->contains(fn ($item) => TextNormalizer::contains($item->nguyenLieu?->ten_nguyen_lieu, $filters['tu_khoa']));
            });
        }

        if (! empty($filters['ma_loai_mon'])) {
            $items = $items->where('ma_loai_mon', (int) $filters['ma_loai_mon']);
        }

        return $items->values();
    }

    public function nguyenLieus(): Collection
    {
        return NguyenLieu::query()
            ->where('duoc_su_dung', true)
            ->orderBy('ten_nguyen_lieu')
            ->get();
    }

    public function congThuc(Mon $mon): Collection
    {
        return CongThuc::query()->with('nguyenLieu')->where('ma_mon', $mon->ma_mon)->get();
    }

    public function replace(Mon $mon, array $items): void
    {
        DB::transaction(function () use ($mon, $items) {
            CongThuc::query()->where('ma_mon', $mon->ma_mon)->delete();

            foreach ($items as $item) {
                if (! empty($item['ma_nguyen_lieu']) && (float) ($item['so_luong'] ?? 0) > 0) {
                    CongThuc::query()->create([
                        'ma_mon' => $mon->ma_mon,
                        'ma_nguyen_lieu' => $item['ma_nguyen_lieu'],
                        'so_luong' => $item['so_luong'],
                    ]);
                }
            }
        });
    }
}
