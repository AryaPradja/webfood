<?php
header('Content-Type: application/json; charset=utf-8');
require 'koneksi.php';

// Memastikan data diterima dalam format JSON
$data = json_decode(file_get_contents("php://input"));

if(isset($data->latitude) && isset($data->longitude)) {
    $lat = (string)$data->latitude;
    $lng = (string)$data->longitude;

    // Gunakan Prepared Statement untuk mencegah SQL Injection
    $stmt = $conn->prepare("INSERT INTO pesanan_lokasi (latitude, longitude) VALUES (?, ?)");
    $stmt->bind_param("ss", $lat, $lng);

    if ($stmt->execute()) {
        echo json_encode(["status" => "sukses", "pesan" => "Lokasi berhasil disimpan."]);
    } else {
        echo json_encode(["status" => "gagal", "pesan" => "Gagal menyimpan ke database: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "gagal", "pesan" => "Data lokasi tidak lengkap."]);
}

$conn->close();
?>