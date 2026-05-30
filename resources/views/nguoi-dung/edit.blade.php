@extends('layouts.app')

@section('title', 'Cập nhật người dùng')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Cập nhật người dùng</h1>
    <a class="btn btn-outline-secondary" href="{{ route('nguoi-dung.index') }}">Quay lại</a>
</div>

<div class="card page-card">
    <div class="card-body">
        <form method="POST" action="{{ route('nguoi-dung.update', $nguoiDung) }}">
            @csrf
            @method('PUT')
            @include('nguoi-dung.form', ['nguoiDung' => $nguoiDung])
            <button class="btn btn-primary" type="submit">Cập nhật</button>
        </form>
    </div>
</div>
@endsection
