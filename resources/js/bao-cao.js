(() => {
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

    const normalize = values => {
        const max = Math.max(...values, 1);
        return values.map(value => Number(value || 0) / max);
    };

    const prepareCanvas = canvas => {
        const ratio = window.devicePixelRatio || 1;
        const rect = canvas.getBoundingClientRect();
        canvas.width = Math.max(1, Math.round(rect.width * ratio));
        canvas.height = Math.max(1, Math.round(rect.height * ratio));
        const context = canvas.getContext('2d');
        context.setTransform(ratio, 0, 0, ratio, 0, 0);
        return { context, width: rect.width, height: rect.height };
    };

    const drawSparkline = (id, values, color) => {
        const canvas = document.getElementById(id);
        if (!canvas) {
            return;
        }

        const { context, width, height } = prepareCanvas(canvas);
        const points = normalize(values.length ? values : [0, 0]);
        const step = width / Math.max(points.length - 1, 1);

        context.clearRect(0, 0, width, height);
        context.beginPath();
        context.strokeStyle = color;
        context.lineWidth = 2;
        context.lineJoin = 'round';
        points.forEach((value, index) => {
            const x = index * step;
            const y = height - 3 - (value * (height - 6));
            index ? context.lineTo(x, y) : context.moveTo(x, y);
        });
        context.stroke();
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

    const revenueRows = data.daily || [];
    const values = revenueRows.map(row => Number(row.value || 0));
    drawSparkline('report-spark-revenue', values, accent);
    drawSparkline('report-spark-invoices', values.map((value, index) => value ? index + 1 : 0), '#15803d');
    drawSparkline('report-spark-pending', values.map((value, index) => (index % 2 ? value / 2 : value)), '#d97706');
    drawSparkline('report-spark-stock', values.map((value, index) => Math.max(0, value - index)), '#64748b');

    const renderSelectedRange = () => {
        drawRevenueChart(rangeSelect.value === 'monthly' ? (data.monthly || []) : (data.daily || []));
    };

    rangeSelect.addEventListener('change', renderSelectedRange);
    window.addEventListener('resize', renderSelectedRange);
    renderSelectedRange();
})();
