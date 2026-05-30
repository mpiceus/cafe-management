<nav class="navbar navbar-expand bg-white border-bottom px-4">
    <div class="container-fluid px-0">
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-outline-secondary btn-sm" type="button" id="sidebar-toggle" aria-label="An hien thanh ben">
                <span class="navbar-toggler-icon"></span>
            </button>
            <span class="navbar-brand mb-0 h1">Cafe Management</span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="text-muted small">{{ auth()->user()->ho_ten }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-outline-secondary btn-sm" type="submit">Đăng xuất</button>
            </form>
        </div>
    </div>
</nav>
