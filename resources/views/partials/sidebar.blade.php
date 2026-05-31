@php($user = auth()->user())
@php($isAdmin = $user->chuc_vu === \App\Models\NguoiDung::CHUC_VU_CHU_CUA_HANG)
@php($isOrderStaff = $user->chuc_vu === \App\Models\NguoiDung::CHUC_VU_NHAN_VIEN_ORDER)
@php($isBarista = $user->chuc_vu === \App\Models\NguoiDung::CHUC_VU_NHAN_VIEN_PHA_CHE)
<aside class="sidebar flex-column p-3" id="app-sidebar">
    <div class="text-white fw-semibold fs-5 px-2 py-3">Quản lý Cafe</div>
    <nav class="nav flex-column gap-1">
        @if($isAdmin)
            <a data-menu-key="dashboard" class="nav-link rounded {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Trang chủ</a>
        @endif

        @if($isAdmin)
            <a data-menu-key="nguoi-dung" class="nav-link rounded {{ request()->routeIs('nguoi-dung.*') ? 'active' : '' }}" href="{{ route('nguoi-dung.index') }}">Người dùng</a>
            <a data-menu-key="mon" class="nav-link rounded {{ request()->routeIs('mon.*') ? 'active' : '' }}" href="{{ route('mon.index') }}">Quản lý món</a>
            <a data-menu-key="gia-mon" class="nav-link rounded {{ request()->routeIs('gia-mon.*') ? 'active' : '' }}" href="{{ route('gia-mon.index') }}">Quản lý giá bán</a>
            <a data-menu-key="cong-thuc" class="nav-link rounded {{ request()->routeIs('cong-thuc.*') ? 'active' : '' }}" href="{{ route('cong-thuc.index') }}">Công thức</a>
            <a data-menu-key="nguyen-lieu" class="nav-link rounded {{ request()->routeIs('nguyen-lieu.*') ? 'active' : '' }}" href="{{ route('nguyen-lieu.index') }}">Nguyên liệu</a>
            <a data-menu-key="don-nhap" class="nav-link rounded {{ request()->routeIs('don-nhap.*') ? 'active' : '' }}" href="{{ route('don-nhap.index') }}">Nhập hàng</a>
            <a data-menu-key="bao-cao" class="nav-link rounded {{ request()->routeIs('bao-cao.*') ? 'active' : '' }}" href="{{ route('bao-cao.index') }}">Báo cáo</a>
        @endif

        @if($isOrderStaff)
            <a data-menu-key="mon" class="nav-link rounded {{ request()->routeIs('mon.*') ? 'active' : '' }}" href="{{ route('mon.index') }}">Quản lý món</a>
            <a data-menu-key="cong-thuc" class="nav-link rounded {{ request()->routeIs('cong-thuc.*') ? 'active' : '' }}" href="{{ route('cong-thuc.index') }}">Công thức</a>
            <a data-menu-key="don-nhap" class="nav-link rounded {{ request()->routeIs('don-nhap.*') ? 'active' : '' }}" href="{{ route('don-nhap.index') }}">Nhập hàng</a>
        @endif

        @if($isBarista)
            <a data-menu-key="mon" class="nav-link rounded {{ request()->routeIs('mon.*') ? 'active' : '' }}" href="{{ route('mon.index') }}">Quản lý món</a>
            <a data-menu-key="cong-thuc" class="nav-link rounded {{ request()->routeIs('cong-thuc.*') ? 'active' : '' }}" href="{{ route('cong-thuc.index') }}">Công thức</a>
        @endif

        @if(in_array($user->chuc_vu, [\App\Models\NguoiDung::CHUC_VU_CHU_CUA_HANG, \App\Models\NguoiDung::CHUC_VU_NHAN_VIEN_ORDER], true))
            <a data-menu-key="order" class="nav-link rounded {{ request()->routeIs('order.*') ? 'active' : '' }}" href="{{ route('order.index') }}">Order và thanh toán</a>
        @endif

        @if(in_array($user->chuc_vu, [\App\Models\NguoiDung::CHUC_VU_CHU_CUA_HANG, \App\Models\NguoiDung::CHUC_VU_NHAN_VIEN_PHA_CHE], true))
            <a data-menu-key="pha-che" class="nav-link rounded {{ request()->routeIs('pha-che.*') ? 'active' : '' }}" href="{{ route('pha-che.index') }}">Pha chế</a>
        @endif
    </nav>
</aside>
