@extends('layouts.app')

@section('title', 'Tạo đơn nhập')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h1 class="h4 mb-0">Tạo đơn nhập</h1>
    <a class="btn btn-outline-secondary" href="{{ route('don-nhap.index') }}">Quay lại</a>
</div>

<div class="card page-card">
    <div class="card-body">
        <form method="POST" action="{{ route('don-nhap.store') }}" data-persist-key="don-nhap-create" data-persist-clear-on-submit="1">
            @csrf
            <div class="mb-4">
                <label class="form-label">Nhà cung cấp</label>
                <select class="form-select @error('ma_nha_cung_cap') is-invalid @enderror" id="supplier-select" name="ma_nha_cung_cap">
                    <option value="">Chọn nhà cung cấp</option>
                    @foreach($nhaCungCaps as $ncc)
                        <option value="{{ $ncc->ma_nha_cung_cap }}" @selected(old('ma_nha_cung_cap', $prefillSupplierId ?? '') == $ncc->ma_nha_cung_cap)>{{ $ncc->ten_nha_cung_cap }}</option>
                    @endforeach
                </select>
                @error('ma_nha_cung_cap')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            @error('items')<div class="alert alert-danger py-2">{{ $message }}</div>@enderror

            <div id="purchase-list" class="d-flex flex-column gap-3 mb-3"></div>
            <button id="purchase-add" class="draft-add mb-4 w-100" type="button">+ Thêm nguyên liệu cần nhập</button>

            <div class="row justify-content-end mb-4">
                <div class="col-lg-4">
                    <div class="card bg-light border-0">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <span class="fw-semibold">Tổng tiền</span>
                            <span id="purchase-total" class="fs-5">0 đ</span>
                        </div>
                    </div>
                </div>
            </div>

            <button class="btn btn-primary">Lưu đơn nhập</button>
        </form>
    </div>
</div>
@php
    $nguyenLieuOptions = $nguyenLieus->map(function ($item) {
        return [
            'id' => $item->ma_nguyen_lieu,
            'name' => $item->ten_nguyen_lieu,
            'normalized_name' => \App\Support\TextNormalizer::normalize($item->ten_nguyen_lieu),
            'unit' => $item->don_vi_tinh,
            'ratio' => (float) $item->ti_le_su_dung,
            'supplier_id' => $item->ma_nha_cung_cap,
        ];
    })->values();
@endphp

<script type="application/json" id="purchase-ingredient-data">@json($nguyenLieuOptions)</script>
<script type="application/json" id="purchase-initial-rows">@json(array_values(old('items', $prefillItems ?? [])))</script>

@push('scripts')
    <script src="{{ \App\Http\Controllers\ResourceAssetController::url('js', 'don-nhap-create.js') }}"></script>
@endpush
@endsection
