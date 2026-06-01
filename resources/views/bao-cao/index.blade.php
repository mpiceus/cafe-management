@extends('layouts.app')

@section('title', 'Báo cáo thống kê')

@section('content')
@push('styles')
    <link rel="stylesheet" href="{{ \App\Http\Controllers\ResourceAssetController::url('css', 'bao-cao.css') }}">
@endpush

@php
    $maxMonBanChay = max(1, (float) ($monBanChay->max('tong_so_luong') ?? 1));
    $chartData = [
        'daily' => $doanhThuTheoNgay->map(fn ($row) => [
            'label' => \Carbon\Carbon::parse($row->ngay)->format('d/m'),
            'value' => (float) $row->doanh_thu,
        ])->values(),
        'monthly' => $doanhThuTheoThang->map(fn ($row) => [
            'label' => \Carbon\Carbon::createFromFormat('Y-m', $row->thang)->format('m/Y'),
            'value' => (float) $row->doanh_thu,
        ])->values(),
    ];
    $summaryCards = [
        ['label' => 'Doanh thu', 'value' => number_format($tongQuan['doanh_thu'], 0, ',', '.') . ' đ', 'icon' => 'revenue', 'tone' => ''],
        ['label' => 'Số hóa đơn', 'value' => $tongQuan['so_hoa_don'], 'icon' => 'invoice', 'tone' => 'is-success'],
        ['label' => 'Chờ pha chế', 'value' => $tongQuan['hoa_don_cho_pha_che'], 'icon' => 'clock', 'tone' => 'is-warning'],
        ['label' => 'Nguyên liệu sắp hết', 'value' => $tongQuan['nguyen_lieu_sap_het'], 'icon' => 'stock', 'tone' => 'is-warning'],
    ];
@endphp

<div class="report-page">
    <div class="report-heading mb-3">
        <div>
            <h1 class="h4 mb-1">Báo cáo thống kê</h1>
            <div class="text-muted">Theo dõi doanh thu, món bán chạy và tồn kho nguyên liệu.</div>
        </div>
    </div>

    <div class="card report-filter-card mb-3">
        <div class="card-body">
            <form class="row g-3 align-items-end" method="GET">
                <div class="col-lg-3 col-md-4">
                    <label class="form-label fw-semibold">Từ ngày</label>
                    <input type="date" class="form-control" name="tu_ngay" value="{{ $filters['tu_ngay'] ?? '' }}">
                </div>
                <div class="col-lg-3 col-md-4">
                    <label class="form-label fw-semibold">Đến ngày</label>
                    <input type="date" class="form-control" name="den_ngay" value="{{ $filters['den_ngay'] ?? '' }}">
                </div>
                <div class="col-lg-6 col-md-4 report-filter-actions">
                    <button class="btn btn-primary px-4" type="submit">Lọc báo cáo</button>
                    <a class="btn btn-success d-inline-flex align-items-center justify-content-center gap-2 px-4" href="{{ route('bao-cao.export', ['tu_ngay' => $filters['tu_ngay'] ?? '', 'den_ngay' => $filters['den_ngay'] ?? '']) }}">
                        <svg class="report-action-icon" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v12"/><path d="m7 10 5 5 5-5"/><path d="M5 21h14"/></svg>
                        Xuất Excel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-3">
        @foreach($summaryCards as $card)
            <div class="col-xl-3 col-md-6">
                <div class="card report-stat-card">
                    <div class="card-body">
                        <div class="report-stat-header">
                            <div>
                                <div class="report-stat-label">{{ $card['label'] }}</div>
                                <div class="report-stat-value">{{ $card['value'] }}</div>
                            </div>
                            <div class="report-stat-icon {{ $card['tone'] }}">
                                @switch($card['icon'])
                                    @case('revenue')
                                        <svg class="report-icon" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7H14a3.5 3.5 0 0 1 0 7H6"/></svg>
                                        @break
                                    @case('invoice')
                                        <svg class="report-icon" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2h9l3 3v17H6z"/><path d="M14 2v4h4"/><path d="M9 11h6M9 15h6"/></svg>
                                        @break
                                    @case('clock')
                                        <svg class="report-icon" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                                        @break
                                    @default
                                        <svg class="report-icon" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3 2 21h20z"/><path d="M12 9v5M12 18h.01"/></svg>
                                @endswitch
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card report-panel mb-3">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h2 class="report-panel-title">Xu hướng doanh thu</h2>
                    <div class="report-section-note">Giá trị doanh thu theo thời gian đã chọn.</div>
                </div>
                <select class="form-select form-select-sm w-auto" id="report-chart-range" aria-label="Khoảng tổng hợp biểu đồ">
                    <option value="daily">Theo ngày</option>
                    <option value="monthly">Theo tháng</option>
                </select>
            </div>
            <div class="report-chart-wrap">
                <canvas class="report-chart" id="report-revenue-chart"></canvas>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-lg-7">
            <div class="card report-panel h-100">
                <div class="card-body">
                    <h2 class="report-panel-title">Món bán chạy</h2>
                    <div class="report-section-note">Top món theo số lượng bán ra.</div>
                    <div class="report-list">
                        @forelse($monBanChay as $item)
                            @php($progress = min(100, round(((float) $item->tong_so_luong / $maxMonBanChay) * 100, 2)))
                            <div class="report-list-row">
                                <div class="report-list-meta">
                                    <span>{{ $item->mon?->ten_mon ?? 'Món không còn tồn tại' }}</span>
                                    <strong>{{ \App\Support\FormatHelper::number($item->tong_so_luong) }}</strong>
                                </div>
                                <div class="report-progress"><div class="report-progress-bar" data-report-progress="{{ $progress }}"></div></div>
                            </div>
                        @empty
                            <div class="report-empty">Chưa có dữ liệu món bán chạy.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card report-panel h-100">
                <div class="card-body">
                    <h2 class="report-panel-title">Nguyên liệu sắp hết</h2>
                    <div class="report-section-note">Các nguyên liệu đã chạm ngưỡng tồn kho tối thiểu.</div>
                    <div class="report-list">
                        @forelse($nguyenLieuSapHet as $nl)
                            @php($stockProgress = min(100, round(((float) $nl->ton_kho / max(1, (float) $nl->so_luong_toi_thieu)) * 100, 2)))
                            <div class="report-list-row">
                                <div class="report-list-meta">
                                    <span class="report-stock-name">
                                        <svg class="report-warning-icon" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v6M12 17h.01"/></svg>
                                        {{ $nl->ten_nguyen_lieu }}
                                    </span>
                                    <strong>{{ \App\Support\FormatHelper::number($nl->ton_kho) }} {{ $nl->don_vi_tinh }}</strong>
                                </div>
                                <div class="report-progress"><div class="report-progress-bar is-warning" data-report-progress="{{ $stockProgress }}"></div></div>
                            </div>
                        @empty
                            <div class="report-empty">Không có nguyên liệu sắp hết.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card report-panel mb-3">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between gap-3 mb-3">
                <div>
                    <h2 class="report-panel-title">Gợi ý nhập kho</h2>
                    <div class="report-section-note">
                        Ước tính từ dữ liệu bán hàng {{ $goiYKhoRange['tu_ngay'] }} - {{ $goiYKhoRange['den_ngay'] }}.
                    </div>
                </div>
            </div>

            <form class="row g-3 align-items-end report-assistant-filter" method="GET">
                <input type="hidden" name="tu_ngay" value="{{ $filters['tu_ngay'] ?? '' }}">
                <input type="hidden" name="den_ngay" value="{{ $filters['den_ngay'] ?? '' }}">
                <div class="col-lg-2 col-md-4">
                    <label class="form-label fw-semibold">Quan sát</label>
                    <select class="form-select" name="so_ngay_quan_sat">
                        @foreach([7, 14, 30] as $soNgay)
                            <option value="{{ $soNgay }}" @selected($goiYKhoFilters['so_ngay_quan_sat'] === $soNgay)>{{ $soNgay }} ngày</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <label class="form-label fw-semibold">Dự trữ</label>
                    <select class="form-select" name="so_ngay_du_tru">
                        @foreach([3, 7, 14] as $soNgay)
                            <option value="{{ $soNgay }}" @selected($goiYKhoFilters['so_ngay_du_tru'] === $soNgay)>{{ $soNgay }} ngày</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-4 col-md-4">
                    <label class="form-label fw-semibold">Nhà cung cấp</label>
                    <select class="form-select" name="ma_nha_cung_cap">
                        <option value="">Tất cả nhà cung cấp</option>
                        @foreach($nhaCungCaps as $ncc)
                            <option value="{{ $ncc->ma_nha_cung_cap }}" @selected($goiYKhoFilters['ma_nha_cung_cap'] === (int) $ncc->ma_nha_cung_cap)>{{ $ncc->ten_nha_cung_cap }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 col-md-7">
                    <input type="hidden" name="chi_can_nhap" value="0">
                    <div class="form-check report-assistant-check">
                        <input class="form-check-input" id="only-needed-stock" type="checkbox" name="chi_can_nhap" value="1" @checked($goiYKhoFilters['chi_can_nhap'])>
                        <label class="form-check-label" for="only-needed-stock">Chỉ hiển thị mặt hàng cần được nhập</label>
                    </div>
                </div>
                <div class="col-lg-1 col-md-5">
                    <button class="btn btn-outline-primary report-assistant-submit" type="submit">Cập nhật</button>
                </div>
            </form>
        </div>

        <form id="restock-assistant-form" data-create-url="{{ route('don-nhap.create') }}">
            <div class="table-responsive">
                <table class="table table-hover report-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="report-select-column"><input class="form-check-input" id="restock-select-all" type="checkbox" aria-label="Chọn tất cả nguyên liệu cần nhập"></th>
                            <th>Nguyên liệu</th>
                            <th>Nhà cung cấp</th>
                            <th>Ưu tiên</th>
                            <th class="text-end">Tồn / tối thiểu</th>
                            <th class="text-end">Tiêu thụ TB/ngày</th>
                            <th class="text-end">Còn lại</th>
                            <th class="text-end">Đề xuất nhập</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($goiYNhapKho as $row)
                            <tr>
                                <td>
                                    @if($row['de_xuat'] > 0 && $row['ma_nha_cung_cap'])
                                        <input
                                            class="form-check-input restock-item"
                                            type="checkbox"
                                            data-ingredient-id="{{ $row['ma_nguyen_lieu'] }}"
                                            data-supplier-id="{{ $row['ma_nha_cung_cap'] }}"
                                            data-quantity="{{ $row['so_luong_mua'] }}"
                                            data-unit="{{ $row['don_vi_mua'] }}"
                                            aria-label="Chọn {{ $row['ten'] }}">
                                    @endif
                                </td>
                                <td class="fw-semibold">{{ $row['ten'] }}</td>
                                <td>{{ $row['nha_cung_cap'] }}</td>
                                <td><span class="report-priority is-{{ $row['muc_uu_tien'] }}">{{ $row['nhan_uu_tien'] }}</span></td>
                                <td class="text-end">{{ \App\Support\FormatHelper::number($row['ton_kho']) }} / {{ \App\Support\FormatHelper::number($row['ton_toi_thieu']) }} {{ $row['don_vi'] }}</td>
                                <td class="text-end">{{ \App\Support\FormatHelper::number($row['tieu_thu_trung_binh_ngay']) }} {{ $row['don_vi'] }}</td>
                                <td class="text-end">{{ $row['so_ngay_con_lai'] === null ? 'Chưa xác định' : \App\Support\FormatHelper::number($row['so_ngay_con_lai']) . ' ngày' }}</td>
                                <td class="text-end text-danger fw-semibold">{{ \App\Support\FormatHelper::number($row['de_xuat']) }} {{ $row['don_vi'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">Không có nguyên liệu phù hợp với bộ lọc.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="report-assistant-actions">
                <div class="report-section-note" id="restock-assistant-feedback">Chọn các nguyên liệu cùng một nhà cung cấp để tạo đơn nhập.</div>
                <button class="btn btn-primary" type="submit">Tạo đơn nhập từ mục đã chọn</button>
            </div>
        </form>
    </div>
</div>

<script type="application/json" id="report-chart-data">@json($chartData)</script>

@push('scripts')
    <script src="{{ \App\Http\Controllers\ResourceAssetController::url('js', 'bao-cao.js') }}"></script>
@endpush
@endsection
