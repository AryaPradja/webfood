<?php
// ========================================================
// Konfigurasi Koneksi Database WebFood
// ========================================================
$host = "localhost";
$user = "root"; // Sesuaikan jika ada password di XAMPP Anda
$pass = "";     
$db   = "db_antarmakanan";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode([
        "status" => "gagal",
        "pesan"  => "Koneksi database gagal: " . $conn->connect_error
    ]));
}

// Set karakter encoding ke utf8mb4 agar mendukung karakter khusus & emoji
$conn->set_charset("utf8mb4");

// Set timezone ke Asia/Jakarta
date_default_timezone_set('Asia/Jakarta');
?>