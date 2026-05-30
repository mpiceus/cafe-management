@extends('layouts.app')

@section('title', 'Cập nhật nguyên liệu')

@section('content')
<div class="d-flex justify-content-between mb-3"><h1 class="h4 mb-0">Cập nhật nguyên liệu</h1><a class="btn btn-outline-secondary" href="{{ route('nguyen-lieu.index') }}">Quay lại</a></div>
<div class="card page-card"><div class="card-body"><form method="POST" action="{{ route('nguyen-lieu.update', $nguyenLieu) }}" data-persist-key="nguyen-lieu-edit-{{ $nguyenLieu->ma_nguyen_lieu }}" data-persist-clear-on-submit="1">@csrf @method('PUT') @include('nguyen-lieu.form')<button class="btn btn-primary">Cập nhật</button></form></div></div>
@endsection
