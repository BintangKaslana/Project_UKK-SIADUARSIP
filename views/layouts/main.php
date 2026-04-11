<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Pengaduan Sarana Sekolah</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/ukk_bintang_26/public/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg" style="background-color: #4455DD;">
        <div class="container">
            <!-- Logo kiri -->
            <a class="navbar-brand d-flex align-items-center gap-2" href="/ukk_bintang_26/">
                <div style="width:45px; height:45px; background:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.55rem; color:#4455DD; font-weight:bold; text-align:center; line-height:1.3;">
                    LOGO<br>SEKOLAH
                </div>
                <div style="width:45px; height:45px; background:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.55rem; color:#4455DD; font-weight:bold; text-align:center; line-height:1.3;">
                    LOGO<br>PEMDA
                </div>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav ms-auto">
    <li class="nav-item">
        <a class="nav-link text-white" href="/ukk_bintang_26/">Beranda</a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-white" href="/ukk_bintang_26/aspirasi">Sampaikan Aspirasi</a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-white" href="/ukk_bintang_26/histori">Histori Aspirasi</a>
    </li>
</ul>
            </div>
        </div>
    </nav>

    <main>
        <?= $content ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>