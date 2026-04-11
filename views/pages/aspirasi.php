<?php
$stmt = $conn->query("SELECT id, category_name FROM kategori ORDER BY id");
$result = $stmt->fetchAll();
?>

<div class="container py-5" style="max-width: 700px;">
    <h2 class="fw-bold mb-1" style="color: #4455DD;">Sampaikan Aspirasi</h2>
    <p class="text-muted mb-4">Isi form di bawah ini untuk menyampaikan pengaduanmu.</p>

    <?php if (isset($_GET['message']) && $_GET['message'] === 'success') { ?>
        <div class="alert" style="background-color: #BBDD22; color: #222; border: none;">
            ✅ Pengaduan berhasil dikirim!
        </div>
    <?php } ?>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="/ukk_bintang_26/save_aspirasi" method="post" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">NIS</label>
                    <input type="number" name="nis" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Nama Lengkap</label>
                    <input type="text" name="full_name" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Kelas</label>
                    <input type="text" name="class" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Kategori</label>
                    <select name="category" class="form-select" required>
                        <?php foreach ($result as $row) { ?>
                            <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['category_name']) ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Lokasi</label>
                    <input type="text" name="location" class="form-control" required>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Deskripsi</label>
                    <textarea name="description" class="form-control" rows="5" required></textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn px-4 py-2" 
                            style="background-color: #4455DD; color: #fff; border: none;">
                        Kirim Pengaduan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>