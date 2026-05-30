<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class BaoCaoDoanhThuSheet implements FromArray, WithHeadings, WithTitle
{
    public function __construct(
        private readonly Collection $rows,
        private readonly string $title,
        private readonly string $label
    ) {}

    public function array(): array
    {
        return $this->rows
            ->map(fn ($row) => [
                $row->{$this->label},
                (float) $row->doanh_thu,
            ])
            ->toArray();
    }

    public function headings(): array
    {
        return [$this->label === 'ngay' ? 'Ngày' : 'Tháng', 'Doanh thu'];
    }

    public function title(): string
    {
        return $this->title;
    }
}
