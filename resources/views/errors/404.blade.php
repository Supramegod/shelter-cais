{{-- resources/views/errors/404.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>404 - Halaman Tidak Ditemukan</title>
    <style>
        body {
            text-align: center;
            font-family: Arial, sans-serif;
            margin: 50px;
        }
        h1 {
            font-size: 48px;
            color: #ff6b6b;
        }
        p {
            font-size: 24px;
            color: #333;
        }
        a {
            color: #3490dc;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <h1>404 - Halaman Tidak Ditemukan</h1>
    <p>Maaf, halaman yang Anda cari tidak ditemukan.</p>
    <a href="{{ url('/') }}">Kembali ke Beranda</a>
</body>
</html>
