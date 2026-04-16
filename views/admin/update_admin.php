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

$id               = isset($_POST['id'])               ? (int)$_POST['id']             : 0;
$username         = isset($_POST['username'])         ? trim($_POST['username'])       : '';
$full_name        = isset($_POST['full_name'])        ? trim($_POST['full_name'])      : '';
$role             = isset($_POST['role'])             ? trim($_POST['role'])           : 'admin';
$password         = isset($_POST['password'])         ? $_POST['password']             : '';
$password_confirm = isset($_POST['password_confirm']) ? $_POST['password_confirm']     : '';

if ($id <= 0 || $username === '' || $full_name === '') {
    header('Location: ' . BASE_PATH . '/admin/edit_admin?id=' . $id);
    exit();
}
if (!in_array($role, ['admin', 'head_admin'])) {
    $role = 'admin';
}

// Update password jika diisi
if ($password !== '') {
    if ($password !== $password_confirm) {
        header('Location: ' . BASE_PATH . '/admin/edit_admin?id=' . $id . '&error=password_mismatch');
        exit();
    }
    if (strlen($password) < 6) {
        header('Location: ' . BASE_PATH . '/admin/edit_admin?id=' . $id . '&error=password_short');
        exit();
    }
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("UPDATE admin SET username = ?, full_name = ?, role = ?, password = ? WHERE id = ?");
    $stmt->execute([$username, $full_name, $role, $hash, $id]);
} else {
    $stmt = $conn->prepare("UPDATE admin SET username = ?, full_name = ?, role = ? WHERE id = ?");
    $stmt->execute([$username, $full_name, $role, $id]);
}

// Jika admin mengupdate diri sendiri, refresh session
if ((int)$_SESSION['admin_id'] === $id) {
    $_SESSION['admin_username'] = $username;
    $_SESSION['admin_fullname'] = $full_name;
    $_SESSION['admin_role']     = $role;
}

header('Location: ' . BASE_PATH . '/admin/manage_admin?message=updated');
exit();
