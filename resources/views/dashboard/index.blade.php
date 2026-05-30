@extends('layouts.app')

@section('title', 'Trang chủ')

@section('content')
<div class="row g-3">
    <div class="col-12">
        <div class="card page-card">
            <div class="card-body">
                <h1 class="h4 mb-2">Trang chủ</h1>
                <p class="text-muted mb-0">Xin chào {{ auth()->user()->ho_ten }}. Chọn chức năng ở menu bên trái để tiếp tục.</p>
            </div>
        </div>
    </div>
</div>
@endsection
