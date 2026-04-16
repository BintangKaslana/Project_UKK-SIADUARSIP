<?php
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';

// Validasi session admin
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: ' . BASE_PATH . '/admin/login');
    exit();
}

// Validasi aspiration_id
$aspiration_id = intval($_GET['aspiration_id'] ?? 0);
if ($aspiration_id <= 0) {
    header('Location: ' . BASE_PATH . '/admin');
    exit();
}

$sql = "SELECT ia.id, s.nis, s.full_name, s.class, k.category_name, ia.location, ia.description, ia.bukti_foto, a.aspiration_id, a.status, a.feedback
        FROM input_aspirasi ia
        JOIN siswa s ON ia.nis = s.nis
        JOIN kategori k ON ia.category_id = k.id
        JOIN aspirasi a ON ia.id = a.aspiration_id
        WHERE a.aspiration_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$aspiration_id]);
$row = $stmt->fetch();

// Handle jika data tidak ditemukan
if (!$row) {
    header('Location: ' . BASE_PATH . '/admin');
    exit();
}
?>

<div class="max-w-2xl mx-auto">
    <h2 class="text-2xl font-bold text-[#4455DD] mb-6">Edit Aspirasi</h2>

    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div><span class="font-semibold text-gray-600">NIS:</span> <?= htmlspecialchars($row['nis'] ?? '') ?></div>
            <div><span class="font-semibold text-gray-600">Nama:</span> <?= htmlspecialchars($row['full_name'] ?? '') ?></div>
            <div><span class="font-semibold text-gray-600">Kelas:</span> <?= htmlspecialchars($row['class'] ?? '') ?></div>
            <div><span class="font-semibold text-gray-600">Kategori:</span> <?= htmlspecialchars($row['category_name'] ?? '') ?></div>
            <div><span class="font-semibold text-gray-600">Lokasi:</span> <?= htmlspecialchars($row['location'] ?? '') ?></div>
            <div class="col-span-2"><span class="font-semibold text-gray-600">Deskripsi:</span> <?= htmlspecialchars($row['description'] ?? '') ?></div>
            <?php if (!empty($row['bukti_foto'])): ?>
            <div class="col-span-2">
                <span class="font-semibold text-gray-600">Bukti Foto:</span>
                <div class="mt-2">
                    <a href="<?= BASE_PATH ?>/public/uploads/bukti/<?= htmlspecialchars($row['bukti_foto']) ?>" target="_blank">
                        <img src="<?= BASE_PATH ?>/public/uploads/bukti/<?= htmlspecialchars($row['bukti_foto']) ?>"
                             alt="Bukti Foto"
                             class="max-h-48 rounded-lg border border-gray-200 hover:opacity-90 transition object-contain">
                    </a>
                    <p class="text-xs text-gray-400 mt-1">Klik gambar untuk buka ukuran penuh</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        <form action="<?= BASE_PATH ?>/admin/update_aspirasi" method="post" class="space-y-4">
            <input type="hidden" name="aspiration_id" value="<?= htmlspecialchars($row['aspiration_id']) ?>">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#4455DD]">
                    <option value="menunggu" <?= $row['status'] === 'menunggu' ? 'selected' : '' ?>>Menunggu</option>
                    <option value="proses"   <?= $row['status'] === 'proses'   ? 'selected' : '' ?>>Proses</option>
                    <option value="selesai"  <?= $row['status'] === 'selesai'  ? 'selected' : '' ?>>Selesai</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Feedback</label>
                <textarea name="feedback" rows="5"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#4455DD]"><?= htmlspecialchars($row['feedback'] ?? '') ?></textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="bg-[#4455DD] text-white px-6 py-2 rounded-lg font-semibold hover:opacity-90 transition">
                    Update
                </button>
                <a href="<?= BASE_PATH ?>/admin" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-semibold hover:opacity-90 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
