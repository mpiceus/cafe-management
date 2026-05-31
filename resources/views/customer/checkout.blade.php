<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Thanh toán - Hóa đơn #{{ $hoaDon->ma_hoa_don }}</title>
    <style>
        html,body{height:100%;margin:0}
        body{display:flex;align-items:center;justify-content:center;background:#f5f7fb;font-family:Arial,Helvetica,sans-serif}
        .container{width:100%;max-width:420px;text-align:center;padding:24px}
        .card{background:#fff;border-radius:12px;padding:20px;box-shadow:0 6px 24px rgba(16,24,40,.08)}
        .amount{font-size:28px;font-weight:700;margin:12px 0}
        .qr{margin:18px 0}
        .bank{font-size:14px;color:#333;margin-top:6px}
        .success{background:#e6ffed;border:1px solid #b7f0c6;color:#0b6623;padding:20px;border-radius:10px}
        img.qr-img{max-width:320px;width:80%;height:auto}
        .hidden{display:none}
    </style>
</head>
<body>
    <div class="container">
        <div id="mainCard" class="card">
            <div id="statusArea">
                <div style="font-size:16px;color:#666">Quét QR để thanh toán</div>
                <div class="amount">{{ number_format($hoaDon->tong_tien, 0, ',', '.') }} đ</div>
                <div class="qr">
                    <img class="qr-img" id="qrImage" src="{{ $qrUrl }}" alt="QR thanh toán">
                </div>
                <div class="bank">
                    <div><strong>Ngân hàng:</strong> {{ $bankName }} ({{ $bankCode }})</div>
                    <div><strong>Số tài khoản:</strong> {{ $bankAccount }}</div>
                    <div><strong>Chủ tài khoản:</strong> {{ $accountName }}</div>
                    <div style="margin-top:8px;color:#888;font-size:13px">Mã đơn: #{{ $hoaDon->ma_hoa_don }}</div>
                </div>
            </div>

            <div id="successArea" class="success hidden">
                <div style="font-size:20px;font-weight:700">Thanh toán thành công!</div>
                <div style="margin-top:8px">Mã đơn của bạn là <strong>#{{ $hoaDon->ma_hoa_don }}</strong>.</div>
                <div style="margin-top:6px">Vui lòng đợi, đồ uống của bạn đang được pha chế.</div>
            </div>
        </div>
    </div>

<script>
(function(){
    var hoaDonId = '{{ $hoaDon->ma_hoa_don }}';
    var pollUrl = '/api/payment-status/' + hoaDonId;
    var qrImage = document.getElementById('qrImage');
    var statusArea = document.getElementById('statusArea');
    var successArea = document.getElementById('successArea');

    function checkStatus(){
        fetch(pollUrl, { credentials: 'same-origin' }).then(function(res){
            return res.json();
        }).then(function(data){
            if (data && data.status === 'da_thanh_toan'){
                // show success
                if (qrImage) qrImage.style.display = 'none';
                statusArea.classList.add('hidden');
                successArea.classList.remove('hidden');
                document.body.style.background = '#e6ffed';
                // stop polling
                if (window._paymentPoll) clearInterval(window._paymentPoll);
            }
        }).catch(function(){/* ignore */});
    }

    // initial poll
    checkStatus();
    // poll every 3 seconds
    window._paymentPoll = setInterval(checkStatus, 3000);
})();
</script>
</body>
</html>
