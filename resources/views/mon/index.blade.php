@extends('layouts.app')

@section('title', 'Quản lý món')

@section('content')
@php($canManage = auth()->user()->chuc_vu === \App\Models\NguoiDung::CHUC_VU_CHU_CUA_HANG)
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-0">Quản lý món</h1>
        <div class="text-muted">Bấm tên món để xem công thức, bấm giá để xem lịch sử giá</div>
    </div>
    @if($canManage)
        <a class="btn btn-primary" href="{{ route('mon.create') }}">Thêm món</a>
    @endif
</div>

<div class="card page-card mb-3">
    <div class="card-body">
        <form class="row g-3 align-items-end" method="GET" action="{{ route('mon.index') }}">
            <div class="col-md-4">
                <label class="form-label" for="tu_khoa">Tìm kiếm món</label>
                <input id="tu_khoa" name="tu_khoa" class="form-control" value="{{ $filters['tu_khoa'] ?? '' }}" placeholder="Tên món">
            </div>
            <div class="col-md-3">
                <label class="form-label" for="ma_loai_mon">Loại món</label>
                <select id="ma_loai_mon" name="ma_loai_mon" class="form-select">
                    <option value="">Tất cả</option>
                    @foreach($loaiMons as $loaiMon)
                        <option value="{{ $loaiMon->ma_loai_mon }}" @selected(($filters['ma_loai_mon'] ?? '') == $loaiMon->ma_loai_mon)>{{ $loaiMon->ten_loai_mon }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label" for="trang_thai">Trạng thái</label>
                <select id="trang_thai" name="trang_thai" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="{{ \App\Models\Mon::TRANG_THAI_DANG_BAN }}" @selected(($filters['trang_thai'] ?? '') === \App\Models\Mon::TRANG_THAI_DANG_BAN)>Đang bán</option>
                    <option value="{{ \App\Models\Mon::TRANG_THAI_DUNG_BAN }}" @selected(($filters['trang_thai'] ?? '') === \App\Models\Mon::TRANG_THAI_DUNG_BAN)>Dừng bán</option>
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-outline-primary" type="submit">Lọc</button>
            </div>
        </form>
    </div>
</div>

<div class="card page-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Món</th>
                    <th>Loại</th>
                    <th>Giá hiện tại</th>
                    <th>Phục vụ</th>
                    <th>Topping</th>
                    <th>Trạng thái</th>
                    <th>Tình trạng kho</th>
                    @if($canManage)
                        <th class="text-end">Thao tác</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($mons as $mon)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-light border rounded overflow-hidden" style="width: 58px; height: 58px;">
                                    @if($mon->hinh_anh)
                                        <img src="{{ asset('storage/'.$mon->hinh_anh) }}" alt="{{ $mon->ten_mon }}" class="w-100 h-100 object-fit-cover">
                                    @endif
                                </div>
                                <div>
                                    <a class="fw-semibold text-decoration-none" href="{{ route('cong-thuc.show', $mon) }}">{{ $mon->ten_mon }}</a>
                                    <div class="text-muted small">{{ \Illuminate\Support\Str::limit($mon->mo_ta, 80) }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $mon->loaiMon?->ten_loai_mon }}</td>
                        <td>
                            <a class="text-decoration-none" href="{{ route('gia-mon.show', $mon) }}">
                                {{ $mon->giaMoiNhat ? number_format($mon->giaMoiNhat->gia, 0, ',', '.') . ' đ' : 'Chưa có giá' }}
                            </a>
                        </td>
                        <td>{{ ['ca_hai' => 'Cả hai', 'chi_nong' => 'Chỉ nóng', 'chi_lanh' => 'Chỉ lạnh', 'khong_ap_dung' => 'Không áp dụng'][$mon->che_do_phuc_vu] ?? $mon->che_do_phuc_vu }}</td>
                        <td>{{ $mon->cho_them_topping ? 'Có' : 'Không' }}</td>
                        <td>
                            <span class="badge {{ $mon->dangBan() ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $mon->dangBan() ? 'Đang bán' : 'Dừng bán' }}</span>
                        </td>
                        <td>
                            @if($mon->tam_het)
                                <span class="badge text-bg-warning">Tạm hết</span>
                                <div class="small text-muted mt-1">{{ $mon->tam_het_nguyen_lieus->join(', ') }}</div>
                            @else
                                <span class="badge text-bg-light">Đủ nguyên liệu</span>
                            @endif
                        </td>
                        @if($canManage)
                            <td class="text-end">
                                <div class="d-inline-flex gap-2">
                                    <a class="btn btn-outline-primary btn-sm" href="{{ route('mon.edit', $mon) }}">Sửa</a>
                                    <form method="POST" action="{{ route('mon.toggle-status', $mon) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-outline-secondary btn-sm" type="submit">{{ $mon->dangBan() ? 'Dừng bán' : 'Bán lại' }}</button>
                                    </form>
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $canManage ? 8 : 7 }}" class="text-center text-muted py-4">Chưa có món.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $mons->links() }}</div>
</div>
@endsection
