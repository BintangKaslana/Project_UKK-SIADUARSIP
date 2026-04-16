<?php
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: ' . BASE_PATH . '/admin/login');
    exit();
}
$isHeadAdmin = (($_SESSION['admin_role'] ?? '') === 'head_admin');

$filterKategori = isset($_GET['kategori']) ? trim($_GET['kategori']) : '';
$filterNis      = isset($_GET['nis'])      ? trim($_GET['nis'])      : '';
$filterBulan    = isset($_GET['bulan'])    ? trim($_GET['bulan'])    : '';
$filterTanggal  = isset($_GET['tanggal']) ? trim($_GET['tanggal'])  : '';
$filterStatus   = isset($_GET['status'])  ? trim($_GET['status'])   : '';

// --- PAGINATION ---
$per_page    = 10;
$page        = max(1, intval($_GET['page'] ?? 1));
$offset      = ($page - 1) * $per_page;

// Hitung aspirasi pending review untuk badge notifikasi
$stmtPending  = $conn->query("SELECT COUNT(*) AS total FROM aspirasi WHERE review_status = 'pending'");
$pendingCount = (int)$stmtPending->fetch()['total'];

$sqlBase = " FROM input_aspirasi ia
        JOIN siswa s    ON ia.nis         = s.nis
        JOIN kategori k ON ia.category_id = k.id
        JOIN aspirasi a ON ia.id          = a.aspiration_id
        LEFT JOIN admin adm ON a.feedback_by = adm.id
        WHERE 1=1";

$params = [];
if ($filterKategori !== '') { $sqlBase .= " AND k.id = ?";                              $params[] = $filterKategori; }
if ($filterNis !== '')      { $sqlBase .= " AND s.nis = ?";                              $params[] = $filterNis; }
if ($filterBulan !== '')    { $sqlBase .= " AND TO_CHAR(ia.created_at, 'YYYY-MM') = ?"; $params[] = $filterBulan; }
if ($filterTanggal !== '')  { $sqlBase .= " AND DATE(ia.created_at) = ?";               $params[] = $filterTanggal; }
if ($filterStatus !== '')   { $sqlBase .= " AND a.status = ?";                           $params[] = $filterStatus; }

// Hitung total rows untuk pagination
$countStmt = $conn->prepare("SELECT COUNT(*)" . $sqlBase);
$countStmt->execute($params);
$total_rows  = (int) $countStmt->fetchColumn();
$total_pages = max(1, (int) ceil($total_rows / $per_page));
if ($page > $total_pages) $page = $total_pages;

// Ambil data dengan LIMIT + OFFSET
$sql = "SELECT ia.id, s.nis, s.full_name, s.class, k.category_name, ia.location, ia.description,
               ia.bukti_foto, a.aspiration_id, a.status, a.review_status, a.is_anonim,
               a.feedback, a.feedback_by, adm.full_name AS fb_admin_name, adm.username AS fb_admin_user, adm.role AS fb_admin_role,
               ia.created_at" . $sqlBase . " ORDER BY ia.id DESC LIMIT :limit OFFSET :offset";
$stmt = $conn->prepare($sql);
foreach ($params as $i => $val) {
    $stmt->bindValue($i + 1, $val);
}
$stmt->bindValue(':limit',  $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset,   PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll();
?>

<?php if (isset($_GET['message']) && $_GET['message'] === 'updated'): ?>
    <div class="bg-[#BBDD22] text-gray-800 px-4 py-3 rounded-lg mb-4 text-sm font-semibold">✅ Aspirasi berhasil diperbarui.</div>
<?php endif; ?>
<?php if (isset($_GET['message']) && $_GET['message'] === 'deleted'): ?>
    <div class="bg-[#EE6666] text-white px-4 py-3 rounded-lg mb-4 text-sm font-semibold">🗑️ Aspirasi berhasil dihapus.</div>
<?php endif; ?>
<?php if (isset($_GET['error']) && $_GET['error'] === 'akses'): ?>
    <div class="bg-[#FFDD44] text-gray-800 px-4 py-3 rounded-lg mb-4 text-sm font-semibold">⚠️ Akses ditolak. Fitur ini hanya tersedia untuk Head Admin.</div>
<?php endif; ?>

<h2 class="text-2xl font-bold text-[#4455DD] mb-4">Daftar Aspirasi Siswa</h2>

<?php if ($pendingCount > 0): ?>
<a href="<?= BASE_PATH ?>/admin/review_aspirasi"
   class="inline-flex items-center gap-2 bg-[#FFDD44] text-gray-800 px-4 py-2 rounded-lg text-sm font-bold hover:opacity-90 transition mb-4">
    📋 Review Aspirasi Baru
    <span class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full"><?= $pendingCount ?></span>
</a>
<?php endif; ?>

<!-- Filter -->
<form method="get" class="bg-white rounded-xl shadow p-4 mb-6 flex flex-wrap gap-3 items-end">
    <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1">Kategori</label>
        <select name="kategori" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#4455DD]">
            <option value="">PILIH KATEGORI</option>
            <?php
            $kStmt = $conn->query("SELECT id, category_name FROM kategori ORDER BY id");
            foreach ($kStmt->fetchAll() as $k) {
                $selected = $filterKategori == $k['id'] ? 'selected' : '';
                echo "<option value='" . htmlspecialchars($k['id']) . "' $selected>" . htmlspecialchars($k['category_name']) . "</option>";
            }
            ?>
        </select>
    </div>
    <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1">NIS</label>
        <input type="text" name="nis" placeholder="Filter NIS..." value="<?= htmlspecialchars($filterNis) ?>"
               class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#4455DD]">
    </div>
    <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
        <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#4455DD]">
            <option value=""></option>
            <option value="menunggu" <?= $filterStatus === 'menunggu' ? 'selected' : '' ?>>MENUNGGU</option>
            <option value="proses"   <?= $filterStatus === 'proses'   ? 'selected' : '' ?>>PROSES</option>
            <option value="selesai"  <?= $filterStatus === 'selesai'  ? 'selected' : '' ?>>SELESAI</option>
        </select>
    </div>
    <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1">Bulan</label>
        <input type="month" name="bulan" value="<?= htmlspecialchars($filterBulan) ?>"
               class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#4455DD]">
    </div>
    <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal</label>
        <input type="date" name="tanggal" value="<?= htmlspecialchars($filterTanggal) ?>"
               class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#4455DD]">
    </div>
    <button type="submit" class="bg-[#4455DD] text-white px-4 py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition">Filter</button>
    <a href="<?= BASE_PATH ?>/admin" class="bg-[#EE6666] text-white px-4 py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition">Reset</a>
</form>

<!-- Tabel Aspirasi -->
<div class="overflow-x-auto">
    <table class="w-full bg-white rounded-xl shadow text-sm">
        <thead>
            <tr class="bg-[#4455DD] text-white text-xs">
                <th class="px-3 py-3 text-left">NO</th>
                <th class="px-3 py-3 text-left">NIS</th>
                <th class="px-3 py-3 text-left">NAMA</th>
                <th class="px-3 py-3 text-left">KELAS</th>
                <th class="px-3 py-3 text-left">KATEGORI</th>
                <th class="px-3 py-3 text-left">LOKASI</th>
                <th class="px-3 py-3 text-left">DESKRIPSI</th>
                <th class="px-3 py-3 text-left">STATUS</th>
                <th class="px-3 py-3 text-left">REVIEW</th>
                <th class="px-3 py-3 text-left">FOTO</th>
                <th class="px-3 py-3 text-left">FEEDBACK</th>
                <th class="px-3 py-3 text-left">TANGGAL</th>
                <th class="px-3 py-3 text-left">TINDAKAN</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $no = 1;
        foreach ($rows as $row):
            $statusClass = match($row['status']) {
                'menunggu' => 'bg-[#FFDD44] text-gray-800',
                'proses'   => 'bg-[#33AAEE] text-white',
                'selesai'  => 'bg-[#BBDD22] text-gray-800',
                default    => 'bg-gray-200 text-gray-800'
            };
            $reviewClass = match($row['review_status']) {
                'pending'  => 'bg-[#FFDD44] text-gray-800',
                'approved' => 'bg-[#BBDD22] text-gray-800',
                'rejected' => 'bg-[#EE6666] text-white',
                default    => 'bg-gray-200 text-gray-800'
            };
            $reviewLabel = match($row['review_status']) {
                'pending'  => 'Pending',
                'approved' => 'Disetujui',
                'rejected' => 'Ditolak',
                default    => '-'
            };

            $dashModalId = 'dashmodal-' . intval($row['aspiration_id']);

            // Truncate deskripsi
            $descFull = htmlspecialchars($row['description']);
            $descNeed = mb_strlen($row['description']) > 60;
            $descDisp = $descNeed ? htmlspecialchars(mb_substr($row['description'], 0, 60)) . '...' : $descFull;

            // Truncate & info feedback
            $feedRaw  = $row['feedback'] ?? '';
            $feedFull = htmlspecialchars($feedRaw ?: '-');
            $feedNeed = mb_strlen($feedRaw) > 60;
            $feedDisp = $feedNeed ? htmlspecialchars(mb_substr($feedRaw, 0, 60)) . '...' : $feedFull;

            // Info admin yang balas
            $fbAdminHtml = '';
            if (!empty($row['feedback_by'])) {
                $fbName  = htmlspecialchars($row['fb_admin_name'] ?: $row['fb_admin_user'] ?: 'Admin');
                $fbRole  = $row['fb_admin_role'] === 'head_admin' ? 'Head Admin' : 'Admin';
                $fbBadge = $row['fb_admin_role'] === 'head_admin'
                    ? "<span class='bg-[#FFDD44] text-gray-800 text-[10px] font-bold px-1.5 py-0.5 rounded'>Head Admin</span>"
                    : "<span class='bg-gray-100 text-gray-600 text-[10px] font-semibold px-1.5 py-0.5 rounded'>Admin</span>";
                $fbAdminHtml = "<p class='text-xs text-[#4455DD] font-semibold mt-2'>👤 Oleh: $fbName $fbBadge</p>";
            }

            $anonimTag = $row['is_anonim']
                ? "<span class='ml-1 text-xs bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded'>Anonim</span>"
                : '';
        ?>
        <tr class="border-t border-gray-100 hover:bg-gray-50">
            <td class="px-3 py-2"><?= $no++ ?></td>
            <td class="px-3 py-2"><?= htmlspecialchars($row['nis']) ?></td>
            <td class="px-3 py-2"><?= htmlspecialchars($row['full_name']) ?></td>
            <td class="px-3 py-2"><?= htmlspecialchars($row['class']) ?></td>
            <td class="px-3 py-2"><?= htmlspecialchars($row['category_name']) ?></td>
            <td class="px-3 py-2"><?= htmlspecialchars($row['location']) ?></td>
            <!-- Deskripsi truncate -->
            <td class="px-3 py-2" style="max-width:150px">
                <p class="text-xs text-gray-800" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;"><?= $descFull ?></p>
                <?php if ($descNeed): ?>
                    <button onclick="openDashModal('<?= $dashModalId ?>')" class="text-[#4455DD] text-xs hover:underline whitespace-nowrap">Lihat selengkapnya</button>
                <?php endif; ?>
            </td>
            <td class="px-3 py-2">
                <span class="px-2 py-1 rounded-full text-xs font-semibold <?= $statusClass ?>"><?= ucfirst($row['status']) ?></span>
            </td>
            <td class="px-3 py-2">
                <span class="px-2 py-1 rounded-full text-xs font-semibold <?= $reviewClass ?>"><?= $reviewLabel ?></span>
                <?= $anonimTag ?>
            </td>
            <!-- Foto -->
            <td class="px-3 py-2">
                <?php if (!empty($row['bukti_foto'])): ?>
                    <?php $fotoUrl = BASE_PATH . '/public/uploads/bukti/' . htmlspecialchars($row['bukti_foto']); ?>
                    <a href="<?= $fotoUrl ?>" target="_blank">
                        <img src="<?= $fotoUrl ?>" alt="Bukti" class="w-12 h-12 object-contain rounded border border-gray-200 hover:opacity-80 transition">
                    </a>
                <?php else: ?>
                    <span class="text-gray-400 text-xs">-</span>
                <?php endif; ?>
            </td>
            <!-- Feedback truncate + info admin -->
            <td class="px-3 py-2" style="max-width:160px">
                <p class="text-xs text-gray-800" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;"><?= $feedDisp ?></p>
                <?php if (!empty($row['feedback_by'])): ?>
                    <?php
                    $fbName2  = htmlspecialchars($row['fb_admin_name'] ?: $row['fb_admin_user'] ?: 'Admin');
                    $fbRole2  = $row['fb_admin_role'] === 'head_admin' ? 'Head Admin' : 'Admin';
                    ?>
                    <p class="text-[10px] text-[#4455DD] font-medium mt-0.5">👤 <?= $fbName2 ?> <span class="text-gray-400">(<?= $fbRole2 ?>)</span></p>
                <?php endif; ?>
                <?php if ($feedNeed): ?>
                    <button onclick="openDashModal('<?= $dashModalId ?>')" class="text-[#4455DD] text-xs hover:underline whitespace-nowrap">Lihat selengkapnya</button>
                <?php endif; ?>
            </td>
            <td class="px-3 py-2 whitespace-nowrap"><?= date('d-m-Y', strtotime($row['created_at'])) ?></td>
            <!-- Tindakan -->
            <td class="px-3 py-2 whitespace-nowrap">
                <a href="<?= BASE_PATH ?>/admin/edit_aspirasi?aspiration_id=<?= intval($row['aspiration_id']) ?>"
                   class="bg-[#FFDD44] text-gray-800 px-3 py-1 rounded text-xs font-semibold hover:opacity-90 transition">Edit</a>
                <?php if ($isHeadAdmin): ?>
                <a href="<?= BASE_PATH ?>/admin/delete_aspirasi?aspiration_id=<?= intval($row['aspiration_id']) ?>"
                   onclick="return confirm('Yakin hapus aspirasi ini? Data tidak bisa dikembalikan.')"
                   class="bg-[#EE6666] text-white px-3 py-1 rounded text-xs font-semibold hover:opacity-90 transition ml-1">Hapus</a>
                <?php endif; ?>
            </td>
        </tr>

        <!-- Modal detail aspirasi (per baris) -->
        <div id="<?= $dashModalId ?>" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 px-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full p-6 relative" style="max-width:520px;max-height:85vh;overflow-y:auto;">
                <button onclick="closeDashModal('<?= $dashModalId ?>')" class="absolute top-3 right-4 text-gray-400 hover:text-gray-700 text-2xl font-bold leading-none">&times;</button>
                <h3 class="text-[#4455DD] font-bold text-base mb-1">Detail Aspirasi</h3>
                <p class="text-xs text-gray-400 mb-4">
                    <?= htmlspecialchars($row['category_name']) ?> &bull;
                    <?= htmlspecialchars($row['location']) ?> &bull;
                    <?= date('d M Y', strtotime($row['created_at'])) ?>
                </p>
                <div class="mb-4 bg-gray-50 rounded-lg p-3">
                    <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Deskripsi Lengkap</p>
                    <p class="text-sm text-gray-800 leading-relaxed whitespace-pre-wrap"><?= $descFull ?></p>
                </div>
                <div class="bg-blue-50 rounded-lg p-3">
                    <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Feedback Admin</p>
                    <p class="text-sm text-gray-800 leading-relaxed whitespace-pre-wrap"><?= $feedFull ?></p>
                    <?= $fbAdminHtml ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if (count($rows) === 0): ?>
        <tr>
            <td colspan="13" class="px-4 py-8 text-center text-gray-400">Tidak ada data aspirasi.</td>
        </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- PAGINATION DASHBOARD -->
<?php
$queryParams = $_GET;
unset($queryParams['page']);
$queryString = http_build_query($queryParams);
$baseUrl = BASE_PATH . '/admin' . ($queryString ? '?' . $queryString . '&' : '?');
?>
<?php if ($total_pages > 1): ?>
<div class="flex items-center justify-between mt-6">
    <p class="text-sm text-gray-500">
        Menampilkan <?= $offset + 1 ?>–<?= min($offset + $per_page, $total_rows) ?> dari <?= $total_rows ?> aspirasi
    </p>
    <div class="flex items-center gap-1">
        <?php if ($page > 1): ?>
            <a href="<?= $baseUrl ?>page=<?= $page - 1 ?>"
               class="px-3 py-1.5 rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition">← Prev</a>
        <?php else: ?>
            <span class="px-3 py-1.5 rounded-lg border border-gray-100 text-sm text-gray-300 cursor-not-allowed">← Prev</span>
        <?php endif; ?>

        <?php
        $range = 2;
        $start = max(1, $page - $range);
        $end   = min($total_pages, $page + $range);
        ?>
        <?php if ($start > 1): ?>
            <a href="<?= $baseUrl ?>page=1" class="px-3 py-1.5 rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition">1</a>
            <?php if ($start > 2): ?><span class="px-2 text-gray-400 text-sm">…</span><?php endif; ?>
        <?php endif; ?>

        <?php for ($i = $start; $i <= $end; $i++): ?>
            <?php if ($i === $page): ?>
                <span class="px-3 py-1.5 rounded-lg bg-[#4455DD] text-white text-sm font-bold"><?= $i ?></span>
            <?php else: ?>
                <a href="<?= $baseUrl ?>page=<?= $i ?>" class="px-3 py-1.5 rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition"><?= $i ?></a>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if ($end < $total_pages): ?>
            <?php if ($end < $total_pages - 1): ?><span class="px-2 text-gray-400 text-sm">…</span><?php endif; ?>
            <a href="<?= $baseUrl ?>page=<?= $total_pages ?>" class="px-3 py-1.5 rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition"><?= $total_pages ?></a>
        <?php endif; ?>

        <?php if ($page < $total_pages): ?>
            <a href="<?= $baseUrl ?>page=<?= $page + 1 ?>"
               class="px-3 py-1.5 rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition">Next →</a>
        <?php else: ?>
            <span class="px-3 py-1.5 rounded-lg border border-gray-100 text-sm text-gray-300 cursor-not-allowed">Next →</span>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<script>
function openDashModal(id) {
    const el = document.getElementById(id);
    if (el) { el.classList.remove('hidden'); el.classList.add('flex'); }
}
function closeDashModal(id) {
    const el = document.getElementById(id);
    if (el) { el.classList.add('hidden'); el.classList.remove('flex'); }
}
document.addEventListener('click', function(e) {
    document.querySelectorAll('[id^="dashmodal-"]').forEach(function(modal) {
        if (e.target === modal) closeDashModal(modal.id);
    });
});
</script>
