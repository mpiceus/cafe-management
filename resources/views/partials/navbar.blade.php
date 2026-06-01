<nav class="navbar navbar-expand top-navbar px-4">
    <div class="container-fluid px-0">
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-outline-light btn-sm p-1 d-inline-flex align-items-center justify-content-center" type="button" id="sidebar-toggle" aria-label="Ẩn hiện thanh bên">
                <span class="sidebar-toggle-icon">☰</span>
            </button>
            <span class="navbar-brand mb-0 h1">Ann Coffee</span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="navbar-user small fw-semibold">{{ auth()->user()->ho_ten }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-outline-light btn-sm" type="submit">Đăng xuất</button>
            </form>
        </div>
    </div>
</nav>
