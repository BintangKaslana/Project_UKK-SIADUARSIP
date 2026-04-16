<?php
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: ' . BASE_PATH . '/admin/login');
    exit();
}
if (($_SESSION['admin_role'] ?? '') !== 'head_admin') {
    header('Location: ' . BASE_PATH . '/admin?error=akses');
    exit();
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_PATH . '/admin/add_admin');
    exit();
}
$username  = isset($_POST['username'])  ? trim($_POST['username'])  : '';
$full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
$role      = isset($_POST['role'])      ? trim($_POST['role'])      : 'admin';

// Validasi
if ($username === '' || $full_name === '') {
    header('Location: ' . BASE_PATH . '/admin/add_admin');
    exit();
}
// Pastikan role valid
if (!in_array($role, ['admin', 'head_admin'])) {
    $role = 'admin';
}

$defaultPassword = '12345';
$hash = password_hash($defaultPassword, PASSWORD_BCRYPT);

$stmt = $conn->prepare("INSERT INTO admin (username, password, full_name, role) VALUES (?, ?, ?, ?)");
$stmt->execute([$username, $hash, $full_name, $role]);

header('Location: ' . BASE_PATH . '/admin/manage_admin?message=saved');
exit();
