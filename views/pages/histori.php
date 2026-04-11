<div class="container py-5" style="max-width: 900px;">
    <h2 class="fw-bold mb-1" style="color: #4455DD;">Histori Aspirasi</h2>
    <p class="text-muted mb-4">Masukkan NIS kamu untuk melihat histori pengaduanmu.</p>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <form method="get" class="row g-2 align-items-end">
                <div class="col-md-8">
                    <label class="form-label fw-semibold">NIS</label>
                    <input type="text" name="nis" class="form-control"
                           placeholder="Contoh: 57575249"
                           value="<?= htmlspecialchars($_GET['nis'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn w-100 py-2"
                            style="background-color: #4455DD; color: #fff; border: none;">
                        Cari
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php
    $filterNis = trim($_GET['nis'] ?? '');
    if ($filterNis !== '') {
        $sql = "SELECT ia.id, s.nis, s.full_name, s.class, k.category_name, ia.location,
                       ia.description, a.status, a.feedback, ia.created_at
                FROM input_aspirasi ia
                JOIN siswa s ON ia.nis = s.nis
                JOIN kategori k ON ia.category_id = k.id
                JOIN aspirasi a ON ia.id = a.aspiration_id
                WHERE s.nis = ?
                ORDER BY ia.id DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$filterNis]);
        $rows = $stmt->fetchAll();

        if (count($rows) === 0) {
            echo "<div class='alert' style='background-color:#FFDD44; color:#222; border:none;'>
                    Tidak ada pengaduan ditemukan untuk NIS <strong>$filterNis</strong>.
                  </div>";
        } else {
            echo "<p class='text-muted'>Menampilkan <strong>" . count($rows) . " pengaduan</strong> untuk NIS <strong>" . htmlspecialchars($filterNis) . "</strong></p>";
            echo "<div class='table-responsive'>";
            echo "<table class='table table-bordered table-striped'>";
            echo "<thead style='background-color:#4455DD; color:#fff;'><tr>
                    <th>No</th><th>Kategori</th><th>Lokasi</th>
                    <th>Deskripsi</th><th>Status</th><th>Feedback</th><th>Tanggal</th>
                  </tr></thead><tbody>";
            $no = 1;
            foreach ($rows as $row) {
                $statusStyle = match($row['status']) {
                    'menunggu' => 'background-color:#FFDD44; color:#222;',
                    'proses'   => 'background-color:#33AAEE; color:#fff;',
                    'selesai'  => 'background-color:#BBDD22; color:#222;',
                    default    => 'background-color:#ccc; color:#222;'
                };
                echo "<tr>";
                echo "<td>" . $no++ . "</td>";
                echo "<td>" . htmlspecialchars($row['category_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['location']) . "</td>";
                echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                echo "<td><span class='badge' style='$statusStyle'>" . ucfirst($row['status']) . "</span></td>";
                echo "<td>" . htmlspecialchars($row['feedback'] ?? '-') . "</td>";
                echo "<td>" . date('d-m-Y', strtotime($row['created_at'])) . "</td>";
                echo "</tr>";
            }
            echo "</tbody></table></div>";
        }
    } else {
        echo "<div class='p-4 rounded' style='background-color:#EEF2FF; border-left: 4px solid #4455DD;'>
                Masukkan NIS kamu di atas untuk melihat histori pengaduan.
              </div>";
    }
    ?>
</div>