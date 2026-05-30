<?php

namespace App\Http\Controllers;

use App\Models\ChiTietHoaDon;
use App\Services\PhaCheService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PhaCheController extends Controller
{
    public function __construct(private readonly PhaCheService $service) {}

    public function index(): View
    {
        return view('pha-che.index');
    }

    public function data(): JsonResponse
    {
        return response()->json([
            'orders' => $this->service->donChoPhaChe(),
        ]);
    }

    public function update(Request $request, ChiTietHoaDon $chiTiet): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'trang_thai_pha_che' => ['required', 'in:cho_pha_che,dang_pha_che,da_hoan_thanh'],
        ]);

        $this->service->capNhatTrangThai($chiTiet, $data['trang_thai_pha_che']);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Đã cập nhật trạng thái pha chế.']);
        }

        return redirect()->route('pha-che.index')->with('success', 'Đã cập nhật trạng thái pha chế.');
    }
}
