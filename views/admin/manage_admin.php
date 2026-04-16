<?php
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: ' . BASE_PATH . '/admin/login');
    exit();
}
// Hanya Head Admin yang bisa akses
if (($_SESSION['admin_role'] ?? '') !== 'head_admin') {
    header('Location: ' . BASE_PATH . '/admin?error=akses');
    exit();
}
?>

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-[#4455DD]">Kelola Admin</h1>
        <p class="text-gray-500 text-sm mt-0.5">Hanya Head Admin yang dapat mengelola akun admin.</p>
    </div>
    <a href="<?= BASE_PATH ?>/admin/add_admin"
       class="bg-[#4455DD] text-white px-4 py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition">
        + Tambah Admin
    </a>
</div>

<?php if (isset($_GET['message']) && $_GET['message'] === 'saved'): ?>
    <div class="bg-[#BBDD22] text-gray-800 px-4 py-3 rounded-lg mb-4 text-sm font-semibold">✅ Admin berhasil ditambahkan.</div>
<?php endif; ?>
<?php if (isset($_GET['message']) && $_GET['message'] === 'updated'): ?>
    <div class="bg-[#33AAEE] text-white px-4 py-3 rounded-lg mb-4 text-sm font-semibold">✅ Admin berhasil diperbarui.</div>
<?php endif; ?>
<?php if (isset($_GET['message']) && $_GET['message'] === 'deleted'): ?>
    <div class="bg-[#EE6666] text-white px-4 py-3 rounded-lg mb-4 text-sm font-semibold">🗑️ Admin berhasil dihapus.</div>
<?php endif; ?>

<div class="overflow-x-auto">
    <table class="w-full bg-white rounded-xl shadow text-sm">
        <thead>
            <tr class="bg-[#4455DD] text-white">
                <th class="px-4 py-3 text-left">No</th>
                <th class="px-4 py-3 text-left">Nama Lengkap</th>
                <th class="px-4 py-3 text-left">Username</th>
                <th class="px-4 py-3 text-left">Role</th>
                <th class="px-4 py-3 text-left">Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $currentAdminId = (int)($_SESSION['admin_id'] ?? 0);
        $stmt = $conn->query("SELECT id, username, full_name, role FROM admin ORDER BY role DESC, id ASC");
        $no = 1;
        while ($row = $stmt->fetch()) {
            $isMe      = ($row['id'] === $currentAdminId);
            $roleLabel = $row['role'] === 'head_admin' ? 'Head Admin' : 'Admin';
            $roleBadge = $row['role'] === 'head_admin'
                ? "<span class='bg-[#FFDD44] text-gray-800 text-xs font-bold px-2 py-0.5 rounded-full'>{$roleLabel}</span>"
                : "<span class='bg-gray-100 text-gray-600 text-xs font-semibold px-2 py-0.5 rounded-full'>{$roleLabel}</span>";
            $meBadge   = $isMe ? " <span class='text-[10px] bg-[#33AAEE] text-white px-1.5 py-0.5 rounded font-medium'>Kamu</span>" : '';

            echo "<tr class='border-t border-gray-100 hover:bg-gray-50'>";
            echo "<td class='px-4 py-3'>" . $no++ . "</td>";
            echo "<td class='px-4 py-3 font-medium'>" . htmlspecialchars($row['full_name'] ?: '-') . "$meBadge</td>";
            echo "<td class='px-4 py-3 text-gray-600'>" . htmlspecialchars($row['username']) . "</td>";
            echo "<td class='px-4 py-3'>$roleBadge</td>";

            $editBtn = "<a href='" . BASE_PATH . "/admin/edit_admin?id=" . $row['id'] . "'
                           class='bg-[#FFDD44] text-gray-800 px-3 py-1 rounded text-xs font-semibold hover:opacity-90 transition'>Edit</a>";
            // Tidak bisa hapus diri sendiri atau satu-satunya head_admin
            if ($isMe) {
                $deleteBtn = "<span class='text-xs text-gray-400 px-2'>Tidak bisa hapus diri sendiri</span>";
            } else {
                $deleteBtn = "<a href='" . BASE_PATH . "/admin/delete_admin?id=" . $row['id'] . "'
                                onclick=\"return confirm('Yakin hapus admin ini? Aksi tidak bisa dibatalkan.')\"
                                class='bg-[#EE6666] text-white px-3 py-1 rounded text-xs font-semibold hover:opacity-90 transition ml-1'>Hapus</a>";
            }
            echo "<td class='px-4 py-3 flex gap-1 items-center'>$editBtn $deleteBtn</td>";
            echo "</tr>";
        }
        ?>
        </tbody>
    </table>
</div>