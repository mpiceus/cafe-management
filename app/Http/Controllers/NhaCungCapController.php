<?php

namespace App\Http\Controllers;

use App\Http\Requests\NhaCungCap\StoreNhaCungCapRequest;
use App\Http\Requests\NhaCungCap\UpdateNhaCungCapRequest;
use App\Models\NhaCungCap;
use App\Services\NhaCungCapService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class NhaCungCapController extends Controller
{
    public function __construct(private readonly NhaCungCapService $service) {}

    public function index(Request $request): View
    {
        $filters = $request->only('tu_khoa');

        return view('nha-cung-cap.index', [
            'nhaCungCaps' => $this->service->danhSach($filters),
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return view('nha-cung-cap.create', [
            'nhaCungCap' => new NhaCungCap(),
        ]);
    }

    public function store(StoreNhaCungCapRequest $request): RedirectResponse
    {
        $this->service->taoMoi($request->validated());

        return redirect()->route('nha-cung-cap.index')->with('success', 'Đã thêm nhà cung cấp.');
    }

    public function edit(NhaCungCap $nhaCungCap): View
    {
        return view('nha-cung-cap.edit', [
            'nhaCungCap' => $nhaCungCap,
        ]);
    }

    public function update(UpdateNhaCungCapRequest $request, NhaCungCap $nhaCungCap): RedirectResponse
    {
        $this->service->capNhat($nhaCungCap, $request->validated());

        return redirect()->route('nha-cung-cap.index')->with('success', 'Đã cập nhật nhà cung cấp.');
    }

    public function destroy(NhaCungCap $nhaCungCap): RedirectResponse
    {
        try {
            $this->service->xoa($nhaCungCap);
        } catch (ValidationException $exception) {
            return back()->with('error', $exception->errors()['nha_cung_cap'][0] ?? $exception->getMessage());
        }

        return redirect()->route('nha-cung-cap.index')->with('success', 'Đã xóa nhà cung cấp.');
    }
}
