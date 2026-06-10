<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order\StoreOrderRequest;
use App\Models\HoaDon;
use App\Services\OrderService;
use App\Services\SePayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $service,
        private readonly SePayService $sepayService
    ) {}

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
        $menuData = $mons->map(function ($mon) {
            return [
                'id' => $mon->ma_mon,
                'name' => $mon->ten_mon,
                'image' => $mon->hinh_anh ? asset('storage/'.$mon->hinh_anh) : null,
                'price' => (float) ($mon->giaMoiNhat?->gia ?? 0),
                'category_id' => $mon->ma_loai_mon,
                'category_name' => $mon->loaiMon?->ten_loai_mon,
                'service_mode' => $mon->che_do_phuc_vu,
                'allow_topping' => false,
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

        return view('order.create', [
            'menuData' => $menuData,
            'loaiMons' => $mons->pluck('loaiMon')->filter()->unique('ma_loai_mon')->values(),
        ]);
    }

    public function store(StoreOrderRequest $request): RedirectResponse|JsonResponse|Response
    {
        $hoaDon = $this->service->thanhToan($request->validated(), auth()->id());

        if ($hoaDon->phuong_thuc_thanh_toan === 'chuyen_khoan') {
            $paymentUrl = route('payment.checkout', $hoaDon);
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => "Đã tạo hóa đơn #{$hoaDon->ma_hoa_don}. Vui lòng thanh toán chuyển khoản.",
                    'ma_hoa_don' => $hoaDon->ma_hoa_don,
                    'tong_tien' => $hoaDon->tong_tien,
                    'payment_url' => $paymentUrl,
                    'payment_code' => $this->sepayService->paymentCode($hoaDon),
                ]);
            }

            return redirect()->route('payment.checkout', $hoaDon);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => "Đã thanh toán hóa đơn #{$hoaDon->ma_hoa_don}.",
                'ma_hoa_don' => $hoaDon->ma_hoa_don,
                'tong_tien' => $hoaDon->tong_tien,
                'invoice_url' => route('order.invoice', $hoaDon),
            ]);
        }

        // For immediate (tiền mặt) payments, directly show the invoice PDF for printing
        return $this->invoice($hoaDon);
    }

    public function invoice(HoaDon $hoaDon): Response
    {
        $hoaDon->load(['chiTiets.mon.giaMoiNhat', 'chiTiets.toppings.mon.giaMoiNhat', 'chiTiets.tuyChinhs.nguyenLieu', 'nguoiDung']);

        $pdf = Pdf::loadView('order.invoice', [
            'hoaDon' => $hoaDon,
        ]);

        return $pdf->stream("hoa-don-{$hoaDon->ma_hoa_don}.pdf");
    }
}
