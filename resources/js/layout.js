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
