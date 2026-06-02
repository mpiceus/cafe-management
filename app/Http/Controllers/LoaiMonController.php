<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoaiMon\StoreLoaiMonRequest;
use App\Http\Requests\LoaiMon\UpdateLoaiMonRequest;
use App\Models\LoaiMon;
use App\Services\LoaiMonService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoaiMonController extends Controller
{
    public function __construct(private readonly LoaiMonService $service) {}

    public function index(Request $request): View
    {
        $filters = $request->only('tu_khoa');

        return view('loai-mon.index', [
            'loaiMons' => $this->service->danhSach($filters),
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return view('loai-mon.create', [
            'loaiMon' => new LoaiMon(),
        ]);
    }

    public function store(StoreLoaiMonRequest $request): RedirectResponse
    {
        $this->service->taoMoi($request->validated());

        return redirect()->route('loai-mon.index')->with('success', 'Đã thêm loại món.');
    }

    public function edit(LoaiMon $loaiMon): View
    {
        return view('loai-mon.edit', [
            'loaiMon' => $loaiMon,
        ]);
    }

    public function update(UpdateLoaiMonRequest $request, LoaiMon $loaiMon): RedirectResponse
    {
        $this->service->capNhat($loaiMon, $request->validated());

        return redirect()->route('loai-mon.index')->with('success', 'Đã cập nhật loại món.');
    }

    public function destroy(LoaiMon $loaiMon): RedirectResponse
    {
        try {
            $this->service->xoa($loaiMon);
        } catch (ValidationException $exception) {
            return back()->with('error', $exception->errors()['loai_mon'][0] ?? $exception->getMessage());
        }

        return redirect()->route('loai-mon.index')->with('success', 'Đã xóa loại món.');
    }
}
