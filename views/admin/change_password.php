<?php
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: ' . BASE_PATH . '/admin/login');
    exit();
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_PATH . '/admin/profile');
    exit();
}

$id               = (int)$_SESSION['admin_id'];
$old_password     = isset($_POST['old_password'])        ? $_POST['old_password']        : '';
$new_password     = isset($_POST['new_password'])        ? $_POST['new_password']        : '';
$new_password_confirm = isset($_POST['new_password_confirm']) ? $_POST['new_password_confirm'] : '';

if ($old_password === '' || $new_password === '' || $new_password_confirm === '') {
    header('Location: ' . BASE_PATH . '/admin/profile?error=empty');
    exit();
}
if ($new_password !== $new_password_confirm) {
    header('Location: ' . BASE_PATH . '/admin/profile?error=password_mismatch');
    exit();
}
if (strlen($new_password) < 6) {
    header('Location: ' . BASE_PATH . '/admin/profile?error=password_short');
    exit();
}

// Verifikasi password lama
$stmt = $conn->prepare("SELECT password FROM admin WHERE id = ?");
$stmt->execute([$id]);
$row = $stmt->fetch();
if (!$row || !password_verify($old_password, $row['password'])) {
    header('Location: ' . BASE_PATH . '/admin/profile?error=wrong_password');
    exit();
}

$hash = password_hash($new_password, PASSWORD_BCRYPT);
$stmt = $conn->prepare("UPDATE admin SET password = ? WHERE id = ?");
$stmt->execute([$hash, $id]);

header('Location: ' . BASE_PATH . '/admin/profile?success=password_changed');
exit();
