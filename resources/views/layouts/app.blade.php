<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Cafe Management')</title>
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/5.3.3/css/bootstrap.min.css') }}">
    <style>
        body { background: #f5f7fb; }
        body.auth-shell { overflow: hidden; }
        .app-shell { min-height: 100vh; height: 100vh; overflow: hidden; --sidebar-width: 272px; }
        .sidebar { width: var(--sidebar-width); min-width: var(--sidebar-width); max-width: var(--sidebar-width); background: #18212f; height: 100vh; overflow-y: auto; flex: 0 0 var(--sidebar-width); transition: width .2s ease, min-width .2s ease, max-width .2s ease, flex-basis .2s ease, padding .2s ease, transform .2s ease; }
        .sidebar a { color: #cbd5e1; text-decoration: none; }
        .sidebar a.active, .sidebar a:hover { color: #fff; background: #263244; }
        .content { min-width: 0; width: 0; flex: 1 1 auto; display: flex; flex-direction: column; overflow: hidden; }
        .content-main { flex: 1 1 auto; min-height: 0; overflow: auto; }
        .content-main > .container-fluid { max-width: 100%; }
        .page-card { border: 0; border-radius: 8px; box-shadow: 0 8px 22px rgba(15, 23, 42, .06); }
        .widget-card { border: 0; border-radius: 10px; box-shadow: 0 8px 22px rgba(15, 23, 42, .06); }
        .widget-card.is-disabled { opacity: .6; }
        .widget-card.is-warning { box-shadow: 0 0 0 2px rgba(245, 158, 11, .45), 0 8px 22px rgba(15, 23, 42, .06); }
        .draft-add { border: 1px dashed #cbd5e1; border-radius: 10px; padding: 1rem; text-align: center; color: #64748b; cursor: pointer; background: #f8fafc; }
        .sticky-panel { position: sticky; top: 1rem; }
        .sidebar-collapsed .sidebar { width: 0; min-width: 0; max-width: 0; flex-basis: 0; padding-left: 0 !important; padding-right: 0 !important; overflow: hidden; border-right: 0 !important; }
        .sidebar-collapsed .sidebar > * { opacity: 0; pointer-events: none; }
        @media (max-width: 991.98px) {
            body.auth-shell { overflow: auto; }
            .app-shell { height: auto; overflow: visible; }
            .sidebar { position: fixed; z-index: 1040; inset: 0 auto 0 0; box-shadow: 0 12px 32px rgba(15,23,42,.24); }
            .sidebar-collapsed .sidebar { transform: translateX(-100%); width: var(--sidebar-width); min-width: var(--sidebar-width); max-width: var(--sidebar-width); flex-basis: var(--sidebar-width); padding: 1rem !important; }
            .content { overflow: visible; }
            .content-main { overflow: visible; }
        }
    </style>
</head>
<body class="@auth auth-shell @endauth">
@auth
    <div class="app-shell d-flex">
        @include('partials.sidebar')
        <div class="content flex-grow-1">
            @include('partials.navbar')
            <main class="content-main">
            <div class="container-fluid py-4">
                @include('partials.flash')
                @yield('content')
            </div>
            </main>
        </div>
    </div>
@else
    @yield('content')
@endauth
<script src="{{ asset('vendor/bootstrap/5.3.3/js/bootstrap.bundle.min.js') }}"></script>
<script>
document.querySelectorAll('form[data-persist-key]').forEach(function (form) {
    const key = 'draft:' + form.dataset.persistKey;
    const clearOnSubmit = form.dataset.persistClearOnSubmit === '1';
    const getFields = function () {
        return Array.from(form.querySelectorAll('input, textarea, select'));
    };

    const saveDraft = function () {
        const payload = {};
        getFields().forEach(function (field) {
            if (!field.name || field.type === 'file') {
                return;
            }
            if (field.type === 'checkbox' || field.type === 'radio') {
                payload[field.name] = field.checked ? field.value : '';
                return;
            }
            payload[field.name] = field.value;
        });
        localStorage.setItem(key, JSON.stringify(payload));
    };

    const raw = localStorage.getItem(key);
    if (raw) {
        try {
            const payload = JSON.parse(raw);
            getFields().forEach(function (field) {
                if (!field.name || !(field.name in payload) || field.type === 'file') {
                    return;
                }
                if ((field.type === 'checkbox' || field.type === 'radio') && field.value === payload[field.name]) {
                    field.checked = true;
                    return;
                }
                if (field.type !== 'checkbox' && field.type !== 'radio' && !field.value) {
                    field.value = payload[field.name];
                }
            });
        } catch (error) {
            localStorage.removeItem(key);
        }
    }

    getFields().forEach(function (field) {
        field.addEventListener('input', saveDraft);
        field.addEventListener('change', saveDraft);
    });

    form.addEventListener('input', saveDraft);
    form.addEventListener('change', saveDraft);

    form.addEventListener('submit', function () {
        if (clearOnSubmit) {
            localStorage.removeItem(key);
        }
    });
});

const sidebarKey = 'sidebar-collapsed';
const applySidebarState = function (collapsed) {
    document.body.classList.toggle('sidebar-collapsed', collapsed);
};
applySidebarState(localStorage.getItem(sidebarKey) === '1');
document.getElementById('sidebar-toggle')?.addEventListener('click', function () {
    const collapsed = !document.body.classList.contains('sidebar-collapsed');
    applySidebarState(collapsed);
    localStorage.setItem(sidebarKey, collapsed ? '1' : '0');
});

const menuStatePrefix = 'last-menu-url:';
const activeMenuLink = document.querySelector('.sidebar a.active[data-menu-key]');
if (activeMenuLink) {
    localStorage.setItem(menuStatePrefix + activeMenuLink.dataset.menuKey, window.location.pathname + window.location.search);
}
document.querySelectorAll('.sidebar a[data-menu-key]').forEach(function (link) {
    link.addEventListener('click', function (event) {
        if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || event.button !== 0) {
            return;
        }

        const savedUrl = localStorage.getItem(menuStatePrefix + link.dataset.menuKey);
        if (savedUrl && savedUrl !== window.location.pathname + window.location.search) {
            event.preventDefault();
            window.location.href = savedUrl;
        }
    });
});
</script>
@stack('scripts')
</body>
</html>
