@extends('layouts.app')

@section('title', 'Thêm người dùng')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Thêm người dùng</h1>
    <a class="btn btn-outline-secondary" href="{{ route('nguoi-dung.index') }}">Quay lại</a>
</div>

<div class="card page-card">
    <div class="card-body">
        <form method="POST" action="{{ route('nguoi-dung.store') }}">
            @csrf
            @include('nguoi-dung.form', ['nguoiDung' => $nguoiDung])
            <button class="btn btn-primary" type="submit">Lưu</button>
        </form>
    </div>
</div>
@endsection
