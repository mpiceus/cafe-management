@extends('layouts.app')

@section('title', 'Thêm nguyên liệu')

@section('content')
<div class="d-flex justify-content-between mb-3"><h1 class="h4 mb-0">Thêm nguyên liệu</h1><a class="btn btn-outline-secondary" href="{{ route('nguyen-lieu.index') }}">Quay lại</a></div>
<div class="card page-card"><div class="card-body"><form method="POST" action="{{ route('nguyen-lieu.store') }}" data-persist-key="nguyen-lieu-create" data-persist-clear-on-submit="1">@csrf @include('nguyen-lieu.form')<button class="btn btn-primary">Lưu</button></form></div></div>
@endsection
