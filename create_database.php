<?php
require_once __DIR__ . '/public/connection.php';

$dropQueries = [
    "DROP TABLE IF EXISTS aspirasi CASCADE",
    "DROP TABLE IF EXISTS input_aspirasi CASCADE",
    "DROP TABLE IF EXISTS siswa CASCADE",
    "DROP TABLE IF EXISTS kategori CASCADE",
    "DROP TABLE IF EXISTS admin CASCADE",
];

$queries = [
    "CREATE TABLE IF NOT EXISTS admin (
        id       SERIAL PRIMARY KEY,
        username VARCHAR(50)  NOT NULL,
        password VARCHAR(255) NOT NULL
    )",

    "CREATE TABLE IF NOT EXISTS kategori (
        id            SERIAL PRIMARY KEY,
        category_name VARCHAR(130) NOT NULL
    )",

    "CREATE TABLE IF NOT EXISTS siswa (
        nis       INTEGER      PRIMARY KEY,
        full_name VARCHAR(100) NOT NULL,
        class     VARCHAR(20)  NOT NULL
    )",

    "CREATE TABLE IF NOT EXISTS input_aspirasi (
        id          SERIAL PRIMARY KEY,
        nis         INTEGER      NOT NULL REFERENCES siswa(nis)     ON DELETE CASCADE,
        category_id INTEGER      NOT NULL REFERENCES kategori(id)   ON DELETE CASCADE,
        location    VARCHAR(100) NOT NULL,
        description TEXT         NOT NULL,
        bukti_foto  VARCHAR(255) DEFAULT NULL,
        created_at  TIMESTAMP    NOT NULL DEFAULT NOW()
    )",

    "CREATE TABLE IF NOT EXISTS aspirasi (
        aspiration_id INTEGER NOT NULL REFERENCES input_aspirasi(id) ON DELETE CASCADE,
        status        VARCHAR(10)  NOT NULL DEFAULT 'menunggu'
                          CHECK (status IN ('menunggu','proses','selesai')),
        review_status VARCHAR(10)  NOT NULL DEFAULT 'pending'
                          CHECK (review_status IN ('pending','approved','rejected')),
        is_anonim     BOOLEAN      NOT NULL DEFAULT FALSE,
        feedback      TEXT
    )",
];

foreach ($dropQueries as $sql) {
    $conn->exec($sql);
}
foreach ($queries as $sql) {
    $conn->exec($sql);
}

$conn->exec("INSERT INTO kategori (category_name) VALUES ('Fasilitas'), ('Kebersihan'), ('Keamanan')");

$adminUsername = 'admin';
$adminPassword = password_hash('admin123', PASSWORD_BCRYPT);
$stmt = $conn->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
$stmt->execute([$adminUsername, $adminPassword]);

echo "Tabel berhasil dibuat dan data awal berhasil dimasukkan.";