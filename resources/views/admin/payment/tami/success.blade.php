{{-- resources/views/payment/success.blade.php --}}
    <!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ödeme Başarılı</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
            background-color: #f0f8ff;
        }
        h1 {
            color: #28a745;
            font-size: 48px;
            margin-bottom: 20px;
        }
        p {
            font-size: 18px;
            margin-top: 10px;
        }
        .countdown {
            font-weight: bold;
            color: #4f46e5;
            font-size: 22px;
        }
    </style>
    <script>
        let seconds = 4;
        function updateCountdown() {
            document.getElementById('countdown').textContent = seconds;
            if (seconds <= 0) {
                window.location.href = '/admin/top-up-balance';
            }
            seconds--;
        }
        setInterval(updateCountdown, 1000);
        window.onload = updateCountdown;
    </script>
</head>
<body>
<h1>Ödeme Başarılı ✅</h1>
<p><span id="countdown" class="countdown">4</span> saniye içinde yönlendirileceksiniz...</p>
</body>
</html>
