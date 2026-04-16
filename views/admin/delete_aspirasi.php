<?php
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: ' . BASE_PATH . '/admin/login');
    exit();
}
// Hanya Head Admin yang bisa hapus aspirasi
if (($_SESSION['admin_role'] ?? '') !== 'head_admin') {
    header('Location: ' . BASE_PATH . '/admin?error=akses');
    exit();
}

$aspiration_id = isset($_GET['aspiration_id']) ? (int)$_GET['aspiration_id'] : 0;
if ($aspiration_id <= 0) {
    header('Location: ' . BASE_PATH . '/admin');
    exit();
}

// Ambil nama file foto bukti sebelum dihapus (kalau ada)
$stmtFoto = $conn->prepare("SELECT bukti_foto FROM input_aspirasi WHERE id = (SELECT aspiration_id FROM aspirasi WHERE aspiration_id = ?)");
$stmtFoto->execute([$aspiration_id]);
$fotoRow = $stmtFoto->fetch();

// Hapus data — CASCADE akan hapus baris di aspirasi otomatis
$stmt = $conn->prepare("DELETE FROM input_aspirasi WHERE id = ?");
$stmt->execute([$aspiration_id]);

// Hapus file foto dari server kalau ada
if (!empty($fotoRow['bukti_foto'])) {
    $fotoPath = dirname(__DIR__, 2) . '/public/uploads/bukti/' . $fotoRow['bukti_foto'];
    if (file_exists($fotoPath)) {
        unlink($fotoPath);
    }
}

header('Location: ' . BASE_PATH . '/admin?message=deleted');
exit();
