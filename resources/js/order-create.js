document.addEventListener('DOMContentLoaded', function () {
    var STORAGE_KEY = 'order-draft-v6';
    var menuGrid = document.getElementById('menu-grid');
    var orderLines = document.getElementById('order-lines');
    var hiddenInputs = document.getElementById('hidden-inputs');
    var orderEmpty = document.getElementById('order-empty');
    var totalNode = document.getElementById('order-total');
    var form = document.getElementById('order-form');
    var submitButton = document.getElementById('order-submit');
    var message = document.getElementById('order-message');
    var searchInput = document.getElementById('menu-search');
    var categoryInput = document.getElementById('menu-category');
    var cashSection = document.getElementById('cash-section');
    var cashReceivedInput = document.getElementById('cash-received');
    var cashChangeNode = document.getElementById('cash-change');
    var paymentInputs = document.querySelectorAll('input[name="phuong_thuc_thanh_toan"]');
    var mons = JSON.parse(document.getElementById('order-menu-data').textContent || '[]');
    var cart = loadCart();
    var lastTotal = 0;

    function byId(items) {
        var map = {};
        items.forEach(function (item) {
            map[Number(item.id)] = item;
        });
        return map;
    }

    var monMap = byId(mons);
    var ingredientMeta = {};
    mons.forEach(function (mon) {
        (mon.recipe || []).forEach(function (row) {
            ingredientMeta[Number(row.ingredient_id)] = row;
        });
    });

    function normalize(value) {
        return String(value || '')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .replace(/\s+/g, ' ')
            .trim();
    }

    function formatMoney(value) {
        return new Intl.NumberFormat('vi-VN').format(Number(value || 0)) + ' đ';
    }


    function getPaymentMethod() {
        return document.querySelector('input[name="phuong_thuc_thanh_toan"]:checked')?.value || 'tien_mat';
    }

    function updateCashChange() {
        if (!cashSection || !cashReceivedInput || !cashChangeNode) {
            return;
        }
        var received = Number(cashReceivedInput.value || 0);
        var change = Math.max(0, received - lastTotal);
        cashChangeNode.textContent = formatMoney(change);
    }

    function updateCashVisibility() {
        if (!cashSection || !cashReceivedInput || !cashChangeNode) {
            return;
        }
        var isCash = getPaymentMethod() === 'tien_mat';
        cashSection.classList.toggle('d-none', !isCash);
        if (!isCash) {
            cashReceivedInput.value = '';
            cashChangeNode.textContent = formatMoney(0);
            return;
        }
        updateCashChange();
    }
    function makeKey() {
        return Math.random().toString(36).slice(2, 10);
    }

    function clone(value) {
        return JSON.parse(JSON.stringify(value));
    }

    function loadCart() {
        try {
            var raw = localStorage.getItem(STORAGE_KEY);
            var parsed = raw ? JSON.parse(raw) : [];
            return Array.isArray(parsed) ? parsed : [];
        } catch (error) {
            return [];
        }
    }

    function saveCart() {
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(cart));
        } catch (error) {
            return null;
        }
    }

    function clearCartDraft() {
        try {
            localStorage.removeItem(STORAGE_KEY);
        } catch (error) {
            return null;
        }
    }

    function showMessage(text, type) {
        if (!text) {
            message.className = 'alert d-none mb-3 flex-shrink-0';
            message.textContent = '';
            return;
        }

        message.className = 'alert alert-' + (type || 'danger') + ' mb-3 flex-shrink-0';
        message.textContent = text;
    }

    function emptyNode(node) {
        while (node.firstChild) {
            node.removeChild(node.firstChild);
        }
    }

    function usageFor(draftCart) {
        var usage = {};
        draftCart.forEach(function (line) {
            var mon = monMap[Number(line.ma_mon)];
            if (!mon) {
                return;
            }

            (mon.recipe || []).forEach(function (recipe) {
                var ingredientId = Number(recipe.ingredient_id);
                var percent = Number((line.tuy_chinh || {})[ingredientId] || 100) / 100;
                usage[ingredientId] = (usage[ingredientId] || 0) + Number(recipe.amount || 0) * Number(line.so_luong || 0) * percent;
            });

        });
        return usage;
    }

    function shortagesFor(draftCart) {
        var usage = usageFor(draftCart);
        var shortages = [];
        Object.keys(usage).forEach(function (ingredientId) {
            var meta = ingredientMeta[Number(ingredientId)];
            if (!meta) {
                return;
            }

            if (Number(usage[ingredientId]) > Number(meta.stock || 0) + 0.0001) {
                shortages.push(meta.ingredient_name || ('NL #' + ingredientId));
            }
        });
        return shortages;
    }

    function shortageMessage(shortages) {
        return shortages.length ? 'Không đủ nguyên liệu: ' + shortages.join(', ') + '.' : '';
    }

    function defaultCustom(mon) {
        var custom = {};
        (mon.recipe || []).forEach(function (recipe) {
            if (recipe.customizable) {
                custom[Number(recipe.ingredient_id)] = 100;
            }
        });
        return custom;
    }

    function defaultMode(mon) {
        if (mon.service_mode === 'chi_nong') {
            return 'nong';
        }
        if (mon.service_mode === 'chi_lanh') {
            return 'lanh';
        }
        if (mon.service_mode === 'ca_hai') {
            return 'nong';
        }
        return '';
    }

    function newLine(mon) {
        return {
            key: makeKey(),
            ma_mon: Number(mon.id),
            so_luong: 1,
            che_do: defaultMode(mon),
            ghi_chu: '',
            tuy_chinh: defaultCustom(mon),
        };
    }

    function lineSignature(line) {
        var tuyChinh = {};
        Object.keys(line.tuy_chinh || {}).sort().forEach(function (ingredientId) {
            tuyChinh[ingredientId] = Number(line.tuy_chinh[ingredientId]);
        });

        return JSON.stringify({
            ma_mon: Number(line.ma_mon),
            che_do: line.che_do || '',
            ghi_chu: String(line.ghi_chu || '').trim(),
            tuy_chinh: tuyChinh,
        });
    }

    function addOrIncreaseLine(draftCart, line) {
        var signature = lineSignature(line);
        var existing = draftCart.find(function (cartLine) {
            return lineSignature(cartLine) === signature;
        });

        if (existing) {
            existing.so_luong = Number(existing.so_luong || 0) + Number(line.so_luong || 1);
            return;
        }

        draftCart.push(line);
    }

    function checkAddMon(monId) {
        var mon = monMap[Number(monId)];
        if (!mon) {
            return { ok: false, message: 'Không tìm thấy món.' };
        }

        var draft = clone(cart);
        addOrIncreaseLine(draft, newLine(mon));
        var shortages = shortagesFor(draft);
        return { ok: !shortages.length, message: shortageMessage(shortages) };
    }

    function checkTopping(lineIndex, toppingId, quantity, toppingIndex) {
        var draft = clone(cart);
        var line = draft[lineIndex];
        if (!line) {
            return { ok: false, message: 'Không tìm thấy dòng món.' };
        }

        line.toppings = Array.isArray(line.toppings) ? line.toppings : [];
        var row = { ma_mon: Number(toppingId), so_luong: Number(quantity || 1) };
        if (typeof toppingIndex === 'number') {
            line.toppings[toppingIndex] = row;
        } else {
            line.toppings.push(row);
        }

        var shortages = shortagesFor(draft);
        return { ok: !shortages.length, message: shortageMessage(shortages) };
    }

    function lineTotal(line) {
        var mon = monMap[Number(line.ma_mon)];
        return Number(mon && mon.price ? mon.price : 0) * Number(line.so_luong || 0);
    }

    function createText(tag, className, text) {
        var node = document.createElement(tag);
        if (className) {
            node.className = className;
        }
        node.textContent = text;
        return node;
    }

    function renderMenu() {
        var keyword = normalize(searchInput.value);
        var category = String(categoryInput.value || '');
        emptyNode(menuGrid);

        var filtered = mons.filter(function (mon) {
            var sameCategory = !category || String(mon.category_id) === category;
            var sameKeyword = !keyword || normalize(mon.name).indexOf(keyword) !== -1;
            return sameCategory && sameKeyword;
        });

        if (!filtered.length) {
            var emptyCol = document.createElement('div');
            emptyCol.className = 'col-12 text-muted text-center py-4';
            emptyCol.textContent = 'Không có món phù hợp.';
            menuGrid.appendChild(emptyCol);
            return;
        }

        filtered.forEach(function (mon) {
            var availability = checkAddMon(mon.id);
            var col = document.createElement('div');
            var button = document.createElement('button');
            var body = document.createElement('div');

            col.className = 'col-xxl-3 col-lg-4 col-md-6';
            button.type = 'button';
            button.className = 'menu-item-button card widget-card h-100 w-100 text-start' + (availability.ok ? '' : ' is-warning is-disabled');
            button.disabled = !availability.ok;
            button.dataset.monId = mon.id;

            if (mon.image) {
                var image = document.createElement('img');
                image.className = 'card-img-top';
                image.style.height = '160px';
                image.style.objectFit = 'cover';
                image.alt = mon.name;
                image.src = mon.image;
                button.appendChild(image);
            } else {
                button.appendChild(createText('div', 'menu-placeholder', 'Cafe'));
            }

            body.className = 'card-body';
            body.appendChild(createText('div', 'fw-semibold mb-1', mon.name));
            body.appendChild(createText('div', 'text-muted small mb-2', mon.category_name || ''));
            body.appendChild(createText('div', 'fw-semibold', formatMoney(mon.price)));
            if (!availability.ok) {
                body.appendChild(createText('span', 'badge text-bg-warning mt-2', 'Tạm hết'));
                body.appendChild(createText('div', 'small text-warning mt-2', availability.message || 'Tạm hết'));
            } else {
                body.appendChild(createText('span', 'badge text-bg-success mt-2', 'Có thể phục vụ'));
            }

            button.appendChild(body);
            col.appendChild(button);
            menuGrid.appendChild(col);
        });
    }

    function renderHiddenInputs() {
        emptyNode(hiddenInputs);
        cart.forEach(function (line, index) {
            addHidden('items[' + index + '][ma_mon]', line.ma_mon);
            addHidden('items[' + index + '][so_luong]', line.so_luong);
            addHidden('items[' + index + '][che_do]', line.che_do || '');
            addHidden('items[' + index + '][ghi_chu]', line.ghi_chu || '');

            Object.keys(line.tuy_chinh || {}).forEach(function (ingredientId) {
                addHidden('items[' + index + '][tuy_chinh][' + ingredientId + ']', line.tuy_chinh[ingredientId]);
            });

        });
    }

    function addHidden(name, value) {
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        hiddenInputs.appendChild(input);
    }

    function renderCart() {
        emptyNode(orderLines);
        orderEmpty.classList.toggle('d-none', cart.length > 0);
        if (submitButton) {
            submitButton.disabled = cart.length === 0;
        }

        var total = 0;
        cart.forEach(function (line, index) {
            var mon = monMap[Number(line.ma_mon)];
            if (!mon) {
                return;
            }

            total += lineTotal(line);
            orderLines.appendChild(renderCartLine(line, mon, index));
        });

        lastTotal = total;
        totalNode.textContent = formatMoney(total);
        updateCashChange();
        renderHiddenInputs();
        saveCart();
    }

    function renderCartLine(line, mon, index) {
        var card = document.createElement('div');
        var body = document.createElement('div');
        card.className = 'card border-0 bg-light-subtle';
        body.className = 'card-body';

        var header = document.createElement('div');
        header.className = 'd-flex justify-content-between align-items-start gap-3';
        var titleBox = document.createElement('div');
        titleBox.appendChild(createText('div', 'fw-semibold', mon.name));
        titleBox.appendChild(createText('div', 'small text-muted', formatMoney(mon.price)));
        var remove = document.createElement('button');
        remove.type = 'button';
        remove.className = 'btn btn-outline-danger btn-sm';
        remove.dataset.action = 'remove-line';
        remove.dataset.index = index;
        remove.textContent = 'Xóa';
        header.appendChild(titleBox);
        header.appendChild(remove);
        body.appendChild(header);

        var row = document.createElement('div');
        row.className = 'row g-3 mt-1';
        row.appendChild(quantityControl(line, index));
        row.appendChild(modeControl(line, mon, index));
        var moneyCol = document.createElement('div');
        moneyCol.className = 'col-12 col-md-4';
        moneyCol.appendChild(createText('label', 'form-label small', 'Thành tiền'));
        moneyCol.appendChild(createText('div', 'form-control form-control-sm bg-light', formatMoney(lineTotal(line))));
        row.appendChild(moneyCol);
        body.appendChild(row);

        var noteWrap = document.createElement('div');
        noteWrap.className = 'mt-3';
        var note = document.createElement('input');
        note.className = 'form-control form-control-sm';
        note.dataset.action = 'note';
        note.dataset.index = index;
        note.value = line.ghi_chu || '';
        noteWrap.appendChild(createText('label', 'form-label small', 'Ghi chú'));
        noteWrap.appendChild(note);
        body.appendChild(noteWrap);

        var custom = customControl(line, mon, index);
        if (custom) {
            body.appendChild(custom);
        }

        card.appendChild(body);
        return card;
    }

    function quantityControl(line, index) {
        var col = document.createElement('div');
        var group = document.createElement('div');
        var dec = document.createElement('button');
        var input = document.createElement('input');
        var inc = document.createElement('button');

        col.className = 'col-12 col-md-4';
        group.className = 'input-group input-group-sm';
        dec.type = 'button';
        dec.className = 'btn btn-outline-secondary';
        dec.dataset.action = 'dec';
        dec.dataset.index = index;
        dec.textContent = '-';
        input.type = 'number';
        input.min = '1';
        input.step = '1';
        input.className = 'form-control text-center bg-white';
        input.dataset.action = 'line-qty';
        input.dataset.index = index;
        input.value = line.so_luong;
        inc.type = 'button';
        inc.className = 'btn btn-outline-secondary';
        inc.dataset.action = 'inc';
        inc.dataset.index = index;
        inc.textContent = '+';

        group.appendChild(dec);
        group.appendChild(input);
        group.appendChild(inc);
        col.appendChild(createText('label', 'form-label small', 'Số lượng'));
        col.appendChild(group);
        return col;
    }

    function modeControl(line, mon, index) {
        var col = document.createElement('div');
        col.className = 'col-12 col-md-4';
        col.appendChild(createText('label', 'form-label small', 'Chế độ'));

        if (mon.service_mode !== 'ca_hai') {
            var text = 'Không áp dụng';
            if (mon.service_mode === 'chi_nong') {
                text = 'Nóng';
            }
            if (mon.service_mode === 'chi_lanh') {
                text = 'Lạnh';
            }
            col.appendChild(createText('div', 'form-control form-control-sm bg-light', text));
            return col;
        }

        var group = document.createElement('div');
        group.className = 'order-choice-row';
        ['nong', 'lanh'].forEach(function (mode) {
            var label = document.createElement('label');
            var input = document.createElement('input');
            label.className = 'order-choice-pill';
            input.className = 'form-check-input';
            input.type = 'radio';
            input.name = 'mode-' + line.key;
            input.value = mode;
            input.dataset.action = 'mode';
            input.dataset.index = index;
            input.checked = line.che_do === mode;
            label.appendChild(input);
            label.appendChild(document.createTextNode(mode === 'nong' ? 'Nóng' : 'Lạnh'));
            group.appendChild(label);
        });
        col.appendChild(group);
        return col;
    }

    function customControl(line, mon, index) {
        var recipes = (mon.recipe || []).filter(function (recipe) {
            return recipe.customizable;
        });
        if (!recipes.length) {
            return null;
        }

        var wrap = document.createElement('div');
        wrap.className = 'mt-3 d-flex flex-column gap-3';
        recipes.forEach(function (recipe) {
            var block = document.createElement('div');
            var group = document.createElement('div');
            block.className = 'd-flex flex-column gap-2';
            group.className = 'order-choice-row';
            block.appendChild(createText('span', 'small text-muted', recipe.ingredient_name));
            [0, 25, 50, 75, 100].forEach(function (percent) {
                var label = document.createElement('label');
                var input = document.createElement('input');
                label.className = 'order-choice-pill';
                input.className = 'form-check-input';
                input.type = 'radio';
                input.name = 'custom-' + line.key + '-' + recipe.ingredient_id;
                input.value = percent;
                input.dataset.action = 'custom';
                input.dataset.index = index;
                input.dataset.ingredientId = recipe.ingredient_id;
                input.checked = Number((line.tuy_chinh || {})[Number(recipe.ingredient_id)] || 100) === percent;
                label.appendChild(input);
                label.appendChild(document.createTextNode(percent + '%'));
                group.appendChild(label);
            });
            block.appendChild(group);
            wrap.appendChild(block);
        });
        return wrap;
    }

    function toppingControl(line, index) {
        var wrap = document.createElement('div');
        var header = document.createElement('div');
        var addButton = document.createElement('button');
        var available = toppings.filter(function (topping) {
            return checkTopping(index, topping.id, 1).ok;
        });

        wrap.className = 'mt-3';
        header.className = 'd-flex justify-content-between align-items-center mb-2';
        header.appendChild(createText('label', 'form-label small mb-0', 'Topping'));
        addButton.type = 'button';
        addButton.className = 'btn btn-link btn-sm px-0';
        addButton.dataset.action = 'add-topping';
        addButton.dataset.index = index;
        addButton.disabled = !available.length;
        addButton.textContent = '+ Thêm topping';
        header.appendChild(addButton);
        wrap.appendChild(header);

        if (!available.length) {
            wrap.appendChild(createText('div', 'small text-warning mb-2', 'Hiện không có topping nào đủ nguyên liệu để thêm.'));
        }

        var list = document.createElement('div');
        list.className = 'd-flex flex-column gap-2';
        (line.toppings || []).forEach(function (item, toppingIndex) {
            list.appendChild(toppingRow(line, index, item, toppingIndex));
        });
        wrap.appendChild(list);
        return wrap;
    }

    function toppingRow(line, lineIndex, item, toppingIndex) {
        var row = document.createElement('div');
        var selectCol = document.createElement('div');
        var qtyCol = document.createElement('div');
        var removeCol = document.createElement('div');
        var select = document.createElement('select');
        var qty = document.createElement('input');
        var remove = document.createElement('button');
        var state = checkTopping(lineIndex, item.ma_mon, item.so_luong, toppingIndex);

        row.className = 'row g-2 align-items-center';
        selectCol.className = 'col-12 col-md-6';
        qtyCol.className = 'col-6 col-md-3';
        removeCol.className = 'col-6 col-md-3 text-end';
        select.className = 'form-select form-select-sm' + (state.ok ? '' : ' is-invalid');
        select.dataset.action = 'topping-select';
        select.dataset.index = lineIndex;
        select.dataset.toppingIndex = toppingIndex;

        toppings.forEach(function (topping) {
            var option = document.createElement('option');
            var optionState = checkTopping(lineIndex, topping.id, item.so_luong, toppingIndex);
            option.value = topping.id;
            option.textContent = topping.name + (optionState.ok ? '' : ' - hết nguyên liệu');
            option.selected = Number(topping.id) === Number(item.ma_mon);
            option.disabled = !optionState.ok;
            select.appendChild(option);
        });

        qty.type = 'number';
        qty.min = '1';
        qty.className = 'form-control form-control-sm';
        qty.dataset.action = 'topping-qty';
        qty.dataset.index = lineIndex;
        qty.dataset.toppingIndex = toppingIndex;
        qty.value = item.so_luong;

        remove.type = 'button';
        remove.className = 'btn btn-outline-danger btn-sm';
        remove.dataset.action = 'remove-topping';
        remove.dataset.index = lineIndex;
        remove.dataset.toppingIndex = toppingIndex;
        remove.textContent = 'Xóa';

        selectCol.appendChild(select);
        qtyCol.appendChild(qty);
        removeCol.appendChild(remove);
        row.appendChild(selectCol);
        row.appendChild(qtyCol);
        row.appendChild(removeCol);
        if (!state.ok) {
            var warningCol = document.createElement('div');
            warningCol.className = 'col-12 small text-danger';
            warningCol.textContent = state.message;
            row.appendChild(warningCol);
        }
        return row;
    }

    function addMenuItem(monId) {
        var check = checkAddMon(monId);
        if (!check.ok) {
            showMessage(check.message, 'danger');
            return;
        }

        var mon = monMap[Number(monId)];
        addOrIncreaseLine(cart, newLine(mon));
        showMessage('');
        renderAll();
    }

    function renderAll() {
        var menuTop = menuGrid.scrollTop;
        var orderTop = orderLines.scrollTop;
        renderMenu();
        renderCart();
        requestAnimationFrame(function () {
            menuGrid.scrollTop = menuTop;
            orderLines.scrollTop = orderTop;
        });
    }

    menuGrid.addEventListener('click', function (event) {
        var button = event.target.closest('[data-mon-id]');
        if (button && !button.disabled) {
            addMenuItem(button.dataset.monId);
        }
    });

    searchInput.addEventListener('input', renderMenu);
    categoryInput.addEventListener('change', renderMenu);

    orderLines.addEventListener('click', function (event) {
        var action = event.target.dataset.action;
        var index = Number(event.target.dataset.index);
        if (!action || Number.isNaN(index)) {
            return;
        }

        if (action === 'remove-line') {
            cart.splice(index, 1);
            showMessage('');
            renderAll();
            return;
        }

        if (action === 'dec' && cart[index].so_luong > 1) {
            cart[index].so_luong -= 1;
            showMessage('');
            renderAll();
            return;
        }

        if (action === 'inc') {
            var draft = clone(cart);
            draft[index].so_luong += 1;
            var shortages = shortagesFor(draft);
            if (shortages.length) {
                showMessage(shortageMessage(shortages), 'danger');
                return;
            }
            cart = draft;
            showMessage('');
            renderAll();
            return;
        }

        if (action === 'add-topping') {
            var first = toppings.find(function (topping) {
                return checkTopping(index, topping.id, 1).ok;
            });
            if (!first) {
                showMessage('Hiện không có topping nào đủ nguyên liệu để thêm.', 'danger');
                return;
            }
            cart[index].toppings = Array.isArray(cart[index].toppings) ? cart[index].toppings : [];
            cart[index].toppings.push({ ma_mon: Number(first.id), so_luong: 1 });
            showMessage('');
            renderAll();
            return;
        }

        if (action === 'remove-topping') {
            cart[index].toppings.splice(Number(event.target.dataset.toppingIndex), 1);
            showMessage('');
            renderAll();
        }
    });

    orderLines.addEventListener('change', function (event) {
        var action = event.target.dataset.action;
        var index = Number(event.target.dataset.index);
        if (!action || Number.isNaN(index)) {
            return;
        }

        if (action === 'mode') {
            cart[index].che_do = event.target.value;
            renderHiddenInputs();
            saveCart();
            return;
        }

        if (action === 'custom') {
            var next = clone(cart);
            next[index].tuy_chinh[Number(event.target.dataset.ingredientId)] = Number(event.target.value);
            var shortages = shortagesFor(next);
            if (shortages.length) {
                showMessage(shortageMessage(shortages), 'danger');
                renderAll();
                return;
            }
            cart = next;
            showMessage('');
            renderAll();
            return;
        }

        if (action === 'line-qty') {
            var qty = Math.max(1, Math.round(Number(event.target.value || 1)));
            var draft = clone(cart);
            draft[index].so_luong = qty;
            var shortages = shortagesFor(draft);
            if (shortages.length) {
                showMessage(shortageMessage(shortages), 'danger');
                renderAll();
                return;
            }
            cart = draft;
            showMessage('');
            renderAll();
            return;
        }

        if (action === 'topping-select') {
            var toppingIndex = Number(event.target.dataset.toppingIndex);
            var currentQty = Number(cart[index].toppings[toppingIndex].so_luong || 1);
            var check = checkTopping(index, event.target.value, currentQty, toppingIndex);
            if (!check.ok) {
                showMessage(check.message, 'danger');
                renderAll();
                return;
            }
            cart[index].toppings[toppingIndex].ma_mon = Number(event.target.value);
            showMessage('');
            renderAll();
            return;
        }

        if (action === 'topping-qty') {
            var qty = Math.max(1, Number(event.target.value || 1));
            var tIndex = Number(event.target.dataset.toppingIndex);
            var selected = cart[index].toppings[tIndex].ma_mon;
            var state = checkTopping(index, selected, qty, tIndex);
            if (!state.ok) {
                showMessage(state.message, 'danger');
                renderAll();
                return;
            }
            cart[index].toppings[tIndex].so_luong = qty;
            showMessage('');
            renderAll();
        }
    });

    orderLines.addEventListener('input', function (event) {
        if (event.target.dataset.action !== 'note') {
            return;
        }
        var index = Number(event.target.dataset.index);
        if (!Number.isNaN(index) && cart[index]) {
            cart[index].ghi_chu = event.target.value;
            renderHiddenInputs();
            saveCart();
        }
    });


    if (cashReceivedInput) {
        cashReceivedInput.addEventListener('input', updateCashChange);
    }

    paymentInputs.forEach(function (input) {
        input.addEventListener('change', updateCashVisibility);
    });
    form.addEventListener('submit', function (event) {
        event.preventDefault();
        if (!cart.length) {
            showMessage('Vui lòng chọn ít nhất một món trước khi thanh toán.', 'danger');
            return;
        }

        var paymentMethod = getPaymentMethod();

        // For cash payments, submit a normal form to a new tab so the PDF invoice opens there
        if (paymentMethod === 'tien_mat') {
            form.target = '_blank';
            // submit the form in the background (opens new tab) and clear the local cart
            form.submit();
            cart = [];
            clearCartDraft();
            renderAll();
            return;
        }

        // For bank transfer, keep AJAX flow to redirect to payment URL
        fetch(form.action, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: new FormData(form),
        })
            .then(function (response) {
                return response.json().then(function (data) {
                    if (!response.ok) {
                        var firstError = data.errors ? Object.values(data.errors)[0][0] : (data.message || 'Không thể thanh toán.');
                        throw new Error(firstError);
                    }
                    return data;
                });
            })
            .then(function (data) {
                if (data.payment_url) {
                    window.location.href = data.payment_url;
                    return;
                }

                showMessage(data.message, 'success');
                cart = [];
                clearCartDraft();
                renderAll();
            })
            .catch(function (error) {
                showMessage(error.message, 'danger');
            });
    });

    updateCashVisibility();
    renderAll();
});
