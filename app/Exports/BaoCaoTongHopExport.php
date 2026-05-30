<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class BaoCaoTongHopExport implements WithMultipleSheets
{
    public function __construct(
        private readonly Collection $doanhThuNgay,
        private readonly Collection $doanhThuThang,
        private readonly Collection $topMon
    ) {}

    public function sheets(): array
    {
        return [
            new BaoCaoDoanhThuSheet($this->doanhThuNgay, 'Doanh Thu Ngay', 'ngay'),
            new BaoCaoDoanhThuSheet($this->doanhThuThang, 'Doanh Thu Thang', 'thang'),
            new BaoCaoTopMonSheet($this->topMon),
        ];
    }
}
