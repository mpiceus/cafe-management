<?php

namespace App\Http\Controllers;

use App\Http\Requests\NguyenLieu\StoreNguyenLieuRequest;
use App\Http\Requests\NguyenLieu\UpdateNguyenLieuRequest;
use App\Models\NguyenLieu;
use App\Services\NguyenLieuService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NguyenLieuController extends Controller
{
    public function __construct(private readonly NguyenLieuService $service) {}

    public function index(Request $request): View
    {
        return view('nguyen-lieu.index', [
            'nguyenLieus' => $this->service->danhSach($request->only(['tu_khoa', 'sap_het'])),
            'filters' => $request->only(['tu_khoa', 'sap_het']),
        ]);
    }

    public function create(): View
    {
        return view('nguyen-lieu.create', [
            'nguyenLieu' => new NguyenLieu(),
            'nhaCungCaps' => $this->service->nhaCungCaps(),
        ]);
    }

    public function store(StoreNguyenLieuRequest $request): RedirectResponse
    {
        $this->service->taoMoi($request->validated());

        return redirect()->route('nguyen-lieu.index')->with('success', 'Đã thêm nguyên liệu.');
    }

    public function edit(NguyenLieu $nguyenLieu): View
    {
        return view('nguyen-lieu.edit', [
            'nguyenLieu' => $nguyenLieu,
            'nhaCungCaps' => $this->service->nhaCungCaps(),
        ]);
    }

    public function update(UpdateNguyenLieuRequest $request, NguyenLieu $nguyenLieu): RedirectResponse
    {
        $this->service->capNhat($nguyenLieu, $request->validated());

        return redirect()->route('nguyen-lieu.index')->with('success', 'Đã cập nhật nguyên liệu.');
    }
}
