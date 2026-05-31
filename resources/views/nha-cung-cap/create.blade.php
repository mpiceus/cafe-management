@extends('layouts.app')

@section('title', 'Thêm nhà cung cấp')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Thêm nhà cung cấp</h1>
    <a class="btn btn-outline-secondary" href="{{ route('nha-cung-cap.index') }}">Quay lại</a>
</div>

<div class="card page-card">
    <div class="card-body">
        <form method="POST" action="{{ route('nha-cung-cap.store') }}" data-persist-key="nha-cung-cap-create" data-persist-clear-on-submit="1">
            @csrf
            @include('nha-cung-cap.form')
            <button class="btn btn-primary">Lưu</button>
        </form>
    </div>
</div>
@endsection
