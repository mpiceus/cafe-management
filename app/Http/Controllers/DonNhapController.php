<?php

namespace App\Http\Controllers;

use App\Http\Requests\DonNhap\StoreDonNhapRequest;
use App\Models\DonNhap;
use App\Services\DonNhapService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DonNhapController extends Controller
{
    public function __construct(private readonly DonNhapService $service) {}

    public function index(Request $request): View
    {
        return view('don-nhap.index', [
            'donNhaps' => $this->service->danhSach($request->only('tu_khoa')),
            'filters' => $request->only('tu_khoa'),
        ]);
    }

    public function create(): View
    {
        return view('don-nhap.create', [
            'nhaCungCaps' => $this->service->nhaCungCaps(),
            'nguyenLieus' => $this->service->nguyenLieus(),
        ]);
    }

    public function show(DonNhap $donNhap): View
    {
        return view('don-nhap.show', [
            'donNhap' => $this->service->chiTiet($donNhap->ma_don_nhap),
        ]);
    }

    public function store(StoreDonNhapRequest $request): RedirectResponse
    {
        $donNhap = $this->service->taoDonNhap($request->validated(), auth()->id());

        return redirect()->route('don-nhap.index')->with('success', "Đã tạo đơn nhập #{$donNhap->ma_don_nhap}.");
    }
}
