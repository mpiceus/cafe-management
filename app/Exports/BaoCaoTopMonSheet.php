<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class BaoCaoTopMonSheet implements FromArray, WithHeadings, WithTitle
{
    public function __construct(private readonly Collection $rows) {}

    public function array(): array
    {
        return $this->rows
            ->map(fn ($row) => [
                $row->mon?->ten_mon,
                (int) $row->tong_so_luong,
            ])
            ->toArray();
    }

    public function headings(): array
    {
        return ['Món', 'Số lượng'];
    }

    public function title(): string
    {
        return 'Top Mon';
    }
}
