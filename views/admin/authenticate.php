<?php
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_PATH . '/admin/login');
    exit();
}

$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

if (empty($username) || empty($password)) {
    // Reset captcha supaya soal baru muncul
    unset($_SESSION['captcha_num1'], $_SESSION['captcha_num2']);
    header('Location: ' . BASE_PATH . '/admin/login?error=1');
    exit();
}

// ---- Validasi CAPTCHA (backend) ----
$captcha_answer  = intval($_POST['captcha_answer'] ?? -999);
$captcha_correct = intval(($_SESSION['captcha_num1'] ?? 0) + ($_SESSION['captcha_num2'] ?? 0));

// Hapus soal dari session setelah dibaca (one-time use)
unset($_SESSION['captcha_num1'], $_SESSION['captcha_num2']);

if ($captcha_answer !== $captcha_correct) {
    header('Location: ' . BASE_PATH . '/admin/login?error=captcha');
    exit();
}

// ---- Validasi username & password ----
$stmt = $conn->prepare("SELECT id, password, role, full_name FROM admin WHERE username = ? LIMIT 1");
$stmt->execute([$username]);
$row = $stmt->fetch();

if ($row && password_verify($password, $row['password'])) {
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_id']        = $row['id'];
    $_SESSION['admin_username']  = $username;
    $_SESSION['admin_role']      = $row['role'];
    $_SESSION['admin_fullname']  = $row['full_name'] ?: $username;
    header('Location: ' . BASE_PATH . '/admin');
    exit();
}

header('Location: ' . BASE_PATH . '/admin/login?error=1');
exit();
