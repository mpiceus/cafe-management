(() => {
    const nguyenLieus = JSON.parse(document.getElementById('purchase-ingredient-data').textContent || '[]');
    const initialRows = JSON.parse(document.getElementById('purchase-initial-rows').textContent || '[]');
    const supplierSelect = document.getElementById('supplier-select');
    const list = document.getElementById('purchase-list');
    const addButton = document.getElementById('purchase-add');
    const totalNode = document.getElementById('purchase-total');
    const draftKey = 'draft:don-nhap-create';
    let rowIndex = 0;

    const normalize = value => (value || '')
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase()
        .trim()
        .replace(/\s+/g, ' ');

    const formatMoney = value => new Intl.NumberFormat('vi-VN', { maximumFractionDigits: 0 }).format(value || 0) + ' đ';
    const formatDecimal = value => new Intl.NumberFormat('vi-VN', { minimumFractionDigits: 0, maximumFractionDigits: 2 }).format(value || 0);
    const getSupplierItems = () => nguyenLieus.filter(item => String(item.supplier_id) === String(supplierSelect.value));
    const getAllowedUnits = baseUnit => baseUnit === 'g' ? ['g', 'kg'] : ['ml', 'l'];
    const getPriceUnitLabel = unit => (unit === 'kg' || unit === 'l') ? unit : `100${unit}`;
    const calculateLineTotal = (quantity, unit, price) => (unit === 'kg' || unit === 'l')
        ? quantity * price
        : (quantity / 100) * price;

    const readDraftRows = () => {
        if (initialRows.length) {
            return initialRows;
        }

        const raw = localStorage.getItem(draftKey);
        if (!raw) {
            return [];
        }

        try {
            const payload = JSON.parse(raw);
            const rows = [];

            Object.keys(payload).forEach(key => {
                const matched = key.match(/^items\[(\d+)\]\[(.+)\]$/);
                if (!matched) {
                    return;
                }

                const index = Number(matched[1]);
                const field = matched[2];
                rows[index] = rows[index] || {};
                rows[index][field] = payload[key];
            });

            return rows.filter(Boolean);
        } catch (error) {
            return [];
        }
    };

    const refreshTotal = () => {
        let total = 0;
        list.querySelectorAll('.line-total-hidden').forEach(input => {
            total += Number(input.value || 0);
        });
        totalNode.textContent = formatMoney(total);
    };

    const closeDropdowns = currentRow => {
        list.querySelectorAll('.ingredient-results').forEach(dropdown => {
            if (!currentRow || dropdown !== currentRow.querySelector('.ingredient-results')) {
                dropdown.classList.add('d-none');
            }
        });
    };

    const renderResults = row => {
        const dropdown = row.querySelector('.ingredient-results');
        const input = row.querySelector('.ingredient-label');
        const items = getSupplierItems();
        const keyword = normalize(input.value);
        const filtered = items.filter(item => !keyword || item.normalized_name.includes(keyword)).slice(0, 8);

        dropdown.innerHTML = filtered.length
            ? filtered.map(item => `
                <button type="button" class="list-group-item list-group-item-action ingredient-option" data-id="${item.id}">
                    <div class="fw-semibold">${item.name}</div>
                    <div class="small text-muted">${item.unit} · tỉ lệ ${item.ratio}</div>
                </button>
            `).join('')
            : '<button type="button" class="list-group-item list-group-item-action disabled">Không tìm thấy nguyên liệu phù hợp</button>';

        dropdown.classList.remove('d-none');
    };

    const syncSelectedIngredient = row => {
        const hiddenInput = row.querySelector('.ingredient-id');
        const labelInput = row.querySelector('.ingredient-label');
        const selected = getSupplierItems().find(item => String(item.id) === hiddenInput.value);

        if (!selected) {
            hiddenInput.value = '';
            row.querySelector('.ratio-display').value = '';
            row.querySelector('.stock-qty').value = '';
            row.querySelector('.line-total').value = '';
            row.querySelector('.line-total-hidden').value = '';
            row.querySelector('.purchase-unit').innerHTML = '<option value="">Chọn</option>';
            row.querySelector('.price-label').textContent = 'Đơn giá';
            refreshTotal();
            return null;
        }

        labelInput.value = selected.name;

        const unitSelect = row.querySelector('.purchase-unit');
        const units = getAllowedUnits(selected.unit);
        const currentUnit = units.includes(unitSelect.value) ? unitSelect.value : (unitSelect.dataset.requestedValue || units[0]);
        unitSelect.innerHTML = units.map(unit => `<option value="${unit}">${unit}</option>`).join('');
        unitSelect.value = units.includes(currentUnit) ? currentUnit : units[0];
        unitSelect.dataset.requestedValue = '';
        row.querySelector('.ratio-display').value = selected.ratio;
        row.querySelector('.price-label').textContent = `Đơn giá/${getPriceUnitLabel(unitSelect.value)}`;

        return selected;
    };

    const updateRow = row => {
        const selected = syncSelectedIngredient(row);
        const soLuongMua = Number(row.querySelector('.purchase-qty').value || 0);
        const donGia = Number(row.querySelector('.purchase-price').value || 0);
        const donViMua = row.querySelector('.purchase-unit').value;
        const ratio = Number(row.querySelector('.ratio-display').value || 0);
        const heSo = (donViMua === 'kg' || donViMua === 'l') ? 1000 : 1;
        const soLuongCoBan = soLuongMua * heSo;
        const soLuongNhapKho = soLuongCoBan * ratio;
        const thanhTien = calculateLineTotal(soLuongMua, donViMua, donGia);

        if (selected) {
            row.querySelector('.price-label').textContent = `Đơn giá/${getPriceUnitLabel(donViMua)}`;
        }

        row.querySelector('.stock-qty').value = selected && soLuongMua > 0 ? formatDecimal(soLuongNhapKho) : '';
        row.querySelector('.line-total').value = selected && soLuongMua > 0 ? formatMoney(thanhTien) : '';
        row.querySelector('.line-total-hidden').value = selected ? thanhTien.toFixed(2) : '';
        refreshTotal();
    };

    const addRow = values => {
        const wrapper = document.createElement('div');
        wrapper.className = 'card border-0 bg-light-subtle';
        wrapper.innerHTML = `
            <div class="card-body">
                <div class="row g-3 align-items-end purchase-row">
                    <div class="col-lg-4 position-relative">
                        <label class="form-label">Nguyên liệu</label>
                        <input class="form-control ingredient-label" placeholder="Gõ để tìm nguyên liệu" autocomplete="off">
                        <input type="hidden" class="ingredient-id" name="items[${rowIndex}][ma_nguyen_lieu]">
                        <div class="ingredient-results ingredient-results-dropdown list-group position-absolute start-0 end-0 mt-1 d-none"></div>
                    </div>
                    <div class="col-lg-2">
                        <label class="form-label">Đơn vị mua</label>
                        <select class="form-select purchase-unit" name="items[${rowIndex}][don_vi_mua]"><option value="">Chọn</option></select>
                    </div>
                    <div class="col-lg-2">
                        <label class="form-label">Số lượng mua</label>
                        <input class="form-control purchase-qty" type="number" step="0.01" min="0.01" name="items[${rowIndex}][so_luong_mua]">
                    </div>
                    <div class="col-lg-2">
                        <label class="form-label">Tỉ lệ sử dụng</label>
                        <input class="form-control ratio-display" readonly>
                    </div>
                    <div class="col-lg-2">
                        <label class="form-label">Số lượng nhập kho</label>
                        <input class="form-control stock-qty" readonly>
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label price-label">Đơn giá</label>
                        <input class="form-control purchase-price" type="number" step="100" min="0" name="items[${rowIndex}][don_gia]">
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label">Thành tiền</label>
                        <input class="form-control line-total" readonly>
                        <input type="hidden" class="line-total-hidden">
                    </div>
                    <div class="col-lg-6 text-end">
                        <button class="btn btn-outline-danger btn-sm remove-row" type="button">Xóa dòng</button>
                    </div>
                </div>
            </div>
        `;

        list.appendChild(wrapper);
        const row = wrapper.querySelector('.purchase-row');

        if (values) {
            row.querySelector('.ingredient-id').value = values.ma_nguyen_lieu || '';
            row.querySelector('.purchase-qty').value = values.so_luong_mua || '';
            row.querySelector('.purchase-price').value = values.don_gia || '';
            row.querySelector('.purchase-unit').dataset.requestedValue = values.don_vi_mua || '';
        }

        rowIndex += 1;
        updateRow(row);
    };

    supplierSelect.addEventListener('change', () => {
        list.querySelectorAll('.purchase-row').forEach(row => {
            if (!getSupplierItems().some(item => String(item.id) === row.querySelector('.ingredient-id').value)) {
                row.querySelector('.ingredient-id').value = '';
                row.querySelector('.ingredient-label').value = '';
            }
            updateRow(row);
        });
        closeDropdowns();
    });

    list.addEventListener('focusin', event => {
        const row = event.target.closest('.purchase-row');
        if (row && event.target.classList.contains('ingredient-label')) {
            renderResults(row);
        }
    });

    list.addEventListener('input', event => {
        const row = event.target.closest('.purchase-row');
        if (!row) {
            return;
        }

        if (event.target.classList.contains('ingredient-label')) {
            row.querySelector('.ingredient-id').value = '';
            renderResults(row);
        }

        updateRow(row);
    });

    list.addEventListener('change', event => {
        const row = event.target.closest('.purchase-row');
        if (row) {
            updateRow(row);
        }
    });

    list.addEventListener('click', event => {
        const option = event.target.closest('.ingredient-option');
        if (option) {
            const row = option.closest('.purchase-row');
            row.querySelector('.ingredient-id').value = option.dataset.id;
            row.querySelector('.ingredient-results').classList.add('d-none');
            updateRow(row);
            return;
        }

        if (event.target.classList.contains('remove-row')) {
            event.target.closest('.card').remove();
            refreshTotal();
        }
    });

    document.addEventListener('click', event => {
        if (!event.target.closest('.purchase-row')) {
            closeDropdowns();
        }
    });

    addButton.addEventListener('click', () => addRow());

    const rows = readDraftRows();
    if (rows.length) {
        rows.forEach(addRow);
    } else {
        addRow();
    }
})();
