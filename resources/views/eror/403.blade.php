<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak - DMS Diskominfo</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
            color: #1f2937;
        }
        .error-container {
            background: white;
            padding: 50px 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            max-width: 450px;
            border-top: 5px solid #ef4444; /* Warna merah peringatan */
        }
        .icon {
            font-size: 60px;
            margin-bottom: 10px;
        }
        .error-code {
            font-size: 80px;
            font-weight: 800;
            color: #ef4444;
            margin: 0;
            line-height: 1;
            letter-spacing: -2px;
        }
        .error-title {
            font-size: 24px;
            font-weight: 600;
            margin: 15px 0 10px;
        }
        .error-message {
            color: #6b7280;
            font-size: 15px;
            margin-bottom: 25px;
            line-height: 1.6;
        }
        .redirect-text {
            font-size: 14px;
            color: #9ca3af;
            background: #f9fafb;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .countdown-number {
            font-weight: bold;
            color: #ef4444;
            font-size: 16px;
        }
        .btn-back {
            display: inline-block;
            padding: 12px 24px;
            background-color: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: background 0.3s;
        }
        .btn-back:hover {
            background-color: #1d4ed8;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="icon">🛡️</div>
        <h1 class="error-code">403</h1>
        <h2 class="error-title">Akses Ditolak!</h2>
        <!-- <p class="error-message">
            Maaf, Anda tidak memiliki izin untuk memasuki area Administrator.<br>
            Aktivitas ini telah dicatat oleh sistem keamanan.
        </p> -->

        <div class="redirect-text">
            Mengembalikan Anda ke Ruang Kerja dalam <span id="countdown" class="countdown-number">5</span> detik...
        </div>

        <a href="{{ route('dashboard') }}" class="btn-back">Kembali Sekarang</a>
    </div>

    <script>
        // Logika Hitung Mundur (Countdown)
        let waktuSisa = 5; // Set timer 5 detik
        const elemenAngka = document.getElementById('countdown');

        const hitungMundur = setInterval(function() {
            waktuSisa--; // Kurangi 1 setiap detik
            elemenAngka.textContent = waktuSisa;

            // Jika waktu habis (0)
            if (waktuSisa <= 0) {
                clearInterval(hitungMundur); // Hentikan timer
                // Lempar kembali ke rute dashboard
                window.location.href = "{{ route('dashboard') }}"; 
            }
        }, 1000); // Dieksekusi setiap 1000 milidetik (1 detik)
    </script>
</body>
</html>