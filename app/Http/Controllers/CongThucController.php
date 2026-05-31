<?php

namespace App\Http\Controllers;

use App\Http\Requests\CongThuc\UpdateCongThucRequest;
use App\Models\Mon;
use App\Services\CongThucService;
use App\Services\MonService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CongThucController extends Controller
{
    public function __construct(
        private readonly CongThucService $service,
        private readonly MonService $monService
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['tu_khoa', 'ma_loai_mon']);

        return view('cong-thuc.index', [
            'mons' => $this->service->mons($filters),
            'loaiMons' => $this->monService->loaiMons(),
            'filters' => $filters,
        ]);
    }

    public function show(Mon $mon): View
    {
        return view('cong-thuc.show', [
            'mon' => $mon->load('loaiMon'),
            'congThucs' => $this->service->congThuc($mon),
        ]);
    }

    public function edit(Mon $mon): View
    {
        return view('cong-thuc.edit', [
            'mon' => $mon->load('loaiMon'),
            'nguyenLieus' => $this->service->nguyenLieus(),
            'congThucs' => $this->service->congThuc($mon),
        ]);
    }

    public function update(UpdateCongThucRequest $request, Mon $mon): RedirectResponse
    {
        $this->service->capNhat($mon, $request->validated());

        return redirect()->route('cong-thuc.index')->with('success', 'Đã cập nhật công thức.');
    }
}
