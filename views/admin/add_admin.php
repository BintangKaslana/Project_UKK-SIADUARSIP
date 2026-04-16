<?php
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: ' . BASE_PATH . '/admin/login');
    exit();
}
if (($_SESSION['admin_role'] ?? '') !== 'head_admin') {
    header('Location: ' . BASE_PATH . '/admin?error=akses');
    exit();
}
?>

<div class="max-w-md mx-auto">
    <h1 class="text-2xl font-bold text-[#4455DD] mb-6">Tambah Admin Baru</h1>
    <div class="bg-white rounded-xl shadow p-6">
        <form action="<?= BASE_PATH ?>/admin/save_admin" method="post" class="space-y-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="full_name" required
                       placeholder="Contoh: Budi Santoso"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#4455DD]">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Username</label>
                <input type="text" name="username" required
                       placeholder="Contoh: budi.santoso"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#4455DD]">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Role</label>
                <select name="role" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#4455DD]">
                    <option value="admin">Admin Biasa</option>
                    <option value="head_admin">Head Admin</option>
                </select>
                <p class="text-xs text-gray-400 mt-1">Head Admin dapat mengelola akun admin lain dan menghapus aspirasi.</p>
            </div>
            <p class="text-xs text-gray-500">Password default: <strong>12345</strong> (admin bisa ganti setelah login)</p>
            <div class="flex gap-3">
                <button type="submit" class="bg-[#4455DD] text-white px-6 py-2 rounded-lg font-semibold hover:opacity-90 transition">
                    Simpan
                </button>
                <a href="<?= BASE_PATH ?>/admin/manage_admin" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-semibold hover:opacity-90 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>