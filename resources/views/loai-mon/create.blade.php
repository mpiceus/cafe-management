@extends('layouts.app')

@section('title', 'Thêm loại món')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Thêm loại món</h1>
    <a class="btn btn-outline-secondary" href="{{ route('loai-mon.index') }}">Quay lại</a>
</div>

<div class="card page-card">
    <div class="card-body">
        <form method="POST" action="{{ route('loai-mon.store') }}" data-persist-key="loai-mon-create" data-persist-clear-on-submit="1">
            @csrf
            @include('loai-mon.form')
            <button class="btn btn-primary">Lưu</button>
        </form>
    </div>
</div>
@endsection
