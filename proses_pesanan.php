<?php
// ========================================================
// API Endpoint Pemrosesan Pesanan Makanan (WebFood)
// ========================================================
header('Content-Type: application/json; charset=utf-8');
require_once 'koneksi.php';

// Terima input JSON dari Fetch API
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

if (!$data) {
    echo json_encode([
        'status' => 'gagal',
        'pesan'  => 'Format data tidak valid.'
    ]);
    exit;
}

// Validasi field utama
$nama_pemesan     = trim($data['nama_pemesan'] ?? '');
$no_telepon       = trim($data['no_telepon'] ?? '');
$alamat_pengiriman = trim($data['alamat_pengiriman'] ?? '');
$catatan          = trim($data['catatan'] ?? '');
$latitude         = trim($data['latitude'] ?? '');
$longitude        = trim($data['longitude'] ?? '');
$metode_pembayaran = trim($data['metode_pembayaran'] ?? 'GoPay');
$items            = $data['items'] ?? [];

if (empty($nama_pemesan) || empty($no_telepon) || empty($alamat_pengiriman)) {
    echo json_encode([
        'status' => 'gagal',
        'pesan'  => 'Mohon lengkapi Nama, No. Telepon/WhatsApp, dan Alamat Pengiriman.'
    ]);
    exit;
}

if (empty($items) || !is_array($items)) {
    echo json_encode([
        'status' => 'gagal',
        'pesan'  => 'Keranjang belanja masih kosong. Silakan pilih menu terlebih dahulu.'
    ]);
    exit;
}

// Hitung total item dan subtotal dari items
$subtotal = 0;
$total_item = 0;
$items_verified = [];

foreach ($items as $item) {
    $makanan_id = intval($item['id'] ?? 0);
    $jumlah     = intval($item['jumlah'] ?? 0);

    if ($makanan_id <= 0 || $jumlah <= 0) {
        continue;
    }

    // Ambil harga asli dari database untuk keamanan
    $stmtCek = $conn->prepare("SELECT id, nama_makanan, harga FROM makanan WHERE id = ? AND tersedia = 1");
    $stmtCek->bind_param("i", $makanan_id);
    $stmtCek->execute();
    $resCek = $stmtCek->get_result();

    if ($menu = $resCek->fetch_assoc()) {
        $harga = floatval($menu['harga']);
        $item_subtotal = $harga * $jumlah;
        $subtotal += $item_subtotal;
        $total_item += $jumlah;

        $items_verified[] = [
            'id'           => $menu['id'],
            'nama_makanan' => $menu['nama_makanan'],
            'harga'        => $harga,
            'jumlah'       => $jumlah,
            'subtotal'     => $item_subtotal
        ];
    }
    $stmtCek->close();
}

if (empty($items_verified)) {
    echo json_encode([
        'status' => 'gagal',
        'pesan'  => 'Menu yang dipilih tidak valid atau sudah tidak tersedia.'
    ]);
    exit;
}

// Hitung ongkir dan diskon
$ongkir = 10000; // Flat promo ongkir Rp 10.000
// Promo gratis ongkir jika belanja di atas Rp 75.000
$diskon = 0;
if ($subtotal >= 75000) {
    $ongkir = 0;
    $diskon = 10000;
}
$total_bayar = $subtotal + $ongkir;

// Buat Kode Pesanan Unik: misal ORD-20261107-7X9B2
$kode_pesanan = 'WF-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

// Mulai transaksi database
$conn->begin_transaction();

try {
    // 1. Simpan ke tabel `pesanan`
    $sqlPesanan = "INSERT INTO pesanan (
        kode_pesanan, nama_pemesan, no_telepon, alamat_pengiriman, catatan,
        latitude, longitude, metode_pembayaran, total_item, subtotal,
        ongkir, diskon, total_bayar, status_pesanan
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Menunggu Konfirmasi')";

    $stmtPesanan = $conn->prepare($sqlPesanan);
    $stmtPesanan->bind_param(
        "sssssssddddds",
        $kode_pesanan,
        $nama_pemesan,
        $no_telepon,
        $alamat_pengiriman,
        $catatan,
        $latitude,
        $longitude,
        $metode_pembayaran,
        $total_item,
        $subtotal,
        $ongkir,
        $diskon,
        $total_bayar
    );

    if (!$stmtPesanan->execute()) {
        throw new Exception("Gagal menyimpan data pesanan: " . $stmtPesanan->error);
    }

    $pesanan_id = $conn->insert_id;
    $stmtPesanan->close();

    // 2. Simpan setiap item ke `pesanan_detail`
    $sqlDetail = "INSERT INTO pesanan_detail (pesanan_id, makanan_id, nama_makanan, harga, jumlah, subtotal) VALUES (?, ?, ?, ?, ?, ?)";
    $stmtDetail = $conn->prepare($sqlDetail);

    foreach ($items_verified as $it) {
        $stmtDetail->bind_param(
            "iisdid",
            $pesanan_id,
            $it['id'],
            $it['nama_makanan'],
            $it['harga'],
            $it['jumlah'],
            $it['subtotal']
        );
        if (!$stmtDetail->execute()) {
            throw new Exception("Gagal menyimpan rincian pesanan: " . $stmtDetail->error);
        }
    }
    $stmtDetail->close();

    // 3. Simpan ke tabel `pesanan_lokasi` jika ada koordinat (menjaga kompatibilitas)
    if (!empty($latitude) && !empty($longitude)) {
        $stmtLokasi = $conn->prepare("INSERT INTO pesanan_lokasi (latitude, longitude) VALUES (?, ?)");
        $stmtLokasi->bind_param("ss", $latitude, $longitude);
        $stmtLokasi->execute();
        $stmtLokasi->close();
    }

    // Commit transaksi
    $conn->commit();

    echo json_encode([
        'status'       => 'sukses',
        'pesan'        => 'Pesanan berhasil dibuat!',
        'kode_pesanan' => $kode_pesanan,
        'redirect'     => 'pesanan_sukses.php?kode=' . urlencode($kode_pesanan)
    ]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        'status' => 'gagal',
        'pesan'  => 'Terjadi kesalahan sistem: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
