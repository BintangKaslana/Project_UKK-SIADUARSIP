<?php
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: ' . BASE_PATH . '/admin/login');
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: ' . BASE_PATH . '/admin/manage_admin');
    exit();
}

$stmt = $conn->prepare("DELETE FROM admin WHERE id = ?");
$stmt->execute([$id]);

header('Location: ' . BASE_PATH . '/admin/manage_admin');
exit();