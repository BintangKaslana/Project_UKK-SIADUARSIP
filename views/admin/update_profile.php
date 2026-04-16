<?php
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: ' . BASE_PATH . '/admin/login');
    exit();
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_PATH . '/admin/profile');
    exit();
}

$id        = (int)$_SESSION['admin_id'];
$full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
$username  = isset($_POST['username'])  ? trim($_POST['username'])  : '';

if ($full_name === '' || $username === '') {
    header('Location: ' . BASE_PATH . '/admin/profile?error=empty');
    exit();
}

$stmt = $conn->prepare("UPDATE admin SET full_name = ?, username = ? WHERE id = ?");
$stmt->execute([$full_name, $username, $id]);

// Refresh session
$_SESSION['admin_fullname'] = $full_name;
$_SESSION['admin_username'] = $username;

header('Location: ' . BASE_PATH . '/admin/profile?success=profile_updated');
exit();
