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
    header('Location: ' . BASE_PATH . '/admin/manage_admin');
    exit();
}

$id        = isset($_POST['id'])        ? (int)$_POST['id']             : 0;
$username  = isset($_POST['username'])  ? trim($_POST['username'])       : '';
$full_name = isset($_POST['full_name']) ? trim($_POST['full_name'])      : '';
$role      = isset($_POST['role'])      ? trim($_POST['role'])           : 'admin';

if ($id <= 0 || $username === '' || $full_name === '') {
    header('Location: ' . BASE_PATH . '/admin/edit_admin?id=' . $id);
    exit();
}
if (!in_array($role, ['admin', 'head_admin'])) {
    $role = 'admin';
}

$stmt = $conn->prepare("UPDATE admin SET username = ?, full_name = ?, role = ? WHERE id = ?");
$stmt->execute([$username, $full_name, $role, $id]);

// Jika admin mengupdate diri sendiri, refresh session
if ((int)$_SESSION['admin_id'] === $id) {
    $_SESSION['admin_username'] = $username;
    $_SESSION['admin_fullname'] = $full_name;
    $_SESSION['admin_role']     = $role;
}

header('Location: ' . BASE_PATH . '/admin/manage_admin?message=updated');
exit();
