<?php
require_once __DIR__ . '/public/connection.php';

// ==============================
// HAPUS DATA LAMA (urutan penting karena ada foreign key)
// ==============================
$conn->exec("DELETE FROM aspirasi");
$conn->exec("DELETE FROM input_aspirasi");
$conn->exec("DELETE FROM siswa");

echo "Data lama berhasil dihapus.<br>";

// ==============================
// DATA SISWA (15 siswa)
// ==============================
$siswaList = [
    [1001, 'Ahmad Fauzi',       'XII RPL 1'],
    [1002, 'Budi Santoso',      'XII RPL 1'],
    [1003, 'Citra Dewi',        'XII RPL 1'],
    [1004, 'Dian Pratama',      'XII RPL 2'],
    [1005, 'Eka Rahmawati',     'XII RPL 2'],
    [1006, 'Fajar Nugroho',     'XII RPL 2'],
    [1007, 'Gita Permatasari',  'XII TKJ 1'],
    [1008, 'Hendra Kurniawan',  'XII TKJ 1'],
    [1009, 'Indah Lestari',     'XII TKJ 1'],
    [1010, 'Joko Widodo',       'XII TKJ 2'],
    [1011, 'Kartika Sari',      'XII TKJ 2'],
    [1012, 'Luthfi Hakim',      'XII MM 1'],
    [1013, 'Maya Anggraini',    'XII MM 1'],
    [1014, 'Nanda Putra',       'XII MM 1'],
    [1015, 'Olivia Putri',      'XII MM 2'],
];

$stmtSiswa = $conn->prepare("INSERT INTO siswa (nis, full_name, class) VALUES (?, ?, ?)");
foreach ($siswaList as $s) {
    $stmtSiswa->execute($s);
}

echo "15 siswa berhasil dimasukkan.<br>";

// ==============================
// DATA ASPIRASI (20 aspirasi)
// Kategori: 1=Fasilitas, 2=Kebersihan, 3=Keamanan
// ==============================
$aspirasiList = [
    // [nis, category_id, lokasi, deskripsi, status, feedback]
    [1001, 1, 'Lab Komputer',       'Komputer nomor 5 rusak, tidak bisa menyala.',                          'selesai',  'Sudah diperbaiki oleh teknisi pada minggu lalu.'],
    [1001, 2, 'Toilet Lantai 2',    'Toilet lantai 2 sering bau dan tidak ada sabun cuci tangan.',          'proses',   'Sedang dikoordinasikan dengan petugas kebersihan.'],
    [1002, 1, 'Perpustakaan',       'AC perpustakaan mati sudah 2 minggu, sangat panas saat belajar.',      'proses',   'Teknisi sudah dijadwalkan datang minggu ini.'],
    [1002, 3, 'Parkiran',           'Parkiran motor tidak ada atapnya sehingga kendaraan kehujanan.',        'menunggu', ''],
    [1003, 2, 'Kantin',             'Area kantin kotor dan banyak sampah berserakan setelah jam istirahat.', 'selesai',  'Jadwal piket kantin sudah diperbarui.'],
    [1003, 1, 'Ruang Kelas XII RPL 1', 'Proyektor di kelas kami sering error dan gambarnya buram.',         'menunggu', ''],
    [1004, 3, 'Gerbang Sekolah',    'Gerbang sekolah tidak terkunci setelah jam 17.00.',                    'selesai',  'Sudah disampaikan ke satpam, sekarang selalu terkunci.'],
    [1004, 2, 'WC Siswa Putra',     'Kran air di WC putra banyak yang bocor dan tidak mengalir.',           'proses',   'Tim maintenance sedang menangani perbaikan pipa.'],
    [1005, 1, 'Lab Komputer',       'Mouse dan keyboard banyak yang rusak di lab komputer.',                 'menunggu', ''],
    [1005, 2, 'Lorong Gedung B',    'Lorong gedung B jarang disapu, banyak debu dan sampah daun.',          'menunggu', ''],
    [1006, 1, 'Ruang Kelas XII RPL 2', 'Kipas angin di kelas mati, sangat panas saat siang hari.',         'selesai',  'Kipas sudah diganti dengan yang baru.'],
    [1006, 3, 'Lapangan Olahraga',  'Lampu lapangan olahraga mati sehingga gelap saat sore hari.',          'proses',   'Penggantian lampu sedang dalam proses pengadaan.'],
    [1007, 1, 'Perpustakaan',       'Banyak buku referensi RPL yang sudah usang dan perlu diperbarui.',     'menunggu', ''],
    [1008, 2, 'Musholla',           'Musholla kurang bersih dan karpet sajadah perlu dicuci.',              'selesai',  'Karpet sudah dicuci dan jadwal bersih musholla diperbarui.'],
    [1009, 3, 'Parkiran',           'Sering ada orang tidak dikenal masuk ke area parkiran sekolah.',       'proses',   'Penambahan CCTV di area parkiran sedang diusulkan.'],
    [1010, 1, 'Lab Komputer',       'Koneksi internet di lab lambat, mengganggu praktik berbasis online.',  'menunggu', ''],
    [1011, 2, 'Toilet Lantai 1',    'Toilet lantai 1 pintunya rusak dan tidak bisa dikunci dari dalam.',    'selesai',  'Pintu toilet sudah diperbaiki oleh tim sarana.'],
    [1012, 1, 'Ruang Kelas XII MM 1', 'Stop kontak di kelas banyak yang tidak berfungsi.',                 'menunggu', ''],
    [1013, 3, 'Gerbang Sekolah',    'Tidak ada petugas jaga di gerbang saat jam pulang sekolah.',           'proses',   'Jadwal jaga satpam sedang disesuaikan.'],
    [1014, 2, 'Kantin',             'Saluran air di belakang kantin tersumbat dan menimbulkan bau.',        'menunggu', ''],
];

$stmtInput = $conn->prepare(
    "INSERT INTO input_aspirasi (nis, category_id, location, description) VALUES (?, ?, ?, ?) RETURNING id"
);
$stmtAspirasi = $conn->prepare(
    "INSERT INTO aspirasi (aspiration_id, status, feedback) VALUES (?, ?, ?)"
);

foreach ($aspirasiList as $a) {
    [$nis, $catId, $lokasi, $deskripsi, $status, $feedback] = $a;

    $stmtInput->execute([$nis, $catId, $lokasi, $deskripsi]);
    $row = $stmtInput->fetch();
    $aspiration_id = $row['id'];

    $stmtAspirasi->execute([$aspiration_id, $status, $feedback]);
}

echo "20 aspirasi berhasil dimasukkan.<br>";
echo "<br><strong style='color:green'>✅ Seeder selesai! Database siap digunakan.</strong>";