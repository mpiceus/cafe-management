@extends('layouts.app')

@section('title', 'Cập nhật nhà cung cấp')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Cập nhật nhà cung cấp</h1>
    <a class="btn btn-outline-secondary" href="{{ route('nha-cung-cap.index') }}">Quay lại</a>
</div>

<div class="card page-card">
    <div class="card-body">
        <form method="POST" action="{{ route('nha-cung-cap.update', $nhaCungCap) }}" data-persist-key="nha-cung-cap-edit-{{ $nhaCungCap->ma_nha_cung_cap }}" data-persist-clear-on-submit="1">
            @csrf
            @method('PUT')
            @include('nha-cung-cap.form')
            <button class="btn btn-primary">Cập nhật</button>
        </form>
    </div>
</div>
@endsection
