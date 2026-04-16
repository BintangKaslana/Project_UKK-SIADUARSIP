<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Pengaduan Sarana Sekolah</title>
    <link rel="stylesheet" href="/ukk_bintang_26/public/css/style.css">
</head>
<body class="bg-gray-50  h-screen">
<nav class="bg-[#4455DD] shadow-md relative z-50 sticky top-0">
    <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
        <div class="flex items-center gap-3">
        <img src="/ukk_bintang_26/public/images/erasebg-transformed.png" 
     alt="Logo SMKN 1 Kotabaru" class="w-11 h-11 rounded-full object-contain bg-white">
<img src="/ukk_bintang_26/public/images/Coat_of_arms_of_South_Kalimantan.svg" 
     alt="Logo Kalimantan Selatan" class="w-11 h-11 rounded-full object-contain bg-white">
        </div>
        <!-- Menu desktop -->
        <div class="hidden md:flex items-center gap-6 ml-auto">
    <a href="/siaduarsip/" class="nav-link nav-beranda text-white text-sm font-medium">Beranda</a>
    <a href="/siaduarsip/aspirasi" class="nav-link nav-aspirasi text-white text-sm font-medium">Sampaikan Aspirasi</a>
    <a href="/siaduarsip/histori" class="nav-link nav-histori text-white text-sm font-medium">Histori Aspirasi</a>
</div>
        <!-- Hamburger mobile -->
        <button id="menuBtn" class="md:hidden text-white focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>
    <!-- Menu mobile -->
    <div id="mobileMenu" class="hidden md:hidden bg-[#3344CC] px-4 py-3 flex flex-col gap-3">
        <a href="/ukk_bintang_26/" class="text-white hover:text-[#FFDD44] transition text-sm font-medium">Beranda</a>
        <a href="/ukk_bintang_26/aspirasi" class="text-white hover:text-[#FFDD44] transition text-sm font-medium">Sampaikan Aspirasi</a>
        <a href="/ukk_bintang_26/histori" class="text-white hover:text-[#FFDD44] transition text-sm font-medium">Histori Aspirasi</a>
    </div>
</nav>
<script>
    document.getElementById('menuBtn').addEventListener('click', function() {
        document.getElementById('mobileMenu').classList.toggle('hidden');
    });
</script>
    <main>
        <?= $content ?>
    </main>
</body>
</html>