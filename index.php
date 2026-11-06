<?php
// ========================================================
// WebFood - Portal Pemesanan Makanan Online (GoFood Style)
// ========================================================
require_once 'koneksi.php';

// 1. Ambil Semua Kategori
$kategoriList = [];
$resKat = $conn->query("SELECT * FROM kategori ORDER BY urutan ASC");
if ($resKat) {
    while ($row = $resKat->fetch_assoc()) {
        $kategoriList[] = $row;
    }
}

// 2. Ambil Semua Menu Makanan & Minuman
$makananList = [];
$sqlMenu = "SELECT m.*, k.nama_kategori, k.slug AS kategori_slug 
            FROM makanan m 
            JOIN kategori k ON m.kategori_id = k.id 
            WHERE m.tersedia = 1 
            ORDER BY m.is_populer DESC, m.id ASC";
$resMenu = $conn->query($sqlMenu);
if ($resMenu) {
    while ($row = $resMenu->fetch_assoc()) {
        $makananList[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebFood - Pesan Makanan Lezat Cepat & Praktis Ala GoFood</title>
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --gofood-red: #EE2737;
            --gofood-red-dark: #D61827;
            --gojek-green: #00AA13;
            --dark-charcoal: #1C1C1E;
            --bg-light: #F7F8FA;
            --border-color: #EAEAEA;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-light);
            color: #2D3748;
            overflow-x: hidden;
            padding-bottom: 70px;
        }

        /* Top Bar & Navbar */
        .navbar-main {
            background: #ffffff;
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 1020;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        }

        .brand-logo {
            font-size: 1.55rem;
            font-weight: 800;
            color: var(--gofood-red) !important;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .brand-logo .brand-badge {
            background-color: var(--gofood-red);
            color: #fff;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 0.95rem;
        }

        .location-box {
            background-color: #F3F4F6;
            border-radius: 30px;
            padding: 6px 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid transparent;
        }

        .location-box:hover {
            border-color: var(--gofood-red);
            background-color: #FFF5F5;
        }

        .location-box .loc-text {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-weight: 600;
            color: #1F2937;
        }

        .cart-btn-nav {
            background-color: var(--gofood-red);
            color: white;
            border-radius: 50px;
            padding: 8px 18px;
            font-weight: 700;
            border: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(238, 39, 55, 0.25);
        }

        .cart-btn-nav:hover {
            background-color: var(--gofood-red-dark);
            color: white;
            transform: translateY(-1px);
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #FF4B2B 0%, #EE2737 50%, #C41221 100%);
            color: white;
            border-radius: 0 0 32px 32px;
            padding: 45px 0 55px;
            position: relative;
            overflow: hidden;
        }

        .hero-pattern {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            opacity: 0.08;
            background-image: radial-gradient(#ffffff 2px, transparent 2px);
            background-size: 24px 24px;
            pointer-events: none;
        }

        .hero-title {
            font-size: 2.7rem;
            font-weight: 800;
            line-height: 1.2;
            letter-spacing: -0.8px;
        }

        .hero-badge-pill {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(8px);
            color: #fff;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .hero-search-box {
            background: #ffffff;
            border-radius: 16px;
            padding: 8px 10px;
            display: flex;
            align-items: center;
            box-shadow: 0 8px 30px rgba(0,0,0,0.18);
        }

        .hero-search-box input {
            border: none;
            outline: none;
            box-shadow: none;
            padding-left: 12px;
            font-size: 0.95rem;
            color: #333;
            width: 100%;
        }

        .hero-search-box .btn-search {
            background-color: var(--gofood-red);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 10px 22px;
            font-weight: 700;
            transition: all 0.2s;
        }

        .hero-feature-card {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(6px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 14px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .hero-feature-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #ffffff;
            color: var(--gofood-red);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        /* Promo Banner Cards */
        .promo-card {
            border-radius: 18px;
            padding: 18px;
            color: white;
            position: relative;
            overflow: hidden;
            border: none;
            box-shadow: 0 4px 16px rgba(0,0,0,0.06);
            transition: transform 0.25s ease;
        }

        .promo-card:hover {
            transform: translateY(-3px);
        }

        .promo-bg-1 { background: linear-gradient(135deg, #FF6B6B, #EE2737); }
        .promo-bg-2 { background: linear-gradient(135deg, #00B09B, #00AA13); }
        .promo-bg-3 { background: linear-gradient(135deg, #F37335, #FDC830); }

        /* Categories Section */
        .category-tab-container {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            padding: 8px 2px 14px;
            scrollbar-width: none;
        }
        .category-tab-container::-webkit-scrollbar { display: none; }

        .category-chip {
            background: #ffffff;
            border: 1px solid #E5E7EB;
            color: #4B5563;
            border-radius: 50px;
            padding: 10px 20px;
            font-weight: 700;
            font-size: 0.88rem;
            white-space: nowrap;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }

        .category-chip:hover {
            border-color: var(--gofood-red);
            color: var(--gofood-red);
            background: #FFF5F5;
        }

        .category-chip.active {
            background-color: var(--gofood-red);
            border-color: var(--gofood-red);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(238, 39, 55, 0.3);
        }

        /* Food Cards Grid */
        .food-card {
            background: #ffffff;
            border-radius: 18px;
            border: 1px solid #ECECEC;
            overflow: hidden;
            transition: all 0.28s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            box-shadow: 0 3px 12px rgba(0,0,0,0.03);
        }

        .food-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.08);
            border-color: #E2E8F0;
        }

        .food-img-wrapper {
            position: relative;
            width: 100%;
            height: 190px;
            overflow: hidden;
            background: #f1f1f1;
        }

        .food-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .food-card:hover .food-img-wrapper img {
            transform: scale(1.05);
        }

        .badge-rating {
            position: absolute;
            top: 12px;
            right: 12px;
            background: rgba(28, 28, 30, 0.85);
            backdrop-filter: blur(4px);
            color: #FFF;
            padding: 4px 9px;
            border-radius: 8px;
            font-size: 0.78rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .badge-promo-tag {
            position: absolute;
            top: 12px;
            left: 12px;
            background: var(--gofood-red);
            color: #FFF;
            padding: 4px 9px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 0.3px;
        }

        .badge-time {
            position: absolute;
            bottom: 10px;
            left: 12px;
            background: rgba(255, 255, 255, 0.92);
            color: #1F2937;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .food-body {
            padding: 16px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .food-cat-pill {
            font-size: 0.72rem;
            font-weight: 700;
            color: #6B7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .food-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 6px;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .food-desc {
            font-size: 0.82rem;
            color: #6B7280;
            margin-bottom: 14px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.4;
            flex-grow: 1;
        }

        .price-current {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--gofood-red);
        }

        .price-original {
            font-size: 0.82rem;
            color: #9CA3AF;
            text-decoration: line-through;
            margin-left: 6px;
        }

        .btn-add-food {
            background-color: #FFF0F2;
            color: var(--gofood-red);
            border: 1px solid #FFE0E4;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.85rem;
            padding: 7px 16px;
            transition: all 0.2s;
        }

        .btn-add-food:hover {
            background-color: var(--gofood-red);
            color: #FFF;
            border-color: var(--gofood-red);
        }

        /* Cart Offcanvas & Controls */
        .cart-item-row {
            padding: 12px 0;
            border-bottom: 1px solid #F1F2F4;
        }

        .qty-btn {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: 1px solid #D1D5DB;
            background: #FFF;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: bold;
            color: #374151;
            cursor: pointer;
            transition: all 0.2s;
        }

        .qty-btn:hover {
            border-color: var(--gofood-red);
            color: var(--gofood-red);
            background: #FFF5F5;
        }

        .qty-display {
            font-weight: 700;
            font-size: 0.9rem;
            min-width: 24px;
            text-align: center;
        }

        /* Floating Bottom Cart Bar for Mobile */
        .floating-cart-bar {
            position: fixed;
            bottom: 18px;
            left: 50%;
            transform: translateX(-50%);
            width: 90%;
            max-width: 500px;
            background: var(--gofood-red);
            color: white;
            border-radius: 16px;
            padding: 12px 20px;
            display: none;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 8px 24px rgba(238, 39, 55, 0.4);
            z-index: 1030;
            cursor: pointer;
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from { transform: translate(-50%, 40px); opacity: 0; }
            to { transform: translate(-50%, 0); opacity: 1; }
        }

        /* Payment Radio Pills */
        .payment-label {
            border: 1px solid #E5E7EB;
            border-radius: 12px;
            padding: 10px 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s;
            margin-bottom: 8px;
        }

        .payment-label:hover {
            border-color: #D1D5DB;
            background: #F9FAFB;
        }

        .form-check-input:checked + .payment-label {
            border-color: var(--gofood-red);
            background-color: #FFF5F5;
            color: var(--gofood-red);
            font-weight: 700;
        }

        @media (max-width: 768px) {
            .hero-title { font-size: 1.95rem; }
            .hero-section { padding: 30px 0 40px; }
            .hero-feature-card { display: none; }
        }
    </style>
</head>
<body>

    <!-- 1. NAVBAR / HEADER -->
    <nav class="navbar-main py-2">
        <div class="container d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <a class="brand-logo" href="index.php">
                    <span class="brand-badge"><i class="fa-solid fa-utensils"></i></span>
                    <span>WebFood</span>
                </a>
                
                <!-- Quick Location Detector Badge -->
                <div class="location-box d-none d-md-flex" id="navLocationBox" onclick="dapatkanLokasi()">
                    <i class="fa-solid fa-location-dot text-danger"></i>
                    <div>
                        <div style="font-size: 0.72rem; color: #6B7280; line-height: 1;">Antar ke:</div>
                        <div class="loc-text" id="navLocationText">Pilih / Deteksi Lokasi GPS</div>
                    </div>
                    <i class="fa-solid fa-crosshairs text-muted small ms-1" title="Deteksi Otomatis"></i>
                </div>
            </div>

            <!-- Navbar Right: Search & Cart Button -->
            <div class="d-flex align-items-center gap-2">
                <button class="cart-btn-nav" type="button" data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas">
                    <i class="fa-solid fa-bag-shopping"></i>
                    <span class="d-none d-sm-inline">Keranjang</span>
                    <span class="badge bg-white text-danger rounded-pill px-2" id="cartCountBadge">0</span>
                    <span class="d-none d-md-inline ms-1 fw-bold" id="cartTotalNav">Rp 0</span>
                </button>
            </div>
        </div>
    </nav>

    <!-- 2. HERO SECTION -->
    <section class="hero-section">
        <div class="hero-pattern"></div>
        <div class="container position-relative">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <div class="hero-badge-pill mb-3">
                        <i class="fa-solid fa-fire text-warning"></i> Promo Kuliner Terfavorit Hari Ini
                    </div>
                    <h1 class="hero-title mb-3">
                        Lapar? Makanan Favoritmu Siap Diantar Hangat!
                    </h1>
                    <p class="fs-6 text-white-50 mb-4" style="max-width: 540px;">
                        Pesan aneka menu lezat khas nusantara dan jajanan kekinian ala GoFood. Pengantaran kilat, harga bersahabat, dan banyak voucher diskon.
                    </p>

                    <!-- Hero Search Box -->
                    <div class="hero-search-box mb-4">
                        <i class="fa-solid fa-magnifying-glass text-muted ms-2"></i>
                        <input type="text" id="foodSearchInput" placeholder="Cari ayam geprek, nasi goreng, kopi, martabak..." onkeyup="filterMenuSearch()">
                        <button class="btn btn-search" type="button" onclick="focusSearch()">
                            Cari
                        </button>
                    </div>

                    <!-- Hero Trust Highlights -->
                    <div class="row g-2">
                        <div class="col-4">
                            <div class="hero-feature-card">
                                <div class="hero-feature-icon">
                                    <i class="fa-solid fa-bolt"></i>
                                </div>
                                <div class="small lh-1 text-white">
                                    <strong>20 Menit</strong><br><span class="text-white-50" style="font-size: 0.75rem;">Antar Kilat</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="hero-feature-card">
                                <div class="hero-feature-icon">
                                    <i class="fa-solid fa-star"></i>
                                </div>
                                <div class="small lh-1 text-white">
                                    <strong>4.9+ Rating</strong><br><span class="text-white-50" style="font-size: 0.75rem;">Resto Pilihan</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="hero-feature-card">
                                <div class="hero-feature-icon">
                                    <i class="fa-solid fa-shield-halved"></i>
                                </div>
                                <div class="small lh-1 text-white">
                                    <strong>100% Higienis</strong><br><span class="text-white-50" style="font-size: 0.75rem;">Pasti Enak</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hero Banner Illustration / Promos -->
                <div class="col-lg-5 d-none d-lg-block text-center">
                    <div class="position-relative">
                        <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=600&auto=format&fit=crop&q=80" 
                             alt="Delicious Feast" 
                             class="img-fluid rounded-4 shadow-lg border border-3 border-white"
                             style="max-height: 320px; width: 100%; object-fit: cover;">
                        <div class="badge bg-white text-dark shadow-sm position-absolute top-0 start-0 m-3 px-3 py-2 rounded-pill fw-bold">
                            <i class="fa-solid fa-motorcycle text-success me-1"></i> Gratis Ongkir s/d 10rb
                        </div>
                        <div class="badge bg-danger text-white shadow-sm position-absolute bottom-0 end-0 m-3 px-3 py-2 rounded-pill fw-bold">
                            <i class="fa-solid fa-tag me-1"></i> Diskon s/d 40%
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. PROMO TICKER / VOUCHER CARDS -->
    <div class="container mt-4 mb-4">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="promo-card promo-bg-1">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-white text-danger mb-1 fw-bold">KODE: MAKANHEMAT</span>
                            <h5 class="fw-bold mb-0">Diskon Kilat 40%</h5>
                            <small class="text-white-50">Min. order Rp 40.000</small>
                        </div>
                        <i class="fa-solid fa-fire-flame-curved fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="promo-card promo-bg-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-white text-success mb-1 fw-bold">GRATIS ONGKIR</span>
                            <h5 class="fw-bold mb-0">Bebas Ongkir 5 KM</h5>
                            <small class="text-white-50">Otomatis aktif pesanan pertama</small>
                        </div>
                        <i class="fa-solid fa-truck-fast fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="promo-card promo-bg-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-white text-dark mb-1 fw-bold">HAPPY HOUR</span>
                            <h5 class="fw-bold mb-0">Kopi & Minuman Rp 12k</h5>
                            <small class="text-white-50">Segarkan harimu sekarang</small>
                        </div>
                        <i class="fa-solid fa-mug-hot fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. KATEGORI / JENIS MAKANAN (FILTER TABS) -->
    <div class="container mb-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h4 class="fw-bold mb-0 d-flex align-items-center gap-2">
                <i class="fa-solid fa-layer-group text-danger"></i> Jenis Makanan & Kategori
            </h4>
            <span class="text-muted small" id="menuCountDisplay">Menampilkan <?= count($makananList); ?> Menu</span>
        </div>

        <div class="category-tab-container">
            <button class="category-chip active" onclick="filterKategori('all', this)">
                <i class="fa-solid fa-border-all"></i> Semua Menu
            </button>
            <?php foreach ($kategoriList as $kat): ?>
                <button class="category-chip" onclick="filterKategori('<?= $kat['slug']; ?>', this)">
                    <i class="fa-solid <?= htmlspecialchars($kat['icon']); ?>"></i> <?= htmlspecialchars($kat['nama_kategori']); ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 5. GRID MENU MAKANAN -->
    <div class="container mb-5">
        <div class="row g-4" id="foodContainer">
            <?php if (empty($makananList)): ?>
                <div class="col-12 text-center py-5">
                    <i class="fa-solid fa-utensils fa-3x text-muted mb-3"></i>
                    <h5>Belum ada menu makanan.</h5>
                    <p class="text-muted">Jalankan setup database terlebih dahulu di <a href="setup_database.php">setup_database.php</a>.</p>
                </div>
            <?php else: ?>
                <?php foreach ($makananList as $m): ?>
                    <div class="col-xl-3 col-lg-4 col-md-6 food-item-wrapper" 
                         data-category="<?= $m['kategori_slug']; ?>" 
                         data-name="<?= strtolower(htmlspecialchars($m['nama_makanan'])); ?>"
                         data-desc="<?= strtolower(htmlspecialchars($m['deskripsi'])); ?>">
                        <div class="food-card">
                            <div class="food-img-wrapper">
                                <img src="<?= htmlspecialchars($m['gambar']); ?>" alt="<?= htmlspecialchars($m['nama_makanan']); ?>" loading="lazy">
                                
                                <?php if ($m['is_promo']): ?>
                                    <div class="badge-promo-tag">
                                        <i class="fa-solid fa-tag"></i> PROMO
                                    </div>
                                <?php endif; ?>

                                <div class="badge-rating">
                                    <i class="fa-solid fa-star text-warning"></i> <?= $m['rating']; ?> (<?= $m['terjual']; ?>+)
                                </div>

                                <div class="badge-time">
                                    <i class="fa-regular fa-clock me-1 text-danger"></i><?= htmlspecialchars($m['estimasi_waktu']); ?>
                                </div>
                            </div>

                            <div class="food-body">
                                <div class="food-cat-pill"><?= htmlspecialchars($m['nama_kategori']); ?></div>
                                <h5 class="food-title" title="<?= htmlspecialchars($m['nama_makanan']); ?>">
                                    <?= htmlspecialchars($m['nama_makanan']); ?>
                                </h5>
                                <p class="food-desc"><?= htmlspecialchars($m['deskripsi']); ?></p>

                                <div class="d-flex align-items-center justify-content-between pt-2 border-top mt-auto">
                                    <div>
                                        <span class="price-current">Rp <?= number_format($m['harga'], 0, ',', '.'); ?></span>
                                        <?php if (!empty($m['harga_coret']) && $m['harga_coret'] > $m['harga']): ?>
                                            <span class="price-original">Rp <?= number_format($m['harga_coret'], 0, ',', '.'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <button class="btn btn-add-food" 
                                            onclick="tambahKeKeranjang(<?= $m['id']; ?>, '<?= addslashes(htmlspecialchars($m['nama_makanan'])); ?>', <?= $m['harga']; ?>, '<?= addslashes($m['gambar']); ?>')">
                                        <i class="fa-solid fa-plus me-1"></i> Tambah
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div id="noResultsAlert" class="text-center py-5 d-none">
            <i class="fa-solid fa-magnifying-glass fa-3x text-muted mb-3"></i>
            <h5 class="fw-bold">Menu Tidak Ditemukan</h5>
            <p class="text-muted">Coba kata kunci pencarian atau pilih kategori yang lain.</p>
            <button class="btn btn-outline-danger rounded-pill px-4" onclick="resetFilter()">Tampilkan Semua Menu</button>
        </div>
    </div>

    <!-- 6. OFFCANVAS SHOPPING CART & CHECKOUT -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="cartOffcanvas" aria-labelledby="cartOffcanvasLabel" style="width: 440px;">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-bold d-flex align-items-center gap-2" id="cartOffcanvasLabel">
                <i class="fa-solid fa-bag-shopping text-danger"></i> Keranjang Pesanan
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        
        <div class="offcanvas-body d-flex flex-column">
            <!-- Empty Cart Notice -->
            <div id="emptyCartView" class="text-center my-auto py-5">
                <i class="fa-solid fa-basket-shopping fa-3x text-muted mb-3"></i>
                <h6 class="fw-bold">Keranjangmu Masih Kosong</h6>
                <p class="text-muted small">Pilih menu favoritmu di atas dan klik "+ Tambah" untuk memesan.</p>
            </div>

            <!-- Cart Items List -->
            <div id="cartItemsList" class="flex-grow-1 overflow-auto d-none mb-3">
                <!-- Dynamically rendered via JS -->
            </div>

            <!-- Bill Breakdown & Checkout Form (shown when cart has items) -->
            <div id="cartCheckoutSection" class="border-top pt-3 d-none">
                <!-- Ringkasan Biaya -->
                <div class="bg-light p-3 rounded-3 mb-3">
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>Subtotal Makanan</span>
                        <span id="billSubtotal">Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>Biaya Ongkir</span>
                        <span id="billOngkir">Rp 10.000</span>
                    </div>
                    <div class="d-flex justify-content-between small text-success mb-1 d-none" id="billDiscountRow">
                        <span>Diskon Belanja</span>
                        <span id="billDiskon">-Rp 0</span>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between fw-bold fs-6">
                        <span>Total Pembayaran</span>
                        <span class="text-danger" id="billTotal">Rp 0</span>
                    </div>
                </div>

                <!-- Tombol Lanjut ke Checkout Modal -->
                <button class="btn btn-danger w-100 py-3 fw-bold rounded-3 shadow-sm d-flex align-items-center justify-content-between" 
                        data-bs-toggle="modal" data-bs-target="#checkoutModal">
                    <span>Lanjut ke Pembayaran</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- 7. MODAL FORMULIR CHECKOUT & PENGIRIMAN -->
    <div class="modal fade" id="checkoutModal" tabindex="-1" aria-labelledby="checkoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="checkoutModalLabel">
                        <i class="fa-solid fa-clipboard-check text-danger"></i> Konfirmasi Pengiriman & Pembayaran
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4">
                    <form id="orderForm" onsubmit="submitPesanan(event)">
                        <div class="row g-3">
                            <!-- Data Pemesan -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Nama Lengkap Pemesan *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fa-solid fa-user text-muted"></i></span>
                                    <input type="text" class="form-control" id="formNama" placeholder="Contoh: Arya Wijaya" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">No. WhatsApp / Telepon *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fa-solid fa-phone text-muted"></i></span>
                                    <input type="tel" class="form-control" id="formTelepon" placeholder="Contoh: 08123456789" required>
                                </div>
                            </div>

                            <!-- Alamat & GPS Geolocation -->
                            <div class="col-12">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <label class="form-label fw-bold small text-muted mb-0">Alamat Lengkap Pengiriman *</label>
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-0" onclick="dapatkanLokasi(true)">
                                        <i class="fa-solid fa-crosshairs me-1"></i> Deteksi Lokasi GPS Otomatis
                                    </button>
                                </div>
                                <textarea class="form-control" id="formAlamat" rows="2" placeholder="Nama Jalan, Nomor Rumah, RT/RW, Patokan..." required></textarea>
                                <div id="gpsStatusNotice" class="small mt-1 text-muted"></div>
                                <input type="hidden" id="formLat">
                                <input type="hidden" id="formLng">
                            </div>

                            <!-- Catatan Pesanan -->
                            <div class="col-12">
                                <label class="form-label fw-bold small text-muted">Catatan Khusus Resto & Driver (Opsional)</label>
                                <input type="text" class="form-control" id="formCatatan" placeholder="Contoh: Sambal dipisah, jangan pakai daun bawang">
                            </div>

                            <!-- Metode Pembayaran -->
                            <div class="col-12">
                                <label class="form-label fw-bold small text-muted mb-2">Pilih Metode Pembayaran</label>
                                <div class="row g-2">
                                    <div class="col-sm-6 col-md-4">
                                        <input type="radio" class="form-check-input d-none" name="paymentMethod" id="payGoPay" value="GoPay" checked>
                                        <label class="payment-label w-100" for="payGoPay">
                                            <i class="fa-solid fa-wallet text-danger fa-lg"></i>
                                            <div>
                                                <div class="fw-bold small">GoPay</div>
                                                <div class="text-muted" style="font-size: 0.72rem;">Instan & Praktis</div>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="col-sm-6 col-md-4">
                                        <input type="radio" class="form-check-input d-none" name="paymentMethod" id="payOVO" value="OVO">
                                        <label class="payment-label w-100" for="payOVO">
                                            <i class="fa-solid fa-circle-dollar-to-slot text-primary fa-lg"></i>
                                            <div>
                                                <div class="fw-bold small">OVO / DANA</div>
                                                <div class="text-muted" style="font-size: 0.72rem;">E-Wallet Populer</div>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="col-sm-6 col-md-4">
                                        <input type="radio" class="form-check-input d-none" name="paymentMethod" id="payCOD" value="COD (Bayar di Tempat)">
                                        <label class="payment-label w-100" for="payCOD">
                                            <i class="fa-solid fa-money-bill-wave text-success fa-lg"></i>
                                            <div>
                                                <div class="fw-bold small">COD (Tunai)</div>
                                                <div class="text-muted" style="font-size: 0.72rem;">Bayar saat sampai</div>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <input type="radio" class="form-check-input d-none" name="paymentMethod" id="payBCA" value="Transfer Bank BCA">
                                        <label class="payment-label w-100" for="payBCA">
                                            <i class="fa-solid fa-building-columns text-info fa-lg"></i>
                                            <div>
                                                <div class="fw-bold small">Transfer Bank BCA</div>
                                                <div class="text-muted" style="font-size: 0.72rem;">Virtual Account</div>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <input type="radio" class="form-check-input d-none" name="paymentMethod" id="payMandiri" value="Transfer Bank Mandiri">
                                        <label class="payment-label w-100" for="payMandiri">
                                            <i class="fa-solid fa-credit-card text-warning fa-lg"></i>
                                            <div>
                                                <div class="fw-bold small">Bank Mandiri / QRIS</div>
                                                <div class="text-muted" style="font-size: 0.72rem;">Scan QRIS Semua Bank</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ringkasan Checkout Final -->
                        <div class="alert alert-light border mt-3 d-flex justify-content-between align-items-center">
                            <div>
                                <div class="small text-muted">Total Tagihan Final:</div>
                                <div class="fs-4 fw-bold text-danger" id="modalFinalTotal">Rp 0</div>
                            </div>
                            <button type="submit" id="btnKonfirmasiPesanan" class="btn btn-danger btn-lg px-4 fw-bold rounded-3 shadow">
                                <i class="fa-solid fa-check me-2"></i> Konfirmasi Pesanan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- 8. FLOATING CART BAR FOR MOBILE -->
    <div class="floating-cart-bar" id="mobileFloatingCart" data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-white text-danger rounded-pill px-2 py-1 fw-bold" id="mobileCartCount">0</span>
            <span class="fw-bold">Item di Keranjang</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="fw-bold fs-6" id="mobileCartTotal">Rp 0</span>
            <i class="fa-solid fa-chevron-right small"></i>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom WebFood JavaScript -->
    <script>
        // State Keranjang Belanja
        let keranjang = [];
        let userLocation = { lat: '', lng: '', address: '' };

        // Format Rupiah
        function formatRupiah(angka) {
            return 'Rp ' + Number(angka).toLocaleString('id-ID');
        }

        // Tambah Item ke Keranjang
        function tambahKeKeranjang(id, nama, harga, gambar) {
            const index = keranjang.findIndex(item => item.id === id);
            if (index > -1) {
                keranjang[index].jumlah += 1;
            } else {
                keranjang.push({
                    id: id,
                    nama: nama,
                    harga: Number(harga),
                    gambar: gambar,
                    jumlah: 1
                });
            }
            renderKeranjang();
            tampilkanToastTambah(nama);
        }

        // Ubah Jumlah Item (+ / -)
        function ubahJumlah(id, delta) {
            const index = keranjang.findIndex(item => item.id === id);
            if (index > -1) {
                keranjang[index].jumlah += delta;
                if (keranjang[index].jumlah <= 0) {
                    keranjang.splice(index, 1);
                }
            }
            renderKeranjang();
        }

        // Render Keranjang Belanja
        function renderKeranjang() {
            const emptyView = document.getElementById('emptyCartView');
            const itemsList = document.getElementById('cartItemsList');
            const checkoutSection = document.getElementById('cartCheckoutSection');
            const cartCountBadge = document.getElementById('cartCountBadge');
            const cartTotalNav = document.getElementById('cartTotalNav');
            const mobileFloating = document.getElementById('mobileFloatingCart');
            const mobileCartCount = document.getElementById('mobileCartCount');
            const mobileCartTotal = document.getElementById('mobileCartTotal');

            let totalItem = 0;
            let subtotal = 0;

            if (keranjang.length === 0) {
                emptyView.classList.remove('d-none');
                itemsList.classList.add('d-none');
                checkoutSection.classList.add('d-none');
                mobileFloating.style.display = 'none';
                cartCountBadge.innerText = '0';
                cartTotalNav.innerText = 'Rp 0';
                return;
            }

            emptyView.classList.add('d-none');
            itemsList.classList.remove('d-none');
            checkoutSection.classList.remove('d-none');
            mobileFloating.style.display = 'flex';

            let html = '';
            keranjang.forEach(item => {
                totalItem += item.jumlah;
                const itemTotal = item.harga * item.jumlah;
                subtotal += itemTotal;

                html += `
                    <div class="cart-item-row d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <img src="${item.gambar}" alt="${item.nama}" class="rounded-3" style="width: 50px; height: 50px; object-fit: cover;">
                            <div>
                                <div class="fw-bold small text-truncate" style="max-width: 170px;">${item.nama}</div>
                                <div class="text-danger small fw-semibold">${formatRupiah(item.harga)}</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button class="qty-btn" onclick="ubahJumlah(${item.id}, -1)">-</button>
                            <span class="qty-display">${item.jumlah}</span>
                            <button class="qty-btn" onclick="ubahJumlah(${item.id}, 1)">+</button>
                        </div>
                    </div>
                `;
            });

            itemsList.innerHTML = html;

            // Hitung Ongkir & Diskon
            let ongkir = 10000;
            let diskon = 0;
            const discountRow = document.getElementById('billDiscountRow');

            if (subtotal >= 75000) {
                ongkir = 0;
                diskon = 10000;
                discountRow.classList.remove('d-none');
                document.getElementById('billDiskon').innerText = '-Rp 10.000';
            } else {
                discountRow.classList.add('d-none');
            }

            const totalBayar = subtotal + ongkir;

            // Update UI teks
            cartCountBadge.innerText = totalItem;
            mobileCartCount.innerText = totalItem;
            cartTotalNav.innerText = formatRupiah(subtotal);
            mobileCartTotal.innerText = formatRupiah(totalBayar);

            document.getElementById('billSubtotal').innerText = formatRupiah(subtotal);
            document.getElementById('billOngkir').innerHTML = ongkir === 0 ? '<span class="text-success fw-bold">GRATIS</span>' : formatRupiah(ongkir);
            document.getElementById('billTotal').innerText = formatRupiah(totalBayar);
            document.getElementById('modalFinalTotal').innerText = formatRupiah(totalBayar);
        }

        // Tampilkan Notifikasi Mini Saat Tambah Makanan
        function tampilkanToastTambah(nama) {
            // Bisa tambahkan animasi ringkas
            const navBtn = document.querySelector('.cart-btn-nav');
            navBtn.style.transform = 'scale(1.08)';
            setTimeout(() => {
                navBtn.style.transform = 'none';
            }, 200);
        }

        // Filter Kategori
        function filterKategori(categorySlug, buttonEl) {
            document.querySelectorAll('.category-chip').forEach(btn => btn.classList.remove('active'));
            if (buttonEl) buttonEl.classList.add('active');

            const items = document.querySelectorAll('.food-item-wrapper');
            let visibleCount = 0;

            items.forEach(item => {
                if (categorySlug === 'all' || item.getAttribute('data-category') === categorySlug) {
                    item.classList.remove('d-none');
                    visibleCount++;
                } else {
                    item.classList.add('d-none');
                }
            });

            document.getElementById('menuCountDisplay').innerText = `Menampilkan ${visibleCount} Menu`;
            document.getElementById('noResultsAlert').classList.toggle('d-none', visibleCount > 0);
        }

        // Filter Pencarian Makanan
        function filterMenuSearch() {
            const query = document.getElementById('foodSearchInput').value.toLowerCase().trim();
            const items = document.querySelectorAll('.food-item-wrapper');
            let visibleCount = 0;

            items.forEach(item => {
                const name = item.getAttribute('data-name');
                const desc = item.getAttribute('data-desc');

                if (name.includes(query) || desc.includes(query)) {
                    item.classList.remove('d-none');
                    visibleCount++;
                } else {
                    item.classList.add('d-none');
                }
            });

            document.getElementById('menuCountDisplay').innerText = `Menampilkan ${visibleCount} Menu`;
            document.getElementById('noResultsAlert').classList.toggle('d-none', visibleCount > 0);
        }

        function focusSearch() {
            document.getElementById('foodSearchInput').focus();
        }

        function resetFilter() {
            document.getElementById('foodSearchInput').value = '';
            filterKategori('all', document.querySelector('.category-chip'));
        }

        // 8. Integrasi Geolocation GPS (Meningkatkan Fitur Sebelumnya)
        function dapatkanLokasi(isCheckoutForm = false) {
            const navText = document.getElementById('navLocationText');
            const statusNotice = document.getElementById('gpsStatusNotice');

            if (!navigator.geolocation) {
                alert('Browser Anda tidak mendukung deteksi lokasi otomatis.');
                return;
            }

            navText.innerText = 'Mencari lokasi GPS...';
            if (statusNotice) statusNotice.innerHTML = '<span class="text-primary"><i class="fa-solid fa-spinner fa-spin me-1"></i> Mendeteksi koordinat GPS Anda...</span>';

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    userLocation.lat = lat;
                    userLocation.lng = lng;

                    document.getElementById('formLat').value = lat;
                    document.getElementById('formLng').value = lng;

                    // Mengambil alamat perkiraan via Reverse Geocoding gratis (Nominatim OpenStreetMap)
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`)
                        .then(r => r.json())
                        .then(geo => {
                            const displayName = geo.display_name || `Lat: ${lat.toFixed(4)}, Lng: ${lng.toFixed(4)}`;
                            userLocation.address = displayName;
                            navText.innerText = geo.address?.suburb || geo.address?.city || 'Lokasi Terdeteksi';
                            
                            const alamatInput = document.getElementById('formAlamat');
                            if (!alamatInput.value.trim()) {
                                alamatInput.value = displayName;
                            }
                            if (statusNotice) {
                                statusNotice.innerHTML = `<span class="text-success"><i class="fa-solid fa-circle-check me-1"></i> Lokasi GPS aktif (${lat.toFixed(4)}, ${lng.toFixed(4)})</span>`;
                            }
                        })
                        .catch(() => {
                            navText.innerText = `GPS: ${lat.toFixed(3)}, ${lng.toFixed(3)}`;
                            if (statusNotice) {
                                statusNotice.innerHTML = `<span class="text-success"><i class="fa-solid fa-circle-check me-1"></i> Lokasi GPS terdeteksi (${lat}, ${lng})</span>`;
                            }
                        });

                    // Kirim ke simpan_lokasi.php untuk mencatat lokasi
                    fetch('simpan_lokasi.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ latitude: lat, longitude: lng })
                    }).catch(err => console.log('Log lokasi:', err));
                },
                function(error) {
                    navText.innerText = 'Gagal deteksi GPS';
                    if (statusNotice) {
                        statusNotice.innerHTML = '<span class="text-danger"><i class="fa-solid fa-triangle-exclamation me-1"></i> Izin lokasi ditolak atau GPS tidak aktif. Silakan isi alamat manual.</span>';
                    }
                },
                { enableHighAccuracy: true, timeout: 8000 }
            );
        }

        // 9. Submit Pesanan ke proses_pesanan.php
        function submitPesanan(event) {
            event.preventDefault();

            if (keranjang.length === 0) {
                alert('Keranjang belanja Anda masih kosong.');
                return;
            }

            const btnSubmit = document.getElementById('btnKonfirmasiPesanan');
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Memproses Pesanan...';

            const nama = document.getElementById('formNama').value.trim();
            const telp = document.getElementById('formTelepon').value.trim();
            const alamat = document.getElementById('formAlamat').value.trim();
            const catatan = document.getElementById('formCatatan').value.trim();
            const lat = document.getElementById('formLat').value.trim();
            const lng = document.getElementById('formLng').value.trim();
            
            const selectedPayment = document.querySelector('input[name="paymentMethod"]:checked');
            const metodePembayaran = selectedPayment ? selectedPayment.value : 'GoPay';

            const payload = {
                nama_pemesan: nama,
                no_telepon: telp,
                alamat_pengiriman: alamat,
                catatan: catatan,
                latitude: lat,
                longitude: lng,
                metode_pembayaran: metodePembayaran,
                items: keranjang.map(i => ({ id: i.id, jumlah: i.jumlah }))
            };

            fetch('proses_pesanan.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'sukses') {
                    // Berhasil, kosongkan keranjang dan redirect ke pesanan_sukses.php
                    keranjang = [];
                    window.location.href = data.redirect;
                } else {
                    alert('Gagal membuat pesanan: ' + data.pesan);
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = '<i class="fa-solid fa-check me-2"></i> Konfirmasi Pesanan';
                }
            })
            .catch(err => {
                console.error(err);
                alert('Terjadi kesalahan jaringan atau server.');
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = '<i class="fa-solid fa-check me-2"></i> Konfirmasi Pesanan';
            });
        }
    </script>
</body>
</html>