@extends('layouts.app')
@section('title', 'Báo cáo thống kê')
@section('content')
<div class="mb-3">
	<h1 class="h4 mb-0">Báo cáo thống kê</h1>
	<div class="text-muted">Doanh thu, món bán chạy và tồn kho</div>
</div>

<div class="card page-card mb-3">
	<div class="card-body">
		<form class="row g-3 align-items-end" method="GET">
			<div class="col-md-4">
				<label class="form-label">Từ ngày</label>
				<input type="date" class="form-control" name="tu_ngay" value="{{ $filters['tu_ngay'] ?? '' }}">
			</div>
			<div class="col-md-4">
				<label class="form-label">Đến ngày</label>
				<input type="date" class="form-control" name="den_ngay" value="{{ $filters['den_ngay'] ?? '' }}">
			</div>
			<div class="col-md-4 d-grid">
				<button class="btn btn-outline-primary">Lọc báo cáo</button>
			</div>
		</form>
		<div class="d-flex gap-2 mt-3">
			<a class="btn btn-outline-success" href="{{ route('bao-cao.export') }}?tu_ngay={{ $filters['tu_ngay'] ?? '' }}&den_ngay={{ $filters['den_ngay'] ?? '' }}">Xuất Excel</a>
		</div>
	</div>
</div>
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

<div class="row g-3 mt-1">
	<div class="col-lg-6">
		<div class="card page-card">
			<div class="card-header bg-white fw-semibold">Doanh thu theo ngày</div>
			<div class="table-responsive">
				<table class="table table-sm mb-0">
					<thead class="table-light">
						<tr><th>Ngày</th><th class="text-end">Doanh thu</th></tr>
					</thead>
					<tbody>
						@forelse($doanhThuTheoNgay as $row)
							<tr><td>{{ $row->ngay }}</td><td class="text-end">{{ number_format($row->doanh_thu, 0, ',', '.') }} đ</td></tr>
						@empty
							<tr><td colspan="2" class="text-center text-muted py-3">Chưa có dữ liệu.</td></tr>
						@endforelse
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="col-lg-6">
		<div class="card page-card">
			<div class="card-header bg-white fw-semibold">Doanh thu theo tháng</div>
			<div class="table-responsive">
				<table class="table table-sm mb-0">
					<thead class="table-light">
						<tr><th>Tháng</th><th class="text-end">Doanh thu</th></tr>
					</thead>
					<tbody>
						@forelse($doanhThuTheoThang as $row)
							<tr><td>{{ $row->thang }}</td><td class="text-end">{{ number_format($row->doanh_thu, 0, ',', '.') }} đ</td></tr>
						@empty
							<tr><td colspan="2" class="text-center text-muted py-3">Chưa có dữ liệu.</td></tr>
						@endforelse
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<div class="card page-card mt-3">
	<div class="card-header bg-white fw-semibold">Gợi ý nhập kho ({{ $goiYKhoRange['tu_ngay'] }} - {{ $goiYKhoRange['den_ngay'] }})</div>
	<div class="table-responsive">
		<table class="table table-hover align-middle mb-0">
			<thead class="table-light">
				<tr>
					<th>Nguyên liệu</th>
					<th class="text-end">Tồn kho</th>
					<th class="text-end">Đề xuất nhập</th>
					<th>Đơn vị</th>
				</tr>
			</thead>
			<tbody>
				@forelse($goiYNhapKho as $row)
					<tr>
						<td class="fw-semibold">{{ $row['ten'] }}</td>
						<td class="text-end">{{ number_format($row['ton_kho'], 2, ',', '.') }}</td>
						<td class="text-end text-danger fw-semibold">{{ number_format($row['de_xuat'], 2, ',', '.') }}</td>
						<td>{{ $row['don_vi'] }}</td>
					</tr>
				@empty
					<tr><td colspan="4" class="text-center text-muted py-3">Chưa có đề xuất nhập kho.</td></tr>
				@endforelse
			</tbody>
		</table>
	</div>
</div>
@endsection
