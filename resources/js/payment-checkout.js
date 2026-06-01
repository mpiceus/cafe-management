(() => {
    const statusNode = document.getElementById('payment-status');
    const message = document.getElementById('payment-message');
    const invoiceLink = document.getElementById('invoice-link');
    const statusUrl = statusNode?.dataset.statusUrl;

    if (!statusNode || !message || !statusUrl) {
        return;
    }

    const showMessage = (text, type) => {
        if (!text) {
            message.className = 'alert d-none';
            message.textContent = '';
            return;
        }

        message.className = `alert alert-${type}`;
        message.textContent = text;
    };

    const updateStatus = async () => {
        try {
            const response = await fetch(statusUrl, {
                headers: {
                    Accept: 'application/json',
                    'ngrok-skip-browser-warning': 'true',
                },
            });
            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.message || 'Không thể kiểm tra thanh toán.');
            }

            if (data.status === 'da_thanh_toan' || data.status === 'da_hoan_thanh') {
                statusNode.textContent = 'Đã thanh toán';
                invoiceLink?.classList.remove('d-none');
                showMessage('Thanh toán đã được xác nhận.', 'success');
                return true;
            }

            statusNode.textContent = 'Chờ thanh toán';
            return false;
        } catch (error) {
            showMessage(error.message, 'danger');
            return false;
        }
    };

    let timer = setInterval(async () => {
        const paid = await updateStatus();
        if (paid) {
            clearInterval(timer);
        }
    }, 3000);

    updateStatus();
})();
