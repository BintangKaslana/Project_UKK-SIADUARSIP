<?php
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';

// Validasi session admin
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: ' . BASE_PATH . '/admin/login');
    exit();
}

// Validasi method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_PATH . '/admin');
    exit();
}

$aspiration_id = intval($_POST['aspiration_id'] ?? 0);
$status        = trim($_POST['status'] ?? '');
$feedback      = trim($_POST['feedback'] ?? '');

// Validasi aspiration_id
if ($aspiration_id <= 0) {
    header('Location: ' . BASE_PATH . '/admin');
    exit();
}

// Validasi nilai status — hanya boleh nilai yang diizinkan
$allowedStatus = ['menunggu', 'proses', 'selesai'];
if (!in_array($status, $allowedStatus)) {
    header('Location: ' . BASE_PATH . '/admin');
    exit();
}

/* This block of code is preparing and executing a SQL query to update the status and feedback fields
in the "aspirasi" table in the database. */
$stmt = $conn->prepare("UPDATE aspirasi SET status = ?, feedback = ?, feedback_by = ? WHERE aspiration_id = ?");
if ($stmt->execute([$status, $feedback, $_SESSION['admin_id'], $aspiration_id])) {
    header('Location: ' . BASE_PATH . '/admin?message=updated');
} else {
    die('Error updating aspiration.');
}
