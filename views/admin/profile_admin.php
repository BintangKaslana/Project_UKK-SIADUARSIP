<?php
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: ' . BASE_PATH . '/admin/login');
    exit();
}

$id = (int)$_SESSION['admin_id'];
$stmt = $conn->prepare("SELECT id, username, full_name, role FROM admin WHERE id = ?");
$stmt->execute([$id]);
$admin = $stmt->fetch();
if (!$admin) {
    header('Location: ' . BASE_PATH . '/admin/logout');
    exit();
}

$roleLabel = $admin['role'] === 'head_admin' ? 'Head Admin' : 'Admin Biasa';
$error   = $_GET['error']   ?? '';
$success = $_GET['success'] ?? '';
?>

<div class="max-w-lg mx-auto space-y-6">
    <h1 class="text-2xl font-bold text-[#4455DD]">Profil Saya</h1>

    <?php if ($error === 'password_mismatch'): ?>
        <div class="bg-red-50 border border-red-300 text-red-700 rounded-lg px-4 py-3 text-sm">Password baru dan konfirmasi tidak cocok.</div>
    <?php elseif ($error === 'password_short'): ?>
        <div class="bg-red-50 border border-red-300 text-red-700 rounded-lg px-4 py-3 text-sm">Password baru minimal 6 karakter.</div>
    <?php elseif ($error === 'wrong_password'): ?>
        <div class="bg-red-50 border border-red-300 text-red-700 rounded-lg px-4 py-3 text-sm">Password lama tidak sesuai.</div>
    <?php elseif ($error === 'empty'): ?>
        <div class="bg-red-50 border border-red-300 text-red-700 rounded-lg px-4 py-3 text-sm">Semua field wajib diisi.</div>
    <?php endif; ?>

    <?php if ($success === 'profile_updated'): ?>
        <div class="bg-green-50 border border-green-300 text-green-700 rounded-lg px-4 py-3 text-sm">Profil berhasil diperbarui.</div>
    <?php elseif ($success === 'password_changed'): ?>
        <div class="bg-green-50 border border-green-300 text-green-700 rounded-lg px-4 py-3 text-sm">Password berhasil diubah.</div>
    <?php endif; ?>

    <!-- Info & Edit Profil -->
    <div class="bg-white rounded-xl shadow p-6 space-y-4">
        <div class="flex items-center gap-4 mb-2">
            <div class="w-14 h-14 rounded-full bg-[#4455DD] flex items-center justify-center text-white font-bold text-2xl">
                <?= strtoupper(mb_substr($admin['full_name'], 0, 1)) ?>
            </div>
            <div>
                <p class="font-bold text-gray-800 text-lg"><?= htmlspecialchars($admin['full_name']) ?></p>
                <span class="text-xs font-semibold px-2 py-1 rounded-full <?= $admin['role'] === 'head_admin' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' ?>">
                    <?= $roleLabel ?>
                </span>
            </div>
        </div>

        <h2 class="text-sm font-bold text-gray-500 uppercase tracking-wide border-b pb-1">Edit Profil</h2>
        <form action="<?= BASE_PATH ?>/admin/update_profile" method="post" class="space-y-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="full_name" value="<?= htmlspecialchars($admin['full_name']) ?>" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#4455DD]">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Username</label>
                <input type="text" name="username" value="<?= htmlspecialchars($admin['username']) ?>" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#4455DD]">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Role</label>
                <input type="text" value="<?= $roleLabel ?>" disabled
                       class="w-full border border-gray-200 bg-gray-50 rounded-lg px-3 py-2 text-gray-500 text-sm">
            </div>
            <button type="submit" class="bg-[#4455DD] text-white px-6 py-2 rounded-lg font-semibold hover:opacity-90 transition">
                Simpan Perubahan
            </button>
        </form>
    </div>

    <!-- Ganti Password -->
    <div class="bg-white rounded-xl shadow p-6 space-y-4">
        <h2 class="text-sm font-bold text-gray-500 uppercase tracking-wide border-b pb-1">Ganti Password</h2>
        <form action="<?= BASE_PATH ?>/admin/change_password" method="post" class="space-y-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Password Lama</label>
                <input type="password" name="old_password" required placeholder="Masukkan password lama"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#4455DD]">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Password Baru</label>
                <input type="password" name="new_password" required placeholder="Minimal 6 karakter"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#4455DD]">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Konfirmasi Password Baru</label>
                <input type="password" name="new_password_confirm" required placeholder="Ulangi password baru"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#4455DD]">
            </div>
            <button type="submit" class="bg-[#EE6666] text-white px-6 py-2 rounded-lg font-semibold hover:opacity-90 transition">
                Ganti Password
            </button>
        </form>
    </div>
</div>
