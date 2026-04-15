<?php
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';

// Validasi method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_PATH . '/aspirasi');
    exit();
}

$nis         = trim($_POST['nis'] ?? '');
$fullname    = trim($_POST['full_name'] ?? '');
$class       = trim($_POST['class'] ?? '');
$categoryId  = trim($_POST['category'] ?? '');
$location    = trim($_POST['location'] ?? '');
$description = trim($_POST['description'] ?? '');

// Validasi field tidak boleh kosong
if (empty($nis) || empty($fullname) || empty($class) || empty($categoryId) || empty($location) || empty($description)) {
    header('Location: ' . BASE_PATH . '/aspirasi?message=error');
    exit();
}

// Validasi tipe data numerik
if (!is_numeric($nis) || !is_numeric($categoryId)) {
    header('Location: ' . BASE_PATH . '/aspirasi?message=error');
    exit();
}

/* The code block you provided is checking for spam prevention by limiting the number of aspirations a
user can submit in a day. Here's a breakdown of what it does: */
$LIMIT_HARIAN = 3;
$stmtSpam = $conn->prepare(
    "SELECT COUNT(*) AS total
     FROM input_aspirasi
     WHERE nis = ?
       AND DATE(created_at) = CURRENT_DATE"
);
$stmtSpam->execute([$nis]);
$rowSpam = $stmtSpam->fetch();

/* This code block is checking for spam prevention by limiting the number of aspirations a user can
submit in a day. Here's a breakdown of what it does: */
if ((int)$rowSpam['total'] >= $LIMIT_HARIAN) {
    header('Location: ' . BASE_PATH . '/aspirasi?message=spam_limit&nis=' . urlencode($nis));
    exit();
}
// ─────────────────────────────────────────────────────────────────────────────

// Handle upload foto bukti (opsional)
$bukti_foto = null;
if (!empty($_FILES['bukti_foto']['name'])) {
    $file     = $_FILES['bukti_foto'];
    $maxSize  = 2 * 1024 * 1024; // 2MB
    $allowed  = ['image/jpeg', 'image/png', 'image/webp'];

    // Validasi ukuran & tipe MIME
    $finfo    = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if ($file['size'] > $maxSize || !in_array($mimeType, $allowed)) {
        header('Location: ' . BASE_PATH . '/aspirasi?message=foto_error');
        exit();
    }

    // Simpan ke folder uploads/bukti/
    $uploadDir = dirname(__DIR__, 2) . '/public/uploads/bukti/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $ext        = pathinfo($file['name'], PATHINFO_EXTENSION);
    $namaFile   = 'bukti_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $targetPath = $uploadDir . $namaFile;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        header('Location: ' . BASE_PATH . '/aspirasi?message=error');
        exit();
    }

    $bukti_foto = $namaFile;
}

// Cek apakah NIS sudah terdaftar
$stmtCek = $conn->prepare("SELECT nis, full_name, class FROM siswa WHERE nis = ?");
$stmtCek->execute([$nis]);
$siswaTerdaftar = $stmtCek->fetch();

if ($siswaTerdaftar) {
    // NIS sudah ada — pastikan nama & kelas cocok, jangan izinkan overwrite
    $namaDb     = strtolower(trim($siswaTerdaftar['full_name']));
    $namaInput  = strtolower(trim($fullname));
    $kelasDb    = strtolower(trim($siswaTerdaftar['class']));
    $kelasInput = strtolower(trim($class));

    if ($namaDb !== $namaInput || $kelasDb !== $kelasInput) {
        // NIS sudah dimiliki siswa lain, tolak
        header('Location: ' . BASE_PATH . '/aspirasi?message=nis_conflict&nis=' . urlencode($nis));
        exit();
    }
    
} else {
    
    $sqlSiswa = "INSERT INTO siswa (nis, full_name, class) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sqlSiswa);
    $stmt->execute([$nis, $fullname, $class]);
}

/* The provided code snippet is responsible for inserting data into two database tables:
`input_aspirasi` and `aspirasi`. */

$sqlAspirasi = "INSERT INTO input_aspirasi (nis, category_id, location, description, bukti_foto)
                VALUES (?, ?, ?, ?, ?) RETURNING id";
$stmt = $conn->prepare($sqlAspirasi);
$stmt->execute([$nis, $categoryId, $location, $description, $bukti_foto]);
$row = $stmt->fetch();
$aspiration_id = $row['id'];


$sqlDetail = "INSERT INTO aspirasi (aspiration_id, status, feedback) VALUES (?, 'menunggu', '')";
$stmt = $conn->prepare($sqlDetail);
$stmt->execute([$aspiration_id]);

header('Location: ' . BASE_PATH . '/aspirasi?message=success');