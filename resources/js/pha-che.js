(() => {
    const grid = document.getElementById('pha-che-grid');
    const message = document.getElementById('pha-che-message');
    if (!grid || !message) {
        return;
    }

    const fetchUrl = grid.dataset.fetchUrl;
    const updateBaseUrl = grid.dataset.updateBaseUrl;
    let expanded = null;

    const formatNumber = value => new Intl.NumberFormat('vi-VN', { maximumFractionDigits: 2 }).format(value);
    const showMessage = (text, type) => {
        if (!text) {
            message.className = 'alert d-none';
            message.textContent = '';
            return;
        }

        message.className = `alert alert-${type}`;
        message.textContent = text;
    };

    const render = orders => {
        grid.innerHTML = '';

        if (!orders.length) {
            grid.innerHTML = '<div class="card page-card"><div class="card-body text-center text-muted">Không có đơn chờ pha chế.</div></div>';
            return;
        }

        orders.forEach(order => {
            const createdAt = new Date(order.thoi_gian_tao_iso || order.thoi_gian_tao);
            const diffMinutes = Math.max(0, Math.floor((Date.now() - createdAt.getTime()) / 60000));
            const isLate = diffMinutes >= 10;
            const orderCard = document.createElement('div');
            orderCard.className = 'card page-card' + (isLate ? ' border-danger' : '');
            orderCard.innerHTML = `
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <div class="fw-bold fs-5">Hóa đơn #${order.ma_hoa_don}</div>
                            <div class="text-muted small">${order.thoi_gian_tao_text || order.thoi_gian_tao}</div>
                        </div>
                        <div class="text-end">
                            <div class="small">Đã chờ: <strong>${diffMinutes} phút</strong></div>
                            ${isLate ? '<span class="badge text-bg-danger">Trễ</span>' : '<span class="badge text-bg-success">Trong giờ</span>'}
                        </div>
                    </div>
                    <div class="row g-3">
                        ${order.chi_tiets.map(item => `
                            <div class="col-lg-6">
                                <div class="card widget-card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between gap-3">
                                            <div>
                                                <div class="fw-semibold">${item.mon.ten_mon}</div>
                                                <div class="small text-muted">SL: ${item.so_luong} ${item.che_do ? '- ' + (item.che_do === 'chi_nong' || item.che_do === 'nong' ? 'Nóng' : (item.che_do === 'chi_lanh' || item.che_do === 'lanh' ? 'Lạnh' : item.che_do)) : ''}</div>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input complete-item" type="checkbox" data-id="${item.ma_chi_tiet}">
                                                <label class="form-check-label small">Đã hoàn thành</label>
                                            </div>
                                        </div>
                                        ${item.ghi_chu ? `<div class="small mt-2"><strong>Ghi chú:</strong> ${item.ghi_chu}</div>` : ''}
                                        ${(item.toppings || []).length ? `<div class="small mt-2"><strong>Topping:</strong> ${item.toppings.map(t => `${t.mon.ten_mon} x ${t.so_luong}`).join(', ')}</div>` : ''}
                                        ${(item.tuy_chinhs || []).length ? `<div class="small mt-2"><strong>Tùy chỉnh:</strong> ${item.tuy_chinhs.map(t => `${t.nguyen_lieu.ten_nguyen_lieu} ${t.ti_le}%`).join(', ')}</div>` : ''}
                                        <button type="button" class="btn btn-link btn-sm px-0 mt-2 recipe-toggle" data-id="${item.ma_chi_tiet}">Xem liều lượng</button>
                                        <div class="recipe-panel ${expanded === item.ma_chi_tiet ? '' : 'd-none'}" data-panel="${item.ma_chi_tiet}">
                                            <div class="border-top pt-2 mt-2 small d-flex flex-column gap-1">
                                                ${item.cong_thuc_thuc_te.map(ct => `<div class="d-flex justify-content-between"><span>${ct.ten}</span><span>${formatNumber(ct.so_luong)} ${ct.don_vi}</span></div>`).join('')}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
            grid.appendChild(orderCard);
        });
    };

    const fetchOrders = async () => {
        const response = await fetch(fetchUrl);
        const data = await response.json();
        render(data.orders);
    };

    grid.addEventListener('click', async event => {
        if (event.target.classList.contains('recipe-toggle')) {
            const id = Number(event.target.dataset.id);
            expanded = expanded === id ? null : id;
            fetchOrders();
            return;
        }

        if (event.target.classList.contains('complete-item')) {
            event.preventDefault();
            const id = event.target.dataset.id;

            try {
                const response = await fetch(updateBaseUrl + '/' + id, {
                    method: 'PATCH',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ trang_thai_pha_che: 'da_hoan_thanh' }),
                });
                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Không thể cập nhật pha chế.');
                }

                showMessage(data.message, 'success');
                fetchOrders();
            } catch (error) {
                showMessage(error.message, 'danger');
            }
        }
    });

    fetchOrders();
    setInterval(fetchOrders, 3000);
})();
