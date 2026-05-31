@php($user = auth()->user())
@php($isAdmin = $user->chuc_vu === \App\Models\NguoiDung::CHUC_VU_CHU_CUA_HANG)
@php($isOrderStaff = $user->chuc_vu === \App\Models\NguoiDung::CHUC_VU_NHAN_VIEN_ORDER)
@php($isBarista = $user->chuc_vu === \App\Models\NguoiDung::CHUC_VU_NHAN_VIEN_PHA_CHE)
@php($canViewCatalog = $isAdmin || $isOrderStaff || $isBarista)
@php($menuOpen = request()->routeIs('loai-mon.*', 'mon.*', 'gia-mon.*', 'cong-thuc.*'))
@php($stockOpen = request()->routeIs('nguyen-lieu.*', 'don-nhap.*', 'nha-cung-cap.*'))
<aside class="sidebar flex-column p-3" id="app-sidebar">
    <div class="text-white fw-semibold fs-5 px-2 py-3">Ann Coffee</div>
    <nav class="nav flex-column gap-1">
        @if($isAdmin)
            <a data-menu-key="bao-cao" class="nav-link rounded {{ request()->routeIs('bao-cao.*') ? 'active' : '' }}" href="{{ route('bao-cao.index') }}">Báo cáo thống kê</a>
            <a data-menu-key="nguoi-dung" class="nav-link rounded {{ request()->routeIs('nguoi-dung.*') ? 'active' : '' }}" href="{{ route('nguoi-dung.index') }}">Nhân sự</a>
        @endif

        @if($canViewCatalog)
            <button class="nav-link rounded sidebar-group-toggle d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu-group" aria-expanded="{{ $menuOpen ? 'true' : 'false' }}" aria-controls="sidebar-menu-group">
                <span>Quản lý Menu</span>
            </button>
            <div class="collapse {{ $menuOpen ? 'show' : '' }}" id="sidebar-menu-group">
                <div class="nav flex-column gap-1 sidebar-subnav py-1">
                    <a data-menu-key="loai-mon" class="nav-link rounded {{ request()->routeIs('loai-mon.*') ? 'active' : '' }}" href="{{ route('loai-mon.index') }}">Loại món</a>
                    <a data-menu-key="mon" class="nav-link rounded {{ request()->routeIs('mon.*') ? 'active' : '' }}" href="{{ route('mon.index') }}">Quản lý món</a>
                    @if($isAdmin)
                        <a data-menu-key="gia-mon" class="nav-link rounded {{ request()->routeIs('gia-mon.*') ? 'active' : '' }}" href="{{ route('gia-mon.index') }}">Giá bán</a>
                    @endif
                    <a data-menu-key="cong-thuc" class="nav-link rounded {{ request()->routeIs('cong-thuc.*') ? 'active' : '' }}" href="{{ route('cong-thuc.index') }}">Công thức</a>
                </div>
            </div>

            <button class="nav-link rounded sidebar-group-toggle d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-stock-group" aria-expanded="{{ $stockOpen ? 'true' : 'false' }}" aria-controls="sidebar-stock-group">
                <span>Kho nguyên liệu</span>
            </button>
            <div class="collapse {{ $stockOpen ? 'show' : '' }}" id="sidebar-stock-group">
                <div class="nav flex-column gap-1 sidebar-subnav py-1">
                    <a data-menu-key="nguyen-lieu" class="nav-link rounded {{ request()->routeIs('nguyen-lieu.*') ? 'active' : '' }}" href="{{ route('nguyen-lieu.index') }}">Nguyên liệu</a>
                    <a data-menu-key="don-nhap" class="nav-link rounded {{ request()->routeIs('don-nhap.*') ? 'active' : '' }}" href="{{ route('don-nhap.index') }}">Nhập hàng</a>
                    <a data-menu-key="nha-cung-cap" class="nav-link rounded {{ request()->routeIs('nha-cung-cap.*') ? 'active' : '' }}" href="{{ route('nha-cung-cap.index') }}">Nhà cung cấp</a>
                </div>
            </div>
        @endif

        @if($isAdmin || $isOrderStaff)
            <a data-menu-key="order" class="nav-link rounded {{ request()->routeIs('order.*') ? 'active' : '' }}" href="{{ route('order.index') }}">Bán hàng</a>
        @endif

        @if($isAdmin || $isBarista)
            <a data-menu-key="pha-che" class="nav-link rounded {{ request()->routeIs('pha-che.*') ? 'active' : '' }}" href="{{ route('pha-che.index') }}">Pha chế</a>
        @endif
    </nav>
</aside>
