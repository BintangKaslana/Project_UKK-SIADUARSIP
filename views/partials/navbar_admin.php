<?php
$navRole     = $_SESSION['admin_role']     ?? 'admin';
$navFullname = $_SESSION['admin_fullname'] ?? ($_SESSION['admin_username'] ?? 'Admin');
$navUsername = $_SESSION['admin_username'] ?? '';
$isHeadAdmin = ($navRole === 'head_admin');
$roleLabel   = $isHeadAdmin ? 'Head Admin' : 'Admin';
$roleBadgeColor = $isHeadAdmin ? 'bg-[#FFDD44] text-gray-800' : 'bg-white/20 text-white';
?>
<nav class="bg-[#4455DD] shadow-md">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
        <a href="/ukk_bintang_26/" class="text-white font-bold text-lg">📋 Sistem Pengaduan</a>
        <div class="flex items-center gap-3">
            <a href="<?= BASE_PATH ?>/" class="text-white hover:text-[#FFDD44] transition text-sm">Halaman Utama</a>
            <a href="<?= BASE_PATH ?>/admin" class="text-white hover:text-[#FFDD44] transition text-sm">Dashboard</a>
            <?php if ($isHeadAdmin): ?>
            <a href="<?= BASE_PATH ?>/admin/manage_admin" class="text-white hover:text-[#FFDD44] transition text-sm">Kelola Admin</a>
            <a href="<?= BASE_PATH ?>/admin/manage_kategori" class="text-white hover:text-[#FFDD44] transition text-sm">Kelola Kategori</a>
            <?php endif; ?>
            <!-- Profil Admin -->
            <div class="flex items-center gap-2 bg-white/10 rounded-lg px-3 py-1.5">
                <a href="<?= BASE_PATH ?>/admin/profile" class="flex items-center gap-2 hover:opacity-80 transition">
                <div class="w-7 h-7 rounded-full bg-white/20 flex items-center justify-center text-white font-bold text-sm ring-2 ring-white ring-offset-2 ring-offset-gray-800">
    <?= strtoupper(mb_substr($navFullname, 0, 1)) ?>
</div>
                    <div class="leading-tight">
                        <p class="text-white text-xs font-semibold"><?= htmlspecialchars($navFullname) ?></p>
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded <?= $roleBadgeColor ?>"><?= $roleLabel ?></span>
                    </div>
                </a>
            </div>
            <a href="<?= BASE_PATH ?>/admin/logout" class="bg-[#EE6666] text-white px-3 py-1.5 rounded-lg hover:opacity-90 transition text-sm font-semibold">Logout</a>
        </div>
    </div>
</nav>