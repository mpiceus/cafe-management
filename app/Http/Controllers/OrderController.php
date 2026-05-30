<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order\StoreOrderRequest;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $service) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['ma_hoa_don', 'tu_khoa', 'tu_ngay', 'den_ngay']);

        return view('order.index', [
            'hoaDons' => $this->service->danhSachHoaDon($filters),
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        $mons = $this->service->monsDangBan()->values();
        $toppings = $this->service->toppingsDangBan()->values();

        $menuData = $mons->map(function ($mon) {
            return [
                'id' => $mon->ma_mon,
                'name' => $mon->ten_mon,
                'image' => $mon->hinh_anh ? asset('storage/'.$mon->hinh_anh) : null,
                'price' => (float) ($mon->giaMoiNhat?->gia ?? 0),
                'category_id' => $mon->ma_loai_mon,
                'category_name' => $mon->loaiMon?->ten_loai_mon,
                'service_mode' => $mon->che_do_phuc_vu,
                'allow_topping' => (bool) $mon->cho_them_topping,
                'recipe' => $mon->congThucs->map(function ($row) {
                    return [
                        'ingredient_id' => $row->ma_nguyen_lieu,
                        'ingredient_name' => $row->nguyenLieu?->ten_nguyen_lieu,
                        'amount' => (float) $row->so_luong,
                        'stock' => (float) ($row->nguyenLieu?->ton_kho ?? 0),
                        'customizable' => (bool) ($row->nguyenLieu?->duoc_tuy_chinh ?? false),
                    ];
                })->values(),
            ];
        })->values();

        $toppingData = $toppings->map(function ($mon) {
            return [
                'id' => $mon->ma_mon,
                'name' => $mon->ten_mon,
                'price' => (float) ($mon->giaMoiNhat?->gia ?? 0),
                'recipe' => $mon->congThucs->map(function ($row) {
                    return [
                        'ingredient_id' => $row->ma_nguyen_lieu,
                        'ingredient_name' => $row->nguyenLieu?->ten_nguyen_lieu,
                        'amount' => (float) $row->so_luong,
                        'stock' => (float) ($row->nguyenLieu?->ton_kho ?? 0),
                    ];
                })->values(),
            ];
        })->values();

        return view('order.create', [
            'menuData' => $menuData,
            'toppingData' => $toppingData,
            'loaiMons' => $mons->pluck('loaiMon')->filter()->unique('ma_loai_mon')->values(),
        ]);
    }

    public function store(StoreOrderRequest $request): RedirectResponse|JsonResponse
    {
        $hoaDon = $this->service->thanhToan($request->validated(), auth()->id());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => "Đã thanh toán hóa đơn #{$hoaDon->ma_hoa_don}.",
                'ma_hoa_don' => $hoaDon->ma_hoa_don,
                'tong_tien' => $hoaDon->tong_tien,
            ]);
        }

        return redirect()->route('order.index')->with('success', "Đã thanh toán hóa đơn #{$hoaDon->ma_hoa_don}.");
    }
}
