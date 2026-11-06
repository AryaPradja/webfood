<?php
// ========================================================
// Halaman Konfirmasi & Pelacakan Pesanan (GoFood Style)
// ========================================================
require_once 'koneksi.php';

$kode_pesanan = trim($_GET['kode'] ?? '');

if (empty($kode_pesanan)) {
    header("Location: index.php");
    exit;
}

// Ambil data pesanan
$stmt = $conn->prepare("SELECT * FROM pesanan WHERE kode_pesanan = ?");
$stmt->bind_param("s", $kode_pesanan);
$stmt->execute();
$res = $stmt->get_result();
$pesanan = $res->fetch_assoc();
$stmt->close();

if (!$pesanan) {
    die("Pesanan dengan kode " . htmlspecialchars($kode_pesanan) . " tidak ditemukan.");
}

// Ambil rincian item pesanan
$stmtItems = $conn->prepare("SELECT * FROM pesanan_detail WHERE pesanan_id = ?");
$stmtItems->bind_param("i", $pesanan['id']);
$stmtItems->execute();
$items = $stmtItems->get_result()->fetch_all(MYSQLI_ASSOC);
$stmtItems->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pesanan #<?= htmlspecialchars($pesanan['kode_pesanan']); ?> - WebFood</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --gofood-red: #EE2737;
            --gofood-dark: #1C1C1E;
            --gojek-green: #00AA13;
            --bg-light: #F8F9FA;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-light);
            color: #333;
            padding-bottom: 60px;
        }

        .navbar-brand-custom {
            font-weight: 800;
            font-size: 1.4rem;
            color: var(--gofood-red) !important;
            letter-spacing: -0.5px;
        }

        .card-custom {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            background: #fff;
        }

        .status-badge {
            background-color: #e8f5e9;
            color: var(--gojek-green);
            font-weight: 700;
            padding: 8px 16px;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .pulse-dot {
            width: 10px;
            height: 10px;
            background-color: var(--gojek-green);
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 0 rgba(0, 170, 19, 0.7);
            animation: pulse-green 1.5s infinite;
        }

        @keyframes pulse-green {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(0, 170, 19, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(0, 170, 19, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(0, 170, 19, 0); }
        }

        .step-progress {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin: 30px 0 20px;
        }

        .step-progress::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 20px;
            right: 20px;
            height: 4px;
            background: #e0e0e0;
            z-index: 1;
        }

        .step-item {
            position: relative;
            z-index: 2;
            text-align: center;
            flex: 1;
        }

        .step-icon {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #e0e0e0;
            color: #777;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px;
            font-size: 1.1rem;
            transition: all 0.3s;
        }

        .step-item.active .step-icon {
            background: var(--gofood-red);
            color: white;
            box-shadow: 0 4px 12px rgba(238, 39, 55, 0.35);
        }

        .step-item.completed .step-icon {
            background: var(--gojek-green);
            color: white;
        }

        .step-title {
            font-size: 0.8rem;
            font-weight: 600;
            color: #666;
        }

        .driver-card {
            background: linear-gradient(135deg, #1C1C1E, #2C2C2E);
            color: white;
            border-radius: 16px;
            padding: 20px;
        }

        .driver-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
        }

        .btn-brand {
            background-color: var(--gofood-red);
            color: white;
            font-weight: 700;
            border-radius: 10px;
            padding: 12px 24px;
            border: none;
            transition: all 0.2s;
        }

        .btn-brand:hover {
            background-color: #d81b2a;
            color: white;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 navbar-brand-custom" href="index.php">
                <i class="fa-solid fa-burger"></i> WebFood
            </a>
            <a href="index.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fa-solid fa-house me-1"></i> Ke Beranda
            </a>
        </div>
    </nav>

    <div class="container my-4" style="max-width: 820px;">
        <!-- Header Status -->
        <div class="card card-custom p-4 mb-4 text-center">
            <div class="mb-3">
                <div class="status-badge">
                    <span class="pulse-dot"></span>
                    <span>Pesanan Sedang Diproses</span>
                </div>
            </div>
            <h3 class="fw-bold mb-1">Hore! Pesananmu Berhasil Diterima</h3>
            <p class="text-muted mb-3">Kode Pesanan: <strong class="text-dark">#<?= htmlspecialchars($pesanan['kode_pesanan']); ?></strong></p>
            <div class="alert alert-warning py-2 px-3 d-inline-block mx-auto rounded-pill mb-0" style="font-size: 0.9rem;">
                <i class="fa-solid fa-clock me-1"></i> Estimasi Sampai: <strong>20 - 30 Menit</strong>
            </div>

            <!-- Tracker Stepper -->
            <div class="step-progress">
                <div class="step-item completed">
                    <div class="step-icon"><i class="fa-solid fa-check"></i></div>
                    <div class="step-title">Diterima</div>
                </div>
                <div class="step-item active">
                    <div class="step-icon"><i class="fa-solid fa-fire-burner"></i></div>
                    <div class="step-title">Dimasak</div>
                </div>
                <div class="step-item">
                    <div class="step-icon"><i class="fa-solid fa-motorcycle"></i></div>
                    <div class="step-title">Diantar</div>
                </div>
                <div class="step-item">
                    <div class="step-icon"><i class="fa-solid fa-circle-check"></i></div>
                    <div class="step-title">Sampai</div>
                </div>
            </div>
        </div>

        <!-- Driver Simulation Card -->
        <div class="driver-card mb-4 shadow-sm">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <img src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=120&auto=format&fit=crop&q=80" alt="Driver" class="driver-avatar">
                    <div>
                        <div class="badge bg-success mb-1">Driver GoFood Ditugaskan</div>
                        <h5 class="fw-bold mb-0">Budi Santoso</h5>
                        <p class="text-white-50 small mb-0">Honda Vario 125 • <span class="badge bg-secondary">B 3912 KDA</span></p>
                    </div>
                </div>
                <div class="text-end">
                    <div class="text-warning fw-bold"><i class="fa-solid fa-star"></i> 4.95</div>
                    <div class="text-white-50 small">1.200+ Trip Selesai</div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Informasi Pengiriman -->
            <div class="col-md-6">
                <div class="card card-custom p-4 h-100">
                    <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-location-dot text-danger"></i> Tujuan Pengantaran
                    </h5>
                    <div class="mb-3">
                        <div class="text-muted small">Penerima:</div>
                        <div class="fw-bold"><?= htmlspecialchars($pesanan['nama_pemesan']); ?> (<?= htmlspecialchars($pesanan['no_telepon']); ?>)</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Alamat Lengkap:</div>
                        <div><?= nl2br(htmlspecialchars($pesanan['alamat_pengiriman'])); ?></div>
                    </div>
                    <?php if (!empty($pesanan['latitude']) && !empty($pesanan['longitude'])): ?>
                        <div class="mb-3">
                            <span class="badge bg-light text-dark border p-2">
                                <i class="fa-solid fa-crosshairs text-success me-1"></i> Koordinat GPS: 
                                <?= htmlspecialchars($pesanan['latitude']); ?>, <?= htmlspecialchars($pesanan['longitude']); ?>
                            </span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($pesanan['catatan'])): ?>
                        <div class="p-3 bg-light rounded-3">
                            <div class="text-muted small fw-bold"><i class="fa-solid fa-note-sticky me-1"></i> Catatan Pemesan:</div>
                            <div class="fst-italic text-secondary small">"<?= htmlspecialchars($pesanan['catatan']); ?>"</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Rincian Pesanan & Biaya -->
            <div class="col-md-6">
                <div class="card card-custom p-4 h-100">
                    <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-receipt text-danger"></i> Rincian Pesanan
                    </h5>
                    
                    <div class="list-group list-group-flush mb-3">
                        <?php foreach ($items as $it): ?>
                            <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold"><?= htmlspecialchars($it['nama_makanan']); ?></div>
                                    <small class="text-muted"><?= $it['jumlah']; ?>x @ Rp <?= number_format($it['harga'], 0, ',', '.'); ?></small>
                                </div>
                                <div class="fw-bold">
                                    Rp <?= number_format($it['subtotal'], 0, ',', '.'); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <hr class="my-2 text-muted">

                    <div class="d-flex justify-content-between text-muted small mb-1">
                        <span>Subtotal Item</span>
                        <span>Rp <?= number_format($pesanan['subtotal'], 0, ',', '.'); ?></span>
                    </div>
                    <div class="d-flex justify-content-between text-muted small mb-1">
                        <span>Ongkir Delivery</span>
                        <span><?= $pesanan['ongkir'] == 0 ? '<span class="text-success fw-bold">GRATIS</span>' : 'Rp ' . number_format($pesanan['ongkir'], 0, ',', '.'); ?></span>
                    </div>
                    <?php if ($pesanan['diskon'] > 0): ?>
                        <div class="d-flex justify-content-between text-success small mb-1">
                            <span>Diskon Promo</span>
                            <span>-Rp <?= number_format($pesanan['diskon'], 0, ',', '.'); ?></span>
                        </div>
                    <?php endif; ?>

                    <hr class="my-2">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-bold fs-6">Total Pembayaran</span>
                        <span class="fw-bold fs-5 text-danger">Rp <?= number_format($pesanan['total_bayar'], 0, ',', '.'); ?></span>
                    </div>

                    <div class="d-flex align-items-center justify-content-between bg-light p-2 rounded-3">
                        <span class="small text-muted">Metode Pembayaran:</span>
                        <span class="badge bg-danger px-3 py-2 fw-bold"><?= htmlspecialchars($pesanan['metode_pembayaran']); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Button -->
        <div class="text-center mt-4">
            <a href="index.php" class="btn btn-brand btn-lg shadow-sm">
                <i class="fa-solid fa-plus me-2"></i> Pesan Menu Lain Lagi
            </a>
        </div>
    </div>

</body>
</html>
