<?php
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: ' . BASE_PATH . '/admin/login');
    exit();
}
$isHeadAdmin = (($_SESSION['admin_role'] ?? '') === 'head_admin');

$filterKategori = isset($_GET['kategori']) ? trim($_GET['kategori']) : '';
$filterNis      = isset($_GET['nis']) ? trim($_GET['nis']) : '';
$filterBulan    = isset($_GET['bulan']) ? trim($_GET['bulan']) : '';
$filterTanggal  = isset($_GET['tanggal']) ? trim($_GET['tanggal']) : '';
$filterStatus   = isset($_GET['status']) ? trim($_GET['status']) : '';

// Hitung aspirasi pending review untuk badge notifikasi
$stmtPending = $conn->query("SELECT COUNT(*) AS total FROM aspirasi WHERE review_status = 'pending'");
$pendingCount = (int)$stmtPending->fetch()['total'];

$sql = "SELECT ia.id, s.nis, s.full_name, s.class, k.category_name, ia.location, ia.description, ia.bukti_foto, a.aspiration_id, a.status, a.review_status, a.is_anonim, a.feedback, ia.created_at
    FROM input_aspirasi ia
    JOIN siswa s ON ia.nis = s.nis
    JOIN kategori k ON ia.category_id = k.id
    JOIN aspirasi a ON ia.id = a.aspiration_id
    WHERE 1=1";

$params = [];
if ($filterKategori !== '') { $sql .= " AND k.id = ?";                              $params[] = $filterKategori; }
if ($filterNis !== '')      { $sql .= " AND s.nis = ?";                              $params[] = $filterNis; }
if ($filterBulan !== '')    { $sql .= " AND TO_CHAR(ia.created_at, 'YYYY-MM') = ?"; $params[] = $filterBulan; }
if ($filterTanggal !== '')  { $sql .= " AND DATE(ia.created_at) = ?";               $params[] = $filterTanggal; }
if ($filterStatus !== '')   { $sql .= " AND a.status = ?";                           $params[] = $filterStatus; }
$sql .= " ORDER BY ia.id DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
?>

<?php if (isset($_GET['message']) && $_GET['message'] === 'updated') { ?>
    <div class="bg-[#BBDD22] text-gray-800 px-4 py-3 rounded-lg mb-4">Aspirasi berhasil diperbarui.</div>
<?php } ?>

<?php if (isset($_GET['message']) && $_GET['message'] === 'deleted') { ?>
    <div class="bg-[#EE6666] text-white px-4 py-3 rounded-lg mb-4">Aspirasi berhasil dihapus.</div>
<?php } ?>

<?php if (isset($_GET['error']) && $_GET['error'] === 'akses') { ?>
    <div class="bg-[#FFDD44] text-gray-800 px-4 py-3 rounded-lg mb-4 text-sm font-semibold">
        ⚠️ Akses ditolak. Fitur ini hanya tersedia untuk Head Admin.
    </div>
<?php } ?>

<h2 class="text-2xl font-bold text-[#4455DD] mb-4">Daftar Aspirasi Siswa</h2>

<?php if ($pendingCount > 0): ?>
<a href="<?= BASE_PATH ?>/admin/review_aspirasi"
   class="inline-flex items-center gap-2 bg-[#FFDD44] text-gray-800 px-4 py-2 rounded-lg text-sm font-bold hover:opacity-90 transition mb-4">
    📋 Review Aspirasi Baru
    <span class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full"><?= $pendingCount ?></span>
</a>
<?php endif; ?>

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

<div class="overflow-x-auto">
    <table class="w-full bg-white rounded-xl shadow text-sm">
        <thead>
            <tr class="bg-[#4455DD] text-white">
                <th class="px-4 py-3 text-left">NO</th>
                <th class="px-4 py-3 text-left">NIS</th>
                <th class="px-4 py-3 text-left">NAMA</th>
                <th class="px-4 py-3 text-left">KELAS</th>
                <th class="px-4 py-3 text-left">KATEGORI</th>
                <th class="px-4 py-3 text-left">LOKASI</th>
                <th class="px-4 py-3 text-left">DESKRIPSI</th>
                <th class="px-4 py-3 text-left">STATUS</th>
                <th class="px-4 py-3 text-left">REVIEW</th>
                <th class="px-4 py-3 text-left">FOTO</th>
                <th class="px-4 py-3 text-left">FEEDBACK</th>
                <th class="px-4 py-3 text-left">TANGGAL</th>
                <th class="px-4 py-3 text-left">TINDAKAN</th>
            </tr>
        </thead>
        </tbody>
        </table>
    </div>
</div>

<!-- Modals dashboard aspirasi -->
<div id="dashboard-modals"></div>

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

<!-- TIDAK DIGUNAKAN, placeholder agar edit file bisa bekerja -->
<table class="hidden"><tbody>
        <?php
        $no = 1;
        $modalHtml = '';
        while ($row = $stmt->fetch()) {
            $statusClass = match($row['status']) {
                'menunggu' => 'bg-[#FFDD44] text-gray-800',
                'proses'   => 'bg-[#33AAEE] text-white',
                'selesai'  => 'bg-[#BBDD22] text-gray-800',
                default    => 'bg-gray-200 text-gray-800'
            };

            $dashModalId = 'dashmodal-' . intval($row['aspiration_id']);

            // Truncate deskripsi
            $descFull  = htmlspecialchars($row['description']);
            $descShort = mb_strlen($row['description']) > 60
                         ? htmlspecialchars(mb_substr($row['description'], 0, 60)) . '...'
                         : $descFull;
            $descNeed  = mb_strlen($row['description']) > 60;

            // Truncate feedback
            $feedRaw   = $row['feedback'] ?: '';
            $feedFull  = htmlspecialchars($feedRaw ?: '-');
            $feedShort = mb_strlen($feedRaw) > 60
                         ? htmlspecialchars(mb_substr($feedRaw, 0, 60)) . '...'
                         : $feedFull;
            $feedNeed  = mb_strlen($feedRaw) > 60;

            echo "<tr class='border-t border-gray-100 hover:bg-gray-50'>";
            echo "<td class='px-4 py-3'>" . $no++ . "</td>";
            echo "<td class='px-4 py-3'>" . htmlspecialchars($row['nis']) . "</td>";
            echo "<td class='px-4 py-3'>" . htmlspecialchars($row['full_name']) . "</td>";
            echo "<td class='px-4 py-3'>" . htmlspecialchars($row['class']) . "</td>";
            echo "<td class='px-4 py-3'>" . htmlspecialchars($row['category_name']) . "</td>";
            echo "<td class='px-4 py-3'>" . htmlspecialchars($row['location']) . "</td>";
            // Deskripsi
            echo "<td class='px-4 py-3' style='max-width:160px'>";
            echo "<p class='truncate text-sm text-gray-800'>" . $descShort . "</p>";
            if ($descNeed) echo "<button onclick=\"openDashModal('$dashModalId')\" class='text-[#4455DD] text-xs hover:underline whitespace-nowrap'>Lihat selengkapnya</button>";
            echo "</td>";
            echo "<td class='px-4 py-3'><span class='px-2 py-1 rounded-full text-xs font-semibold $statusClass'>" . ucfirst(htmlspecialchars($row['status'])) . "</span></td>";
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
            $anonimTag = $row['is_anonim'] ? "<span class='ml-1 text-xs bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded'>Anonim</span>" : '';
            echo "<td class='px-4 py-3'><span class='px-2 py-1 rounded-full text-xs font-semibold $reviewClass'>$reviewLabel</span>$anonimTag</td>";
            if (!empty($row['bukti_foto'])) {
                $fotoUrl = BASE_PATH . '/public/uploads/bukti/' . htmlspecialchars($row['bukti_foto']);
                echo "<td class='px-4 py-3'><a href='$fotoUrl' target='_blank'><img src='$fotoUrl' alt='Bukti' class='w-12 h-12 object-contain rounded border border-gray-200 hover:opacity-80 transition'></a></td>";
            } else {
                echo "<td class='px-4 py-3 text-gray-400 text-xs'>-</td>";
            }
            echo "<td class='px-4 py-3'>" . htmlspecialchars($row['feedback'] ?: '-') . "</td>";
            echo "<td class='px-4 py-3'>" . date('d-m-Y', strtotime($row['created_at'])) . "</td>";
            echo "<td class='px-4 py-3'>
                    <a href='" . BASE_PATH . "/admin/edit_aspirasi?aspiration_id=" . intval($row['aspiration_id']) . "'
                       class='bg-[#FFDD44] text-gray-800 px-3 py-1 rounded text-xs font-semibold hover:opacity-90 transition'>Edit</a>";
            if ($isHeadAdmin) {
                echo " <a href='" . BASE_PATH . "/admin/delete_aspirasi?aspiration_id=" . intval($row['aspiration_id']) . "'
                       onclick=\"return confirm('Yakin hapus aspirasi ini? Data tidak bisa dikembalikan.')\"
                       class='bg-[#EE6666] text-white px-3 py-1 rounded text-xs font-semibold hover:opacity-90 transition ml-1'>Hapus</a>";
            }
            echo "</td>";
            echo "</tr>";
        }
        ?>
        </tbody>
    </table>
</div>
