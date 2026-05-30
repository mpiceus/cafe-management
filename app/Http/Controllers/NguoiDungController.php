<?php

namespace App\Http\Controllers;

use App\Http\Requests\NguoiDung\StoreNguoiDungRequest;
use App\Http\Requests\NguoiDung\UpdateNguoiDungRequest;
use App\Models\NguoiDung;
use App\Services\NguoiDungService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NguoiDungController extends Controller
{
    public function __construct(
        private readonly NguoiDungService $nguoiDungService
    ) {
    }

    public function index(): View
    {
        return view('nguoi-dung.index', [
            'nguoiDungs' => $this->nguoiDungService->danhSach(),
        ]);
    }

    public function create(): View
    {
        return view('nguoi-dung.create', [
            'nguoiDung' => new NguoiDung(),
        ]);
    }

    public function store(StoreNguoiDungRequest $request): RedirectResponse
    {
        $this->nguoiDungService->taoMoi($request->validated());

        return redirect()
            ->route('nguoi-dung.index')
            ->with('success', 'Đã tạo người dùng mới.');
    }

    public function edit(NguoiDung $nguoiDung): View
    {
        return view('nguoi-dung.edit', [
            'nguoiDung' => $nguoiDung,
        ]);
    }

    public function update(UpdateNguoiDungRequest $request, NguoiDung $nguoiDung): RedirectResponse
    {
        $this->nguoiDungService->capNhat($nguoiDung, $request->validated());

        return redirect()
            ->route('nguoi-dung.index')
            ->with('success', 'Đã cập nhật người dùng.');
    }
}
