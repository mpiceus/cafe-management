(() => {
    const container = document.getElementById('formula-list');
    const addButton = document.getElementById('formula-add');
    const dataNode = document.getElementById('formula-options');

    if (!container || !addButton || !dataNode) {
        return;
    }

    const options = JSON.parse(dataNode.textContent || '[]');
    let index = container.querySelectorAll('.formula-row').length;

    const normalize = value => String(value || '')
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase()
        .trim()
        .replace(/\s+/g, ' ');

    const escapeHtml = value => String(value || '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');

    const closeDropdowns = currentRow => {
        container.querySelectorAll('.ingredient-results').forEach(dropdown => {
            if (!currentRow || dropdown !== currentRow.querySelector('.ingredient-results')) {
                dropdown.classList.add('d-none');
            }
        });
    };

    const renderResults = row => {
        const dropdown = row.querySelector('.ingredient-results');
        const input = row.querySelector('.ingredient-label');
        const keyword = normalize(input.value);
        const filtered = options
            .filter(item => !keyword || normalize(item.label).includes(keyword))
            .slice(0, 8);

        dropdown.innerHTML = filtered.length
            ? filtered.map(item => `
                <button type="button" class="list-group-item list-group-item-action ingredient-option" data-id="${escapeHtml(item.id)}" data-name="${escapeHtml(item.name)}">
                    <div class="fw-semibold">${escapeHtml(item.name)}</div>
                    <div class="small text-muted">${escapeHtml(item.unit)}</div>
                </button>
            `).join('')
            : '<button type="button" class="list-group-item list-group-item-action disabled">Không tìm thấy nguyên liệu phù hợp</button>';

        dropdown.classList.remove('d-none');
    };

    const syncSelectedLabels = () => {
        container.querySelectorAll('.formula-row').forEach(row => {
            const hiddenInput = row.querySelector('.ingredient-id');
            const labelInput = row.querySelector('.ingredient-label');
            const selected = options.find(item => String(item.id) === hiddenInput.value);

            if (selected && !labelInput.value) {
                labelInput.value = selected.name;
            }
        });
    };

    addButton.addEventListener('click', function () {
        const wrapper = document.createElement('div');
        wrapper.className = 'row g-3 align-items-end formula-row';
        wrapper.innerHTML = `
            <div class="col-md-7 position-relative">
                <label class="form-label">Nguyên liệu</label>
                <input class="form-control ingredient-label" placeholder="Gõ để tìm nguyên liệu" autocomplete="off">
                <input type="hidden" class="ingredient-id" name="items[${index}][ma_nguyen_lieu]">
                <div class="ingredient-results ingredient-results-dropdown list-group position-absolute start-0 end-0 mt-1 d-none"></div>
            </div>
            <div class="col-md-4">
                <label class="form-label">Khối lượng hiện tại</label>
                <input class="form-control" type="number" step="0.01" min="0" name="items[${index}][so_luong]">
            </div>
            <div class="col-md-1"><button class="btn btn-outline-danger w-100 remove-row" type="button">-</button></div>
        `;
        container.appendChild(wrapper);
        index += 1;
    });

    container.addEventListener('focusin', event => {
        const row = event.target.closest('.formula-row');
        if (row && event.target.classList.contains('ingredient-label')) {
            renderResults(row);
        }
    });

    container.addEventListener('input', event => {
        const row = event.target.closest('.formula-row');
        if (!row || !event.target.classList.contains('ingredient-label')) {
            return;
        }

        row.querySelector('.ingredient-id').value = '';
        renderResults(row);
    });

    container.addEventListener('click', function (event) {
        const option = event.target.closest('.ingredient-option');
        if (option) {
            const row = option.closest('.formula-row');
            row.querySelector('.ingredient-id').value = option.dataset.id;
            row.querySelector('.ingredient-label').value = option.dataset.name;
            row.querySelector('.ingredient-results').classList.add('d-none');
            return;
        }

        if (event.target.classList.contains('remove-row')) {
            event.target.closest('.formula-row').remove();
        }
    });

    document.addEventListener('click', event => {
        if (!event.target.closest('.formula-row')) {
            closeDropdowns();
        }
    });

    syncSelectedLabels();
})();
