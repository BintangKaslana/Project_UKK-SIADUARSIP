<?php
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: ' . BASE_PATH . '/admin/login');
    exit();
}

// Handle aksi approve/reject via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aspiration_id = intval($_POST['aspiration_id'] ?? 0);
    $aksi          = trim($_POST['aksi'] ?? '');

    if ($aspiration_id > 0 && in_array($aksi, ['approved', 'rejected'])) {
        $stmt = $conn->prepare("UPDATE aspirasi SET review_status = ? WHERE aspiration_id = ?");
        $stmt->execute([$aksi, $aspiration_id]);
    }

    header('Location: ' . BASE_PATH . '/admin/review_aspirasi?message=' . $aksi);
    exit();
}

// Ambil semua aspirasi pending review
$sql = "SELECT ia.id, s.nis, s.full_name, s.class, k.category_name, ia.location,
               ia.description, ia.bukti_foto, a.aspiration_id, a.is_anonim, a.status, ia.created_at
        FROM input_aspirasi ia
        JOIN siswa s ON ia.nis = s.nis
        JOIN kategori k ON ia.category_id = k.id
        JOIN aspirasi a ON ia.id = a.aspiration_id
        WHERE a.review_status = 'pending'
        ORDER BY ia.created_at ASC";
$stmt = $conn->query($sql);
$rows = $stmt->fetchAll();
?>

<?php if (isset($_GET['message']) && $_GET['message'] === 'approved') { ?>
    <div class="bg-[#BBDD22] text-gray-800 px-4 py-3 rounded-lg mb-4 text-sm font-semibold">✅ Aspirasi berhasil disetujui dan kini tampil di histori publik.</div>
<?php } ?>
<?php if (isset($_GET['message']) && $_GET['message'] === 'rejected') { ?>
    <div class="bg-[#EE6666] text-white px-4 py-3 rounded-lg mb-4 text-sm font-semibold">🚫 Aspirasi ditolak dan tidak akan ditampilkan ke publik.</div>
<?php } ?>

<div class="flex items-center justify-between mb-4">
    <div>
        <h2 class="text-2xl font-bold text-[#4455DD]">Review Aspirasi</h2>
        <p class="text-gray-500 text-sm mt-1">Aspirasi baru masuk perlu disetujui sebelum tampil di histori publik.</p>
    </div>
    <span class="bg-[#FFDD44] text-gray-800 text-xs font-bold px-3 py-1 rounded-full">
        <?= count($rows) ?> menunggu review
    </span>
</div>

<?php if (count($rows) === 0) { ?>
    <div class="bg-white rounded-xl shadow p-8 text-center text-gray-400">
        <div class="text-4xl mb-2">✅</div>
        <p class="font-semibold">Semua aspirasi sudah direview.</p>
        <p class="text-sm mt-1">Tidak ada aspirasi baru yang menunggu persetujuan.</p>
    </div>
<?php } else { ?>
    <div class="space-y-4">
        <?php foreach ($rows as $row): ?>
        <div class="bg-white rounded-xl shadow border border-gray-100 p-5">
            <div class="flex items-start justify-between gap-4">
                <!-- Info Aspirasi -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-2 flex-wrap">
                        <span class="bg-[#4455DD] text-white text-xs font-semibold px-2 py-0.5 rounded">
                            <?= htmlspecialchars($row['category_name']) ?>
                        </span>
                        <span class="text-xs text-gray-400">📍 <?= htmlspecialchars($row['location']) ?></span>
                        <span class="text-xs text-gray-400">🕒 <?= date('d M Y, H:i', strtotime($row['created_at'])) ?></span>
                        <?php if ($row['is_anonim']): ?>
                            <span class="bg-gray-100 text-gray-500 text-xs px-2 py-0.5 rounded font-medium">🕵️ Anonim</span>
                        <?php endif; ?>
                    </div>

                    <!-- Identitas siswa (selalu tampil untuk admin) -->
                    <div class="text-xs text-gray-500 mb-2 flex gap-3">
                        <span>NIS: <strong class="text-gray-700"><?= htmlspecialchars($row['nis']) ?></strong></span>
                        <span>Nama: <strong class="text-gray-700"><?= htmlspecialchars($row['full_name']) ?></strong></span>
                        <span>Kelas: <strong class="text-gray-700"><?= htmlspecialchars($row['class']) ?></strong></span>
                    </div>

                    <!-- Deskripsi -->
                    <p class="text-sm text-gray-800 leading-relaxed"><?= nl2br(htmlspecialchars($row['description'])) ?></p>

                    <!-- Foto bukti -->
                    <?php if (!empty($row['bukti_foto'])): ?>
                    <div class="mt-3">
                        <a href="<?= BASE_PATH ?>/public/uploads/bukti/<?= htmlspecialchars($row['bukti_foto']) ?>" target="_blank">
                            <img src="<?= BASE_PATH ?>/public/uploads/bukti/<?= htmlspecialchars($row['bukti_foto']) ?>"
                                 alt="Bukti"
                                 class="max-h-32 rounded-lg border border-gray-200 object-contain hover:opacity-90 transition">
                        </a>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Tombol Approve / Reject -->
                <div class="flex flex-col gap-2 shrink-0">
                    <form method="post" action="<?= BASE_PATH ?>/admin/review_aspirasi">
                        <input type="hidden" name="aspiration_id" value="<?= intval($row['aspiration_id']) ?>">
                        <input type="hidden" name="aksi" value="approved">
                        <button type="submit"
                                class="w-full bg-[#BBDD22] text-gray-800 px-4 py-2 rounded-lg text-xs font-bold hover:opacity-90 transition">
                            ✅ Setujui
                        </button>
                    </form>
                    <form method="post" action="<?= BASE_PATH ?>/admin/review_aspirasi"
                          onsubmit="return confirm('Tolak aspirasi ini? Siswa tidak akan tahu alasannya.')">
                        <input type="hidden" name="aspiration_id" value="<?= intval($row['aspiration_id']) ?>">
                        <input type="hidden" name="aksi" value="rejected">
                        <button type="submit"
                                class="w-full bg-[#EE6666] text-white px-4 py-2 rounded-lg text-xs font-bold hover:opacity-90 transition">
                            🚫 Tolak
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php } ?>
