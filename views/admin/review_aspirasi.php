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

// --- PAGINATION ---
$per_page    = 5;
$page        = max(1, intval($_GET['page'] ?? 1));
$offset      = ($page - 1) * $per_page;

// Hitung total pending
$count_stmt  = $conn->query("SELECT COUNT(*) FROM aspirasi WHERE review_status = 'pending'");
$total_rows  = (int) $count_stmt->fetchColumn();
$total_pages = max(1, (int) ceil($total_rows / $per_page));
if ($page > $total_pages) $page = $total_pages;

// Ambil aspirasi pending dengan LIMIT + OFFSET
$sql = "SELECT ia.id, s.nis, s.full_name, s.class, k.category_name, ia.location,
               ia.description, ia.bukti_foto, a.aspiration_id, a.is_anonim, a.status, ia.created_at
        FROM input_aspirasi ia
        JOIN siswa s ON ia.nis = s.nis
        JOIN kategori k ON ia.category_id = k.id
        JOIN aspirasi a ON ia.id = a.aspiration_id
        WHERE a.review_status = 'pending'
        ORDER BY ia.created_at ASC
        LIMIT :limit OFFSET :offset";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':limit',  $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset,   PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll();
?>

<?php if (isset($_GET['message']) && $_GET['message'] === 'approved') { ?>
    <div class="bg-[#BBDD22] text-gray-800 px-4 py-3 rounded-lg mb-4 text-sm font-semibold">✅ Aspirasi berhasil disetujui dan kini tampil di histori publik.</div>
<?php } ?>
<?php if (isset($_GET['message']) && $_GET['message'] === 'rejected') { ?>
    <div class="bg-[#EE6666] text-white px-4 py-3 rounded-lg mb-4 text-sm font-semibold">🚫 Aspirasi ditolak dan tidak akan ditampilkan ke publik.</div>
<?php } ?>

<script>
function toggleDesc(id) {
    const shortEl = document.getElementById(id + '-short');
    const fullEl  = document.getElementById(id + '-full');
    const btn     = document.getElementById(id + '-btn');
    const isShown = !fullEl.classList.contains('hidden');
    if (isShown) {
        fullEl.classList.add('hidden');
        shortEl.style.display = '-webkit-box';
        btn.textContent = 'Lihat selengkapnya →';
    } else {
        shortEl.style.display = 'none';
        fullEl.classList.remove('hidden');
        btn.textContent = 'Sembunyikan ↑';
    }
}
</script>

<div class="flex items-center justify-between mb-4">
    <div>
        <h2 class="text-2xl font-bold text-[#4455DD]">Review Aspirasi</h2>
        <p class="text-gray-500 text-sm mt-1">Aspirasi baru masuk perlu disetujui sebelum tampil di histori publik.</p>
    </div>
    <span class="bg-[#FFDD44] text-gray-800 text-xs font-bold px-3 py-1 rounded-full">
        <?= $total_rows ?> menunggu review
    </span>
</div>

<?php if ($total_rows === 0) { ?>
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
                    <?php
                        $descFull    = htmlspecialchars($row['description']);
                        $descLong    = mb_strlen($row['description']) > 200;
                        $reviewCardId = 'rdesc-' . intval($row['aspiration_id']);
                    ?>
                    <div>
                        <p id="<?= $reviewCardId ?>-short"
                           class="text-sm text-gray-800 leading-relaxed"
                           style="display:-webkit-box;-webkit-line-clamp:4;-webkit-box-orient:vertical;overflow:hidden;word-break:break-word;">
                            <?= nl2br($descFull) ?>
                        </p>
                        <?php if ($descLong): ?>
                        <p id="<?= $reviewCardId ?>-full"
                           class="text-sm text-gray-800 leading-relaxed whitespace-pre-wrap hidden">
                            <?= nl2br($descFull) ?>
                        </p>
                        <button onclick="toggleDesc('<?= $reviewCardId ?>')"
                                id="<?= $reviewCardId ?>-btn"
                                class="text-[#4455DD] text-xs hover:underline mt-1 font-medium">
                            Lihat selengkapnya →
                        </button>
                        <?php endif; ?>
                    </div>

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

    <!-- PAGINATION -->
    <?php if ($total_pages > 1): ?>
    <div class="flex items-center justify-between mt-6">
        <p class="text-sm text-gray-500">
            Menampilkan <?= $offset + 1 ?>–<?= min($offset + $per_page, $total_rows) ?> dari <?= $total_rows ?> aspirasi
        </p>
        <div class="flex items-center gap-1">
            <!-- Prev -->
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>"
                   class="px-3 py-1.5 rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition">
                    ← Prev
                </a>
            <?php else: ?>
                <span class="px-3 py-1.5 rounded-lg border border-gray-100 text-sm text-gray-300 cursor-not-allowed">← Prev</span>
            <?php endif; ?>

            <!-- Page numbers -->
            <?php
                $range = 2;
                $start = max(1, $page - $range);
                $end   = min($total_pages, $page + $range);
            ?>
            <?php if ($start > 1): ?>
                <a href="?page=1" class="px-3 py-1.5 rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition">1</a>
                <?php if ($start > 2): ?><span class="px-2 text-gray-400 text-sm">…</span><?php endif; ?>
            <?php endif; ?>

            <?php for ($i = $start; $i <= $end; $i++): ?>
                <?php if ($i === $page): ?>
                    <span class="px-3 py-1.5 rounded-lg bg-[#4455DD] text-white text-sm font-bold"><?= $i ?></span>
                <?php else: ?>
                    <a href="?page=<?= $i ?>" class="px-3 py-1.5 rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($end < $total_pages): ?>
                <?php if ($end < $total_pages - 1): ?><span class="px-2 text-gray-400 text-sm">…</span><?php endif; ?>
                <a href="?page=<?= $total_pages ?>" class="px-3 py-1.5 rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition"><?= $total_pages ?></a>
            <?php endif; ?>

            <!-- Next -->
            <?php if ($page < $total_pages): ?>
                <a href="?page=<?= $page + 1 ?>"
                   class="px-3 py-1.5 rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition">
                    Next →
                </a>
            <?php else: ?>
                <span class="px-3 py-1.5 rounded-lg border border-gray-100 text-sm text-gray-300 cursor-not-allowed">Next →</span>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

<?php } ?>
