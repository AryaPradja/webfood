<?php
// ========================================================
// Auto-Setup & Migration Script for WebFood
// ========================================================
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_antarmakanan";

// 1. Buat koneksi awal ke MySQL Server
$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die("Koneksi ke MySQL gagal: " . $conn->connect_error);
}

// 2. Pastikan database dibuat jika belum ada
$conn->query("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$conn->select_db($db);

// 3. Baca dan jalankan file database.sql
$sqlFile = __DIR__ . '/database.sql';
if (!file_exists($sqlFile)) {
    die("File database.sql tidak ditemukan di: " . $sqlFile);
}

$sqlCommands = file_get_contents($sqlFile);

// Eksekusi multi query
if ($conn->multi_query($sqlCommands)) {
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->more_results() && $conn->next_result());
    
    $sukses = true;
    $pesan = "Database dan seed data berhasil diinisialisasi!";
} else {
    $sukses = false;
    $pesan = "Gagal mengeksekusi SQL: " . $conn->error;
}

// Hitung total makanan & kategori
$jmlKategori = 0;
$jmlMakanan = 0;
if ($sukses) {
    if ($res = $conn->query("SELECT COUNT(*) AS total FROM kategori")) {
        $jmlKategori = $res->fetch_assoc()['total'];
    }
    if ($res = $conn->query("SELECT COUNT(*) AS total FROM makanan")) {
        $jmlMakanan = $res->fetch_assoc()['total'];
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Database WebFood</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .setup-card { max-width: 600px; margin: 80px auto; border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        .btn-brand { background-color: #EE2737; color: white; font-weight: 600; border-radius: 8px; }
        .btn-brand:hover { background-color: #d81b2a; color: white; }
    </style>
</head>
<body>
<div class="container">
    <div class="card setup-card p-4 text-center">
        <div class="mb-3">
            <i class="fa-solid fa-utensils fa-3x" style="color: #EE2737;"></i>
        </div>
        <h3 class="fw-bold mb-2">WebFood Database Setup</h3>
        <p class="text-muted">Inisialisasi Tabel & Menu Makanan Ala GoFood</p>

        <?php if ($sukses): ?>
            <div class="alert alert-success d-flex align-items-center justify-content-center gap-2 mb-4" role="alert">
                <i class="fa-solid fa-circle-check fa-lg"></i>
                <div><?= htmlspecialchars($pesan); ?></div>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-6">
                    <div class="border rounded p-3 bg-light">
                        <div class="fs-4 fw-bold text-dark"><?= $jmlKategori; ?></div>
                        <div class="text-muted small">Kategori Makanan</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="border rounded p-3 bg-light">
                        <div class="fs-4 fw-bold text-dark"><?= $jmlMakanan; ?></div>
                        <div class="text-muted small">Menu Makanan & Minuman</div>
                    </div>
                </div>
            </div>
            <a href="index.php" class="btn btn-brand btn-lg w-100 py-3 shadow-sm">
                <i class="fa-solid fa-arrow-left me-2"></i> Masuk ke Halaman Utama WebFood
            </a>
        <?php else: ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($pesan); ?>
            </div>
            <a href="setup_database.php" class="btn btn-secondary">Coba Lagi</a>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
