@extends('layouts.app')

@section('title', 'Đăng nhập')

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center px-3">
    <div class="card page-card" style="max-width: 420px; width: 100%;">
        <div class="card-body p-4">
            <h1 class="h4 mb-1">Đăng nhập</h1>
            <p class="text-muted mb-4">Hệ thống quản lý Ann cafe</p>

            <form method="POST" action="{{ route('login.submit') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="ten_dang_nhap">Tên đăng nhập</label>
                    <input id="ten_dang_nhap" name="ten_dang_nhap" class="form-control @error('ten_dang_nhap') is-invalid @enderror" value="{{ old('ten_dang_nhap') }}" autofocus>
                    @error('ten_dang_nhap')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="mat_khau">Mật khẩu</label>
                    <input id="mat_khau" name="mat_khau" type="password" class="form-control @error('mat_khau') is-invalid @enderror">
                    @error('mat_khau')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button class="btn btn-primary w-100" type="submit">Đăng nhập</button>
            </form>
        </div>
    </div>
</div>
@endsection
