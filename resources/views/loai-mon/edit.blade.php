@extends('layouts.app')

@section('title', 'Cập nhật loại món')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Cập nhật loại món</h1>
    <a class="btn btn-outline-secondary" href="{{ route('loai-mon.index') }}">Quay lại</a>
</div>

<div class="card page-card">
    <div class="card-body">
        <form method="POST" action="{{ route('loai-mon.update', $loaiMon) }}" data-persist-key="loai-mon-edit-{{ $loaiMon->ma_loai_mon }}" data-persist-clear-on-submit="1">
            @csrf
            @method('PUT')
            @include('loai-mon.form')
            <button class="btn btn-primary">Cập nhật</button>
        </form>
    </div>
</div>
@endsection
