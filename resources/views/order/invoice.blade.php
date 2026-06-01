<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hóa đơn #{{ $hoaDon->ma_hoa_don }}</title>
    <link rel="stylesheet" href="{{ resource_path('css/order-invoice.css') }}">
</head>
<body>
    <div class="header">
        <div class="title">HÓA ĐƠN THANH TOÁN</div>
        <div class="muted">Mã hóa đơn: #{{ $hoaDon->ma_hoa_don }}</div>
    </div>

    <div class="meta">
        <div>Thời gian: {{ $hoaDon->thoi_gian_tao->format('d/m/Y H:i') }}</div>
        <div>Nhân viên: {{ $hoaDon->nguoiDung?->ho_ten }}</div>
        <div>Hình thức: {{ $hoaDon->phuong_thuc_thanh_toan === 'chuyen_khoan' ? 'Chuyển khoản' : 'Tiền mặt' }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Món</th>
                <th class="text-right">SL</th>
                <th class="text-right">Đơn giá</th>
                <th class="text-right">Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            @foreach($hoaDon->chiTiets as $chiTiet)
                @php
                    $giaMon = $chiTiet->mon?->giaMoiNhat?->gia ?? 0;
                    $lineTotal = $giaMon * $chiTiet->so_luong;
                @endphp
                <tr>
                    <td>
                        <div><strong>{{ $chiTiet->mon?->ten_mon }}</strong></div>
                        @if($chiTiet->che_do)
                            @php
                                $cheDoLabel = [
                                    'chi_nong' => 'Nóng',
                                    'chi_lanh' => 'Lạnh',
                                    'nong' => 'Nóng',
                                    'lanh' => 'Lạnh',
                                ][$chiTiet->che_do] ?? $chiTiet->che_do;
                            @endphp
                            <div class="muted">Chế độ: {{ $cheDoLabel }}</div>
                        @endif
                        @if($chiTiet->ghi_chu)
                            <div class="muted">Ghi chú: {{ $chiTiet->ghi_chu }}</div>
                        @endif
                        @if($chiTiet->tuyChinhs->isNotEmpty())
                            <div class="muted">Tùy chỉnh: {{ $chiTiet->tuyChinhs->map(fn($t) => $t->nguyenLieu?->ten_nguyen_lieu.' '.$t->ti_le.'%')->join(', ') }}</div>
                        @endif
                        @if($chiTiet->toppings->isNotEmpty())
                            <div class="muted">Topping: {{ $chiTiet->toppings->map(fn($t) => $t->mon?->ten_mon.' x '.$t->so_luong)->join(', ') }}</div>
                        @endif
                    </td>
                    <td class="text-right">{{ $chiTiet->so_luong }}</td>
                    <td class="text-right">{{ number_format($giaMon, 0, ',', '.') }} đ</td>
                    <td class="text-right">{{ number_format($lineTotal, 0, ',', '.') }} đ</td>
                </tr>
                @foreach($chiTiet->toppings as $topping)
                    @php
                        $toppingPrice = $topping->mon?->giaMoiNhat?->gia ?? 0;
                        $toppingTotal = $toppingPrice * $topping->so_luong;
                    @endphp
                    <tr>
                        <td class="muted">+ {{ $topping->mon?->ten_mon }}</td>
                        <td class="text-right">{{ $topping->so_luong }}</td>
                        <td class="text-right">{{ number_format($toppingPrice, 0, ',', '.') }} đ</td>
                        <td class="text-right">{{ number_format($toppingTotal, 0, ',', '.') }} đ</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-right total">Tổng cộng</td>
                <td class="text-right total">{{ number_format($hoaDon->tong_tien, 0, ',', '.') }} đ</td>
            </tr>
        </tfoot>
    </table>

    <div class="muted invoice-thanks">Cảm ơn quý khách!</div>
</body>
</html>
