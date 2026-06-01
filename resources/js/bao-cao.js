(() => {
    const assistantForm = document.getElementById('restock-assistant-form');
    const assistantFeedback = document.getElementById('restock-assistant-feedback');
    const selectAll = document.getElementById('restock-select-all');
    const restockItems = [...document.querySelectorAll('.restock-item')];
    const defaultAssistantFeedback = 'Chọn các nguyên liệu cùng một nhà cung cấp để tạo đơn nhập.';

    const setAssistantFeedback = (message, isError = false) => {
        if (!assistantFeedback) {
            return;
        }

        assistantFeedback.textContent = message;
        assistantFeedback.classList.toggle('text-danger', isError);
    };

    const resetAssistantFeedback = () => {
        setAssistantFeedback(defaultAssistantFeedback);
    };

    if (selectAll) {
        selectAll.addEventListener('change', () => {
            restockItems.forEach(item => {
                item.checked = selectAll.checked;
            });
            resetAssistantFeedback();
        });
    }

    restockItems.forEach(item => {
        item.addEventListener('change', resetAssistantFeedback);
    });

    if (assistantForm) {
        assistantForm.addEventListener('submit', event => {
            event.preventDefault();
            const selectedItems = restockItems.filter(item => item.checked);

            if (!selectedItems.length) {
                setAssistantFeedback('Vui lòng chọn ít nhất một nguyên liệu cần nhập.', true);
                return;
            }

            const supplierIds = [...new Set(selectedItems.map(item => item.dataset.supplierId))];
            if (supplierIds.length !== 1) {
                setAssistantFeedback('Mỗi đơn nhập chỉ thuộc một nhà cung cấp. Vui lòng chọn các nguyên liệu cùng nhà cung cấp.', true);
                return;
            }

            const params = new URLSearchParams({ ma_nha_cung_cap: supplierIds[0] });
            selectedItems.forEach((item, index) => {
                params.set(`items[${index}][ma_nguyen_lieu]`, item.dataset.ingredientId);
                params.set(`items[${index}][so_luong_mua]`, item.dataset.quantity);
                params.set(`items[${index}][don_vi_mua]`, item.dataset.unit);
            });

            window.location.href = `${assistantForm.dataset.createUrl}?${params.toString()}`;
        });
    }

    const dataNode = document.getElementById('report-chart-data');
    const mainCanvas = document.getElementById('report-revenue-chart');
    const rangeSelect = document.getElementById('report-chart-range');

    if (!dataNode || !mainCanvas || !rangeSelect) {
        return;
    }

    const data = JSON.parse(dataNode.textContent || '{}');
    const accent = '#2563eb';
    const grid = '#e5e7eb';
    const muted = '#64748b';

    document.querySelectorAll('[data-report-progress]').forEach(progress => {
        progress.style.setProperty('--report-progress', `${Number(progress.dataset.reportProgress || 0)}%`);
    });

    const prepareCanvas = canvas => {
        const ratio = window.devicePixelRatio || 1;
        const rect = canvas.getBoundingClientRect();
        canvas.width = Math.max(1, Math.round(rect.width * ratio));
        canvas.height = Math.max(1, Math.round(rect.height * ratio));
        const context = canvas.getContext('2d');
        context.setTransform(ratio, 0, 0, ratio, 0, 0);
        return { context, width: rect.width, height: rect.height };
    };

    const drawRevenueChart = rows => {
        const { context, width, height } = prepareCanvas(mainCanvas);
        context.clearRect(0, 0, width, height);

        if (!rows.length) {
            context.fillStyle = muted;
            context.font = '14px Arial';
            context.textAlign = 'center';
            context.fillText('Chưa có dữ liệu doanh thu trong khoảng thời gian này.', width / 2, height / 2);
            return;
        }

        const margin = { top: 14, right: 14, bottom: 32, left: 52 };
        const chartWidth = Math.max(1, width - margin.left - margin.right);
        const chartHeight = Math.max(1, height - margin.top - margin.bottom);
        const values = rows.map(row => Number(row.value || 0));
        const max = Math.max(...values, 1);
        const step = chartWidth / Math.max(rows.length - 1, 1);
        const point = (value, index) => ({
            x: margin.left + (index * step),
            y: margin.top + chartHeight - ((value / max) * chartHeight),
        });

        context.strokeStyle = grid;
        context.fillStyle = muted;
        context.font = '11px Arial';
        context.textAlign = 'right';
        for (let index = 0; index <= 4; index += 1) {
            const y = margin.top + (chartHeight / 4) * index;
            const label = Math.round(max - (max / 4) * index);
            context.beginPath();
            context.moveTo(margin.left, y);
            context.lineTo(width - margin.right, y);
            context.stroke();
            context.fillText(new Intl.NumberFormat('vi-VN').format(label), margin.left - 8, y + 4);
        }

        const gradient = context.createLinearGradient(0, margin.top, 0, height - margin.bottom);
        gradient.addColorStop(0, 'rgba(37, 99, 235, .18)');
        gradient.addColorStop(1, 'rgba(37, 99, 235, 0)');

        context.beginPath();
        rows.forEach((row, index) => {
            const current = point(values[index], index);
            if (!index) {
                context.moveTo(current.x, current.y);
                return;
            }
            const previous = point(values[index - 1], index - 1);
            const middle = (previous.x + current.x) / 2;
            context.bezierCurveTo(middle, previous.y, middle, current.y, current.x, current.y);
        });
        context.lineTo(margin.left + chartWidth, margin.top + chartHeight);
        context.lineTo(margin.left, margin.top + chartHeight);
        context.closePath();
        context.fillStyle = gradient;
        context.fill();

        context.beginPath();
        context.strokeStyle = accent;
        context.lineWidth = 3;
        rows.forEach((row, index) => {
            const current = point(values[index], index);
            if (!index) {
                context.moveTo(current.x, current.y);
                return;
            }
            const previous = point(values[index - 1], index - 1);
            const middle = (previous.x + current.x) / 2;
            context.bezierCurveTo(middle, previous.y, middle, current.y, current.x, current.y);
        });
        context.stroke();

        const labelStep = Math.max(1, Math.ceil(rows.length / 7));
        rows.forEach((row, index) => {
            const current = point(values[index], index);
            context.beginPath();
            context.fillStyle = '#fff';
            context.strokeStyle = accent;
            context.lineWidth = 2;
            context.arc(current.x, current.y, 3.5, 0, Math.PI * 2);
            context.fill();
            context.stroke();

            if (index % labelStep === 0 || index === rows.length - 1) {
                context.fillStyle = muted;
                context.font = '11px Arial';
                context.textAlign = 'center';
                context.fillText(row.label, current.x, height - 8);
            }
        });
    };

    const renderSelectedRange = () => {
        drawRevenueChart(rangeSelect.value === 'monthly' ? (data.monthly || []) : (data.daily || []));
    };

    rangeSelect.addEventListener('change', renderSelectedRange);
    window.addEventListener('resize', renderSelectedRange);
    renderSelectedRange();
})();
