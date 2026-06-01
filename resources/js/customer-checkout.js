(() => {
    const checkout = document.getElementById('customer-checkout');
    const qrImage = document.getElementById('qrImage');
    const statusArea = document.getElementById('statusArea');
    const successArea = document.getElementById('successArea');

    if (!checkout || !statusArea || !successArea) {
        return;
    }

    const checkStatus = () => {
        fetch(checkout.dataset.statusUrl, { credentials: 'same-origin' })
            .then(response => response.json())
            .then(data => {
                if (data?.status !== 'da_thanh_toan') {
                    return;
                }

                qrImage?.classList.add('hidden');
                statusArea.classList.add('hidden');
                successArea.classList.remove('hidden');
                document.body.classList.add('payment-confirmed');
                clearInterval(window._paymentPoll);
            })
            .catch(() => {});
    };

    checkStatus();
    window._paymentPoll = setInterval(checkStatus, 3000);
})();
