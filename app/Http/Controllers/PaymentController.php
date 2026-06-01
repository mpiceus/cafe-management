<?php

namespace App\Http\Controllers;

use App\Models\HoaDon;
use App\Models\SePayRefund;
use App\Services\SePayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(private readonly SePayService $sepayService) {}

    public function checkout(HoaDon $hoaDon): View|RedirectResponse
    {
        if ($hoaDon->phuong_thuc_thanh_toan !== 'chuyen_khoan') {
            return redirect()->route('order.index');
        }

        if (empty($hoaDon->ma_thanh_toan)) {
            $hoaDon->update(['ma_thanh_toan' => $this->sepayService->paymentCode($hoaDon)]);
        }

        return view('payment.checkout', [
            'hoaDon' => $hoaDon,
            'paymentCode' => $this->sepayService->paymentCode($hoaDon),
            'qrUrl' => $this->sepayService->qrUrl($hoaDon),
            'bankName' => config('sepay.bank_name'),
            'bankCode' => config('sepay.bank_code'),
            'bankAccount' => config('sepay.bank_account'),
            'accountName' => config('sepay.account_name'),
        ]);
    }

    /**
     * Customer-facing checkout page (minimal UI for QR display)
     */
    public function customerCheckout(HoaDon $hoaDon): View|RedirectResponse
    {
        if ($hoaDon->phuong_thuc_thanh_toan !== 'chuyen_khoan') {
            return redirect()->route('order.index');
        }

        if (empty($hoaDon->ma_thanh_toan)) {
            $hoaDon->update(['ma_thanh_toan' => $this->sepayService->paymentCode($hoaDon)]);
        }

        return view('customer.checkout', [
            'hoaDon' => $hoaDon,
            'paymentCode' => $this->sepayService->paymentCode($hoaDon),
            'qrUrl' => $this->sepayService->qrUrl($hoaDon),
            'bankName' => config('sepay.bank_name'),
            'bankCode' => config('sepay.bank_code'),
            'bankAccount' => config('sepay.bank_account'),
            'accountName' => config('sepay.account_name'),
        ]);
    }

    public function status(HoaDon $hoaDon): JsonResponse
    {
        return response()->json([
            'status' => $hoaDon->trang_thai,
            'paid_at' => $hoaDon->thoi_gian_thanh_toan?->format('Y-m-d H:i:s'),
        ]);
    }

    public function webhook(Request $request): JsonResponse
    {
        logger()->info('SEPAY HEADERS', $request->headers->all());
        logger()->info('SEPAY BODY', $request->all());
        //if (! $this->sepayService->verifyWebhook($request)) {
           // return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        //}

        $payload = $request->json()->all();
        if (empty($payload)) {
            return response()->json(['success' => false, 'message' => 'No data'], 400);
        }

        $transaction = $this->sepayService->recordTransaction($payload);
        $orderId = $this->resolveOrderIdFromWebhookPayload($payload);

        if (! $orderId) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy mã hóa đơn.']);
        }

        $hoaDon = HoaDon::query()->find($orderId);
        if (! $hoaDon) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy hóa đơn.']);
        }

        $amountIn = (float) $transaction->amount_in;
        $total = (float) $hoaDon->tong_tien;

        if ($amountIn <= 0 || (int)$amountIn !== (int)$total) {
            return response()->json(['success' => false, 'message' => 'Sai số tiền thanh toán.']);
        }

        if ((int) $transaction->ma_hoa_don !== (int) $hoaDon->ma_hoa_don) {
            $transaction->update(['ma_hoa_don' => $hoaDon->ma_hoa_don]);
        }

        if ($hoaDon->trang_thai !== HoaDon::TRANG_THAI_DA_THANH_TOAN) {
            $hoaDon->update([
                'trang_thai' => HoaDon::TRANG_THAI_DA_THANH_TOAN,
                'thoi_gian_thanh_toan' => now(),
            ]);
        }

        return response()->json(['success' => true]);
    }

    private function resolveOrderIdFromWebhookPayload(array $payload): ?int
    {
        foreach (['content', 'code', 'description'] as $field) {
            $value = (string) ($payload[$field] ?? '');
            if ($value === '') {
                continue;
            }

            $orderId = $this->sepayService->extractOrderId($value);
            if ($orderId) {
                return $orderId;
            }
        }

        return null;
    }

    public function history(HoaDon $hoaDon): View
    {
        $hoaDon->load([
            'nguoiDung',
            'chiTiets.mon.giaMoiNhat',
            'chiTiets.toppings.mon.giaMoiNhat',
            'chiTiets.tuyChinhs.nguyenLieu',
            'sepayTransactions' => fn ($query) => $query->latest(),
        ]);

        return view('payment.history', [
            'hoaDon' => $hoaDon,
            'transactions' => $hoaDon->sepayTransactions,
        ]);
    }

    public function refund(Request $request, HoaDon $hoaDon): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        if ($hoaDon->trang_thai !== HoaDon::TRANG_THAI_DA_THANH_TOAN) {
            return back()->with('error', 'Hóa đơn chưa thanh toán.');
        }

        if ((float) $data['amount'] > (float) $hoaDon->tong_tien) {
            return back()->with('error', 'Số tiền hoàn vượt quá tổng tiền hóa đơn.');
        }

        $refund = SePayRefund::query()->create([
            'ma_hoa_don' => $hoaDon->ma_hoa_don,
            'amount' => $data['amount'],
            'reason' => $data['reason'] ?? null,
            'status' => 'requested',
        ]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Đã tạo yêu cầu hoàn tiền.', 'refund_id' => $refund->id]);
        }

        return redirect()->route('payment.history', $hoaDon)->with('success', 'Đã tạo yêu cầu hoàn tiền.');
    }
}
