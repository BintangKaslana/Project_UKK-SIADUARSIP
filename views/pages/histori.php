<div class="max-w-4xl mx-auto px-4 py-6">
    <div class="mb-4">
        <p class="text-[#33AAEE] text-xs font-bold tracking-widest uppercase mb-1">✦ Cek Pengaduan</p>
        <h2 class="text-2xl font-bold text-[#4455DD] mb-1">Histori Aspirasi</h2>
        <p class="text-gray-500 text-sm">Masukkan NIS kamu untuk melihat histori pengaduanmu.</p>
    </div>

    <div class="bg-white rounded-xl shadow-lg border-t-4 border-[#33AAEE] p-5 mb-4">
        <form method="get" class="flex gap-3 items-end">
            <div class="flex-1">
                <label class="block text-xs font-semibold text-gray-700 mb-1">NIS</label>
                <input type="text" name="nis" placeholder="Contoh: 57575249"
                       value="<?= htmlspecialchars($_GET['nis'] ?? '') ?>"
                       class="w-full border-2 border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:border-[#33AAEE] transition">
            </div>
            <button type="submit"
                    class="bg-[#4455DD] text-white px-6 py-1.5 rounded-lg font-semibold hover:bg-[#33AAEE] transition text-sm">
                Cari
            </button>
            <?php if (!empty($_GET['nis'])) { ?>
                <a href="/ukk_bintang_26/histori"
                   class="bg-gray-200 text-gray-700 px-6 py-1.5 rounded-lg font-semibold hover:opacity-90 transition text-sm">
                    Reset
                </a>
            <?php } ?>
        </form>
    </div>

    <?php
    $filterNis = trim($_GET['nis'] ?? '');
    if ($filterNis !== '') {
        $sql = "SELECT ia.id, s.nis, s.full_name, s.class, k.category_name, ia.location,
                       ia.description, a.status, a.review_status, a.is_anonim, a.feedback,
                       a.feedback_by, adm.full_name AS admin_fullname, adm.username AS admin_username, adm.role AS admin_role,
                       ia.created_at
                FROM input_aspirasi ia
                JOIN siswa s ON ia.nis = s.nis
                JOIN kategori k ON ia.category_id = k.id
                JOIN aspirasi a ON ia.id = a.aspiration_id
                LEFT JOIN admin adm ON a.feedback_by = adm.id
                WHERE s.nis = ?
                  AND a.review_status = 'approved'
                ORDER BY ia.id DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$filterNis]);
        $rows = $stmt->fetchAll();

        if (count($rows) === 0) {
            echo "<div class='bg-[#FFDD44]/20 border-l-4 border-[#FFDD44] px-4 py-3 rounded text-sm text-gray-700'>
                    Tidak ada pengaduan yang sudah disetujui untuk NIS <strong>$filterNis</strong>.
                    Pengaduanmu mungkin masih dalam proses review admin.
                  </div>";
        } else {
            echo "<p class='text-gray-500 text-sm mb-2'>Menampilkan <strong>" . count($rows) . " pengaduan</strong> untuk NIS <strong>" . htmlspecialchars($filterNis) . "</strong></p>";
            echo "<div class='overflow-x-auto'>";
            echo "<table class='w-full bg-white rounded-xl shadow text-sm table-fixed'>";
            echo "<colgroup>
                    <col style='width:36px'>
                    <col style='width:100px'>
                    <col style='width:90px'>
                    <col style='width:150px'>
                    <col style='width:105px'>
                    <col style='width:150px'>
                    <col style='width:80px'>
                  </colgroup>";
            echo "<thead><tr class='bg-[#4455DD] text-white text-xs'>
                    <th class='px-3 py-2 text-left'>No</th>
                    <th class='px-3 py-2 text-left'>Kategori</th>
                    <th class='px-3 py-2 text-left'>Lokasi</th>
                    <th class='px-3 py-2 text-left'>Deskripsi</th>
                    <th class='px-3 py-2 text-left'>Status</th>
                    <th class='px-3 py-2 text-left'>Feedback</th>
                    <th class='px-3 py-2 text-left'>Tanggal</th>
                  </tr></thead><tbody>";
            $no = 1;
            foreach ($rows as $row) {
                $statusClass = match($row['status']) {
                    'menunggu' => 'bg-[#FFDD44] text-gray-800',
                    'proses'   => 'bg-[#33AAEE] text-white',
                    'selesai'  => 'bg-[#BBDD22] text-gray-800',
                    default    => 'bg-gray-200 text-gray-800'
                };

                // Truncate deskripsi & feedback jika terlalu panjang
                $descFull     = htmlspecialchars($row['description']);
                $descNeedMore = mb_strlen($row['description']) > 80;

                $feedbackRaw  = $row['feedback'] ?? '';
                $feedFull     = htmlspecialchars($feedbackRaw ?: '-');
                $feedNeedMore = mb_strlen($feedbackRaw) > 80;

                $modalId = 'modal-' . $row['id'];

                echo "<tr class='border-t border-gray-100 hover:bg-gray-50'>";
                echo "<td class='px-3 py-2'>" . $no++ . "</td>";
                echo "<td class='px-3 py-2'>" . htmlspecialchars($row['category_name']) . "</td>";
                echo "<td class='px-3 py-2'>" . htmlspecialchars($row['location']) . "</td>";

                // Kolom Deskripsi
                echo "<td class='px-3 py-2'>";
                echo "<div>";
                echo "<p class='text-gray-800 text-xs leading-snug' style='display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;word-break:break-word;'>" . $descFull . "</p>";
                if ($descNeedMore) {
                    echo "<button onclick=\"openModal('$modalId')\" class='text-[#4455DD] text-xs hover:underline mt-0.5 whitespace-nowrap font-medium'>Lihat selengkapnya →</button>";
                }
                echo "</div></td>";

                // Kolom Status
                $anonimBadge = $row['is_anonim'] ? "<span class='ml-1 text-xs bg-gray-200 text-gray-500 px-1.5 py-0.5 rounded font-medium'>Anonim</span>" : '';
                echo "<td class='px-3 py-2'><span class='px-2 py-1 rounded-full text-xs font-semibold $statusClass'>" . ucfirst($row['status']) . "</span>$anonimBadge</td>";

                // Kolom Feedback
                echo "<td class='px-3 py-2'>";
                echo "<div>";
                echo "<p class='text-gray-800 text-xs leading-snug' style='display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;word-break:break-word;'>" . $feedFull . "</p>";
                if ($feedNeedMore) {
                    echo "<button onclick=\"openModal('$modalId')\" class='text-[#4455DD] text-xs hover:underline mt-0.5 whitespace-nowrap font-medium'>Lihat selengkapnya →</button>";
                }
                echo "</div></td>";

                echo "<td class='px-3 py-2'>" . date('d-m-Y', strtotime($row['created_at'])) . "</td>";
                echo "</tr>";

                // Modal detail aspirasi
                echo "
                <tr id='$modalId-row' class='hidden'>
                  <td colspan='7' class='p-0'></td>
                </tr>";

                // Modal overlay
                $kategori  = htmlspecialchars($row['category_name']);
                $lokasi    = htmlspecialchars($row['location']);
                $tgl       = date('d M Y', strtotime($row['created_at']));

                // Info admin yang balas feedback
                $adminInfoHtml = '';
                if (!empty($row['feedback']) && !empty($row['feedback_by'])) {
                    $adminName  = $row['admin_fullname'] ?: $row['admin_username'] ?: 'Admin';
                    $adminRoleL = $row['admin_role'] === 'head_admin' ? 'Head Admin' : 'Admin';
                    $adminInfoHtml = "<p class='text-xs text-[#4455DD] font-semibold mt-2'>👤 Dibalas oleh: " . htmlspecialchars($adminName) . " <span class='bg-[#FFDD44] text-gray-700 text-[10px] px-1.5 py-0.5 rounded font-bold ml-1'>$adminRoleL</span></p>";
                }

                echo "
                <div id='$modalId' class='fixed inset-0 z-50 hidden items-center justify-center bg-black/50 px-4' style='align-items:center;'>
                  <div class='bg-white rounded-2xl shadow-2xl w-full p-6 relative' style='max-width:520px;max-height:85vh;overflow-y:auto;'>
                    <button onclick=\"closeModal('$modalId')\" class='absolute top-3 right-4 text-gray-400 hover:text-gray-700 text-2xl font-bold leading-none'>&times;</button>
                    <h3 class='text-[#4455DD] font-bold text-base mb-1'>Detail Aspirasi</h3>
                    <p class='text-xs text-gray-400 mb-4'>$kategori &bull; $lokasi &bull; $tgl</p>
                    <div class='mb-4 bg-gray-50 rounded-lg p-3'>
                      <p class='text-xs font-semibold text-gray-500 uppercase mb-2'>Deskripsi Lengkap</p>
                      <p class='text-sm text-gray-800 leading-relaxed whitespace-pre-wrap'>$descFull</p>
                    </div>
                    <div class='bg-blue-50 rounded-lg p-3'>
                      <p class='text-xs font-semibold text-gray-500 uppercase mb-2'>Feedback Admin</p>
                      <p class='text-sm text-gray-800 leading-relaxed whitespace-pre-wrap'>$feedFull</p>
                      $adminInfoHtml
                    </div>
                  </div>
                </div>";
            }
            echo "</tbody></table></div>";
        }
    } else {
        echo "<div class='border-l-4 border-[#4455DD] bg-blue-50 px-4 py-3 rounded text-sm text-gray-700'>
                Masukkan NIS kamu di atas untuk melihat histori pengaduan.
              </div>";
    }
    ?>
</div>

<!-- Modal Script -->
<script>
function openModal(id) {
    const el = document.getElementById(id);
    if (el) {
        el.classList.remove('hidden');
        el.classList.add('flex');
    }
}
function closeModal(id) {
    const el = document.getElementById(id);
    if (el) {
        el.classList.add('hidden');
        el.classList.remove('flex');
    }
}
// Tutup modal jika klik di luar konten
document.addEventListener('click', function(e) {
    document.querySelectorAll('[id^="modal-"]').forEach(function(modal) {
        if (e.target === modal) closeModal(modal.id);
    });
});
</script>
