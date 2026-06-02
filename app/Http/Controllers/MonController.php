<?php

namespace App\Http\Controllers;

use App\Http\Requests\Mon\StoreMonRequest;
use App\Http\Requests\Mon\UpdateMonRequest;
use App\Models\Mon;
use App\Services\MonService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MonController extends Controller
{
    public function __construct(
        private readonly MonService $monService
    ) {
    }

    public function index(Request $request): View
    {
        $filters = $request->only(['tu_khoa', 'ma_loai_mon', 'trang_thai', 'hien_tat_ca']);

        if (($filters['hien_tat_ca'] ?? null) !== '1' && empty($filters['trang_thai'])) {
            $filters['trang_thai'] = Mon::TRANG_THAI_DANG_BAN;
        }

        return view('mon.index', [
            'mons' => $this->monService->danhSach($filters),
            'loaiMons' => $this->monService->loaiMons(),
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return view('mon.create', [
            'mon' => new Mon(),
            'loaiMons' => $this->monService->loaiMons(),
        ]);
    }

    public function store(StoreMonRequest $request): RedirectResponse
    {
        $this->monService->taoMoi($request->validated());

        return redirect()
            ->route('mon.index')
            ->with('success', 'Đã thêm món mới.');
    }

    public function edit(Mon $mon): View
    {
        return view('mon.edit', [
            'mon' => $mon->load('giaMoiNhat'),
            'loaiMons' => $this->monService->loaiMons(),
        ]);
    }

    public function update(UpdateMonRequest $request, Mon $mon): RedirectResponse
    {
        /*
        Request → UpdateMonRequest
        → authorize()
        → rules()
        → validate OK
        → update() controller → service
        */
        $this->monService->capNhat($mon, $request->validated());

        return redirect()
            ->route('mon.index')
            ->with('success', 'Đã cập nhật món.');
    }

    public function toggleStatus(Mon $mon): RedirectResponse
    {
        $this->monService->doiTrangThai($mon);

        return redirect()
            ->route('mon.index')
            ->with('success', 'Đã cập nhật trạng thái món.');
    }

    public function destroy(Mon $mon): RedirectResponse
    {
        try {
            $this->monService->xoa($mon);
        } catch (ValidationException $exception) {
            return back()->with('error', $exception->errors()['mon'][0] ?? $exception->getMessage());
        }

        return redirect()->route('mon.index')->with('success', 'Đã xóa món.');
    }
}
