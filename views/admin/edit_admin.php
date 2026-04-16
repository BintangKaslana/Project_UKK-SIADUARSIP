<?php
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: ' . BASE_PATH . '/admin/login');
    exit();
}
if (($_SESSION['admin_role'] ?? '') !== 'head_admin') {
    header('Location: ' . BASE_PATH . '/admin?error=akses');
    exit();
}
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: ' . BASE_PATH . '/admin/manage_admin');
    exit();
}
$stmt = $conn->prepare("SELECT id, username, full_name, role FROM admin WHERE id = ?");
$stmt->execute([$id]);
$admin = $stmt->fetch();
if (!$admin) {
    header('Location: ' . BASE_PATH . '/admin/manage_admin');
    exit();
}
$isSelf = ((int)$_SESSION['admin_id'] === $id);
?>

<div class="max-w-md mx-auto">
    <h1 class="text-2xl font-bold text-[#4455DD] mb-6">Edit Admin</h1>
    <div class="bg-white rounded-xl shadow p-6">
        <form action="<?= BASE_PATH ?>/admin/update_admin" method="post" class="space-y-4">
            <input type="hidden" name="id" value="<?= $admin['id'] ?>">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="full_name" value="<?= htmlspecialchars($admin['full_name'] ?? '') ?>" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#4455DD]">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Username</label>
                <input type="text" name="username" value="<?= htmlspecialchars($admin['username']) ?>" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#4455DD]">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Role</label>
                <?php if ($isSelf): ?>
                    <input type="hidden" name="role" value="<?= htmlspecialchars($admin['role']) ?>">
                    <div class="w-full border border-gray-200 bg-gray-50 rounded-lg px-3 py-2 text-sm text-gray-500">
                        <?= $admin['role'] === 'head_admin' ? 'Head Admin' : 'Admin Biasa' ?>
                        <span class="text-xs text-gray-400 ml-1">(tidak bisa mengubah role diri sendiri)</span>
                    </div>
                <?php else: ?>
                    <select name="role" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#4455DD]">
                        <option value="admin"      <?= $admin['role'] === 'admin'      ? 'selected' : '' ?>>Admin Biasa</option>
                        <option value="head_admin" <?= $admin['role'] === 'head_admin' ? 'selected' : '' ?>>Head Admin</option>
                    </select>
                <?php endif; ?>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="bg-[#4455DD] text-white px-6 py-2 rounded-lg font-semibold hover:opacity-90 transition">
                    Update
                </button>
                <a href="<?= BASE_PATH ?>/admin/manage_admin" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-semibold hover:opacity-90 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>