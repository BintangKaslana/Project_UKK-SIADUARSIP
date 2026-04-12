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

    <div class="bg-white rounded-xl shadow-lg border-t-4 border-[#4455DD] p-5">
        <form action="/ukk_bintang_26/save_aspirasi" method="post" class="space-y-3">
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
            <div class="bg-[#FFDD44]/20 border-l-4 border-[#FFDD44] px-3 py-2 rounded text-xs text-gray-700">
                ⚠️ Pastikan data yang kamu isi sudah benar sebelum mengirim.
            </div>
            <button type="submit"
                    class="w-full bg-[#4455DD] text-white py-2 rounded-lg font-semibold hover:bg-[#33AAEE] transition">
                Kirim Pengaduan
            </button>
        </form>
    </div>
</div>