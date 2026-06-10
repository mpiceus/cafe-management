@extends('layouts.app')

@section('title', 'Tạo order mới')

@section('content')
@push('styles')
    <link rel="stylesheet" href="{{ \App\Http\Controllers\ResourceAssetController::url('css', 'order-create.css') }}">
@endpush
<div class="order-heading d-flex justify-content-between align-items-center mb-2">
    <div>
        <h1 class="h5 mb-0">Tạo order mới</h1>
    </div>
    <a class="btn btn-outline-secondary btn-sm" href="{{ route('order.index') }}">Quay lại danh sách hóa đơn</a>
</div>

<div class="row g-3 order-page-shell">
    <div class="col-xl-7 d-flex min-w-0">
        <div class="card page-card order-column-card flex-fill overflow-hidden">
            <div class="card-body d-flex flex-column order-panel">
                <div class="row g-3 align-items-end mb-3 flex-shrink-0">
                    <div class="col-lg-8">
                        <label class="form-label" for="menu-search">Tìm món</label>
                        <input id="menu-search" class="form-control" placeholder="Nhập tên món">
                    </div>
                    <div class="col-lg-4">
                        <label class="form-label" for="menu-category">Loại món</label>
                        <select id="menu-category" class="form-select">
                            <option value="">Tất cả</option>
                            @foreach($loaiMons as $loaiMon)
                                <option value="{{ $loaiMon->ma_loai_mon }}">{{ $loaiMon->ten_loai_mon }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div id="menu-grid" class="row g-3 order-panel-scroll">
                    <div class="col-12 text-muted text-center py-4">Đang tải danh sách món...</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-5 d-flex min-w-0">
        <div class="card page-card order-column-card flex-fill overflow-hidden">
            <div class="card-body d-flex flex-column order-panel">
                <div id="order-message" class="alert d-none mb-3 flex-shrink-0" role="alert"></div>

                <form id="order-form" method="POST" action="{{ route('order.store') }}" class="d-flex flex-column flex-grow-1 min-h-0">
                    @csrf
                    <div class="mb-3 flex-shrink-0">
                        <label class="form-label">Hình thức thanh toán</label>
                        <div class="d-flex gap-3 flex-wrap">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" checked name="phuong_thuc_thanh_toan" value="tien_mat" id="pm-cash">
                                <label class="form-check-label" for="pm-cash">Tiền mặt</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="phuong_thuc_thanh_toan" value="chuyen_khoan" id="pm-bank">
                                <label class="form-check-label" for="pm-bank">Chuyển khoản</label>
                            </div>
                        </div>
                    </div>

                    <div id="order-lines" class="d-flex flex-column gap-3 order-panel-scroll flex-grow-1"></div>
                    <div class="text-muted small mb-3 flex-shrink-0" id="order-empty">Chưa có món nào trong hóa đơn.</div>

                    <div class="d-flex justify-content-between align-items-center border-top pt-3 mt-3 flex-shrink-0">
                        <span class="fw-semibold">Tổng tiền</span>
                        <span id="order-total" class="fs-5">0 đ</span>
                    </div>

                    <div id="cash-section" class="mt-3 d-none flex-shrink-0">
                        <label class="form-label" for="cash-received">Tiền khách đưa</label>
                        <input id="cash-received" class="form-control" type="number" min="0" step="1000" placeholder="Nhập số tiền khách đưa">
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span class="text-muted">Tiền thừa</span>
                            <span id="cash-change" class="fw-semibold">0 đ</span>
                        </div>
                    </div>

                    <div id="hidden-inputs"></div>
                    <button id="order-submit" class="btn btn-primary w-100 mt-3 flex-shrink-0" type="submit" disabled>Thanh toán</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="application/json" id="order-menu-data">@json($menuData)</script>

@push('scripts')
    <script src="{{ \App\Http\Controllers\ResourceAssetController::url('js', 'order-create.js') }}"></script>
@endpush
@endsection
