@extends('layouts.app')
@section('title', 'Báo cáo thống kê')
@section('content')
<div class="mb-3"><h1 class="h4 mb-0">Báo cáo thống kê</h1><div class="text-muted">Doanh thu, món bán chạy và tồn kho</div></div>
<div class="row g-3 mb-3">
@foreach([['Doanh thu', number_format($tongQuan['doanh_thu'], 0, ',', '.') . ' đ'], ['Số hóa đơn', $tongQuan['so_hoa_don']], ['Chờ pha chế', $tongQuan['hoa_don_cho_pha_che']], ['Nguyên liệu sắp hết', $tongQuan['nguyen_lieu_sap_het']]] as [$label, $value])
<div class="col-md-3"><div class="card page-card"><div class="card-body"><div class="text-muted small">{{ $label }}</div><div class="h4 mb-0">{{ $value }}</div></div></div></div>
@endforeach
</div>
<div class="row g-3">
<div class="col-lg-4"><div class="card page-card"><div class="card-header bg-white fw-semibold">Món bán chạy</div><div class="list-group list-group-flush">@forelse($monBanChay as $item)<div class="list-group-item d-flex justify-content-between"><span>{{ $item->mon?->ten_mon }}</span><strong>{{ $item->tong_so_luong }}</strong></div>@empty<div class="list-group-item text-muted">Chưa có dữ liệu.</div>@endforelse</div></div></div>
<div class="col-lg-4"><div class="card page-card"><div class="card-header bg-white fw-semibold">Nguyên liệu sắp hết</div><div class="list-group list-group-flush">@forelse($nguyenLieuSapHet as $nl)<div class="list-group-item d-flex justify-content-between"><span>{{ $nl->ten_nguyen_lieu }}</span><strong>{{ $nl->ton_kho }}</strong></div>@empty<div class="list-group-item text-muted">Không có nguyên liệu sắp hết.</div>@endforelse</div></div></div>
<div class="col-lg-4"><div class="card page-card"><div class="card-header bg-white fw-semibold">Nguyên liệu tồn nhiều</div><div class="list-group list-group-flush">@forelse($nguyenLieuTonNhieu as $nl)<div class="list-group-item d-flex justify-content-between"><span>{{ $nl->ten_nguyen_lieu }}</span><strong>{{ $nl->ton_kho }}</strong></div>@empty<div class="list-group-item text-muted">Chưa có dữ liệu.</div>@endforelse</div></div></div>
</div>
@endsection
