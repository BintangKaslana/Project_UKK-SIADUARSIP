<?php
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';

$nis         = trim($_POST['nis']);
$fullname    = trim($_POST['full_name']);
$class       = trim($_POST['class']);
$categoryId  = trim($_POST['category']);
$location    = trim($_POST['location']);
$description = trim($_POST['description']);

// Upsert siswa (PostgreSQL syntax)
$sqlSiswa = "INSERT INTO siswa (nis, full_name, class) VALUES (?, ?, ?)
             ON CONFLICT (nis) DO UPDATE SET full_name = EXCLUDED.full_name, class = EXCLUDED.class";
$stmt = $conn->prepare($sqlSiswa);
$stmt->execute([$nis, $fullname, $class]);

// Insert input_aspirasi, ambil id yang baru dibuat
$sqlAspirasi = "INSERT INTO input_aspirasi (nis, category_id, location, description) VALUES (?, ?, ?, ?) RETURNING id";
$stmt = $conn->prepare($sqlAspirasi);
$stmt->execute([$nis, $categoryId, $location, $description]);
$row = $stmt->fetch();
$aspiration_id = $row['id'];

// Insert ke aspirasi
$sqlDetail = "INSERT INTO aspirasi (aspiration_id, status, feedback) VALUES (?, 'menunggu', '')";
$stmt = $conn->prepare($sqlDetail);
$stmt->execute([$aspiration_id]);

header('Location: ' . BASE_PATH . '/aspirasi?message=success');
