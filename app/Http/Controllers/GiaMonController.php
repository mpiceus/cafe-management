<?php

namespace App\Http\Controllers;

use App\Http\Requests\GiaMon\StoreGiaMonRequest;
use App\Models\Mon;
use App\Services\GiaMonService;
use App\Services\MonService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GiaMonController extends Controller
{
    public function __construct(
        private readonly GiaMonService $giaMonService,
        private readonly MonService $monService
    ) {
    }

    public function index(Request $request): View
    {
        $filters = $request->only(['tu_khoa', 'ma_loai_mon', 'trang_thai_gia']);

        return view('gia-mon.index', [
            'mons' => $this->giaMonService->danhSachMon($filters),
            'loaiMons' => $this->monService->loaiMons(),
            'filters' => $filters,
        ]);
    }

    public function create(Mon $mon): View
    {
        return view('gia-mon.create', [
            'mon' => $mon->load(['loaiMon', 'giaMoiNhat']),
            'lichSuGia' => $this->giaMonService->lichSuTheoMon($mon),
        ]);
    }

    public function store(StoreGiaMonRequest $request, Mon $mon): RedirectResponse
    {
        $this->giaMonService->apDungGiaMoi($mon, $request->validated());

        return redirect()
            ->route('gia-mon.index')
            ->with('success', 'Đã áp dụng giá bán mới.');
    }
}
