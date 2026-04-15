<?php
$stmt = $conn->query("SELECT id, category_name FROM kategori ORDER BY id");
$result = $stmt->fetchAll();
?>

<div class="max-w-2xl mx-auto px-4 py-6">
    <div class="mb-4">
        <p class="text-[#33AAEE] text-xs font-bold tracking-widest uppercase mb-1">✦ Form Pengaduan</p>
        <h2 class="text-2xl font-bold text-[#4455DD] mb-1">Sampaikan Aspirasi</h2>
        <p class="text-gray-500 text-sm">Isi form di bawah ini untuk menyampaikan pengaduanmu.</p>
    </div>

    <?php if (isset($_GET['message']) && $_GET['message'] === 'success') { ?>
        <div class="bg-[#BBDD22] text-gray-800 px-4 py-2 rounded-lg mb-3 font-semibold text-sm">
            ✅ Pengaduan berhasil dikirim!
        </div>
    <?php } ?>

    <?php if (isset($_GET['message']) && $_GET['message'] === 'nis_conflict') { ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded-lg mb-3 text-sm font-semibold">
            ❌ NIS <strong><?= htmlspecialchars($_GET['nis'] ?? '') ?></strong> sudah terdaftar atas nama siswa lain. Pastikan NIS dan Nama Lengkap yang kamu masukkan sudah sesuai dengan identitas kamu.
        </div>
    <?php } ?>

    <?php if (isset($_GET['message']) && $_GET['message'] === 'error') { ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded-lg mb-3 text-sm font-semibold">
            ❌ Terjadi kesalahan. Pastikan semua field sudah diisi dengan benar.
        </div>
    <?php } ?>

    <?php if (isset($_GET['message']) && $_GET['message'] === 'foto_error') { ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded-lg mb-3 text-sm font-semibold">
            ❌ Format file tidak didukung atau ukuran file terlalu besar (maks 2MB). Gunakan JPG, PNG, atau WEBP.
        </div>
    <?php } ?>

    <?php if (isset($_GET['message']) && $_GET['message'] === 'spam_limit') { ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded-lg mb-3 text-sm font-semibold">
            ⛔ NIS <strong><?= htmlspecialchars($_GET['nis'] ?? '') ?></strong> sudah mencapai batas maksimal <strong>3 pengaduan hari ini</strong>. Silakan coba lagi besok.
        </div>
    <?php } ?>

    <div class="bg-white rounded-xl shadow-lg border-t-4 border-[#4455DD] p-5">
        <form action="<?= BASE_PATH ?>/save_aspirasi" method="post" enctype="multipart/form-data" class="space-y-3">
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">NIS</label>
                    <input type="number" name="nis" required
                           class="w-full border-2 border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:border-[#33AAEE] transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="full_name" required
                           class="w-full border-2 border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:border-[#33AAEE] transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Kelas</label>
                    <input type="text" name="class" required
                           class="w-full border-2 border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:border-[#33AAEE] transition">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Kategori</label>
                    <select name="category" required
                            class="w-full border-2 border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:border-[#33AAEE] transition">
                        <?php foreach ($result as $row) { ?>
                            <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['category_name']) ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Lokasi</label>
                    <input type="text" name="location" required
                           class="w-full border-2 border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:border-[#33AAEE] transition">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" rows="3" required
                          class="w-full border-2 border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:border-[#33AAEE] transition"></textarea>
            </div>

            <!-- Upload Bukti Foto -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                            Bukti Foto <span class="text-gray-400 font-normal">(opsional, maks 2MB — JPG/PNG/WEBP)</span>
                        </label>
                        <label id="foto-label"
                            class="inline-flex items-center text-xs cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M12 12V4m0 0L8 8m4-4l4 4"/>
                            </svg>
                            <span id="foto-nama" class="ml-1">Pilih</span>
                            <input type="file" name="bukti_foto" id="bukti_foto" accept="image/jpeg,image/png,image/webp"
                                class="hidden" onchange="tampilNamaFile(this)">
                        </label>
                    </div>

            <div class="bg-[#FFDD44]/20 border-l-4 border-[#FFDD44] px-3 py-2 rounded text-xs text-gray-700">
                ⚠️ Pastikan data yang kamu isi sudah benar sebelum mengirim. Batas pengaduan: <strong>3x per hari</strong> per NIS.
            </div>
            <button type="submit"
                    class="w-full bg-[#4455DD] text-white py-2 rounded-lg font-semibold hover:bg-[#33AAEE] transition">
                Kirim Pengaduan
            </button>
        </form>
    </div>
</div>

<script>
function tampilNamaFile(input) {
    const label = document.getElementById('foto-nama');
    if (input.files && input.files[0]) {
        label.textContent = input.files[0].name;
        label.classList.add('text-[#4455DD]', 'font-semibold');
    } else {
        label.textContent = 'Pilih file gambar...';
        label.classList.remove('text-[#4455DD]', 'font-semibold');
    }
}
</script>