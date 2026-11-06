-- ========================================================
-- Database Schema & Seed Data for WebFood (GoFood Style)
-- Database: db_antarmakanan
-- ========================================================

CREATE DATABASE IF NOT EXISTS `db_antarmakanan` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `db_antarmakanan`;

-- 1. Tabel Kategori Makanan
CREATE TABLE IF NOT EXISTS `kategori` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nama_kategori` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `icon` VARCHAR(50) NOT NULL,
    `deskripsi` VARCHAR(255) DEFAULT NULL,
    `urutan` INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Tabel Makanan & Menu
CREATE TABLE IF NOT EXISTS `makanan` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `kategori_id` INT NOT NULL,
    `nama_makanan` VARCHAR(150) NOT NULL,
    `deskripsi` TEXT DEFAULT NULL,
    `harga` DECIMAL(10,2) NOT NULL,
    `harga_coret` DECIMAL(10,2) DEFAULT NULL,
    `rating` DECIMAL(2,1) DEFAULT 4.8,
    `terjual` INT DEFAULT 50,
    `estimasi_waktu` VARCHAR(50) DEFAULT '15-25 min',
    `gambar` TEXT NOT NULL,
    `is_promo` TINYINT(1) DEFAULT 0,
    `is_populer` TINYINT(1) DEFAULT 0,
    `tersedia` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_makanan_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Tabel Pesanan (Orders)
CREATE TABLE IF NOT EXISTS `pesanan` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `kode_pesanan` VARCHAR(50) NOT NULL UNIQUE,
    `nama_pemesan` VARCHAR(100) NOT NULL,
    `no_telepon` VARCHAR(30) NOT NULL,
    `alamat_pengiriman` TEXT NOT NULL,
    `catatan` TEXT DEFAULT NULL,
    `latitude` VARCHAR(50) DEFAULT NULL,
    `longitude` VARCHAR(50) DEFAULT NULL,
    `metode_pembayaran` VARCHAR(50) NOT NULL,
    `total_item` INT NOT NULL DEFAULT 1,
    `subtotal` DECIMAL(10,2) NOT NULL,
    `ongkir` DECIMAL(10,2) NOT NULL DEFAULT 10000,
    `diskon` DECIMAL(10,2) NOT NULL DEFAULT 0,
    `total_bayar` DECIMAL(10,2) NOT NULL,
    `status_pesanan` ENUM('Menunggu Konfirmasi', 'Sedang Dimasak', 'Diantar Driver', 'Selesai', 'Dibatalkan') DEFAULT 'Menunggu Konfirmasi',
    `waktu_pesan` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Tabel Detail Item Pesanan
CREATE TABLE IF NOT EXISTS `pesanan_detail` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `pesanan_id` INT NOT NULL,
    `makanan_id` INT NOT NULL,
    `nama_makanan` VARCHAR(150) NOT NULL,
    `harga` DECIMAL(10,2) NOT NULL,
    `jumlah` INT NOT NULL,
    `subtotal` DECIMAL(10,2) NOT NULL,
    CONSTRAINT `fk_detail_pesanan` FOREIGN KEY (`pesanan_id`) REFERENCES `pesanan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Tabel Pesanan Lokasi (Tetap dipertahankan untuk backward compatibility)
CREATE TABLE IF NOT EXISTS `pesanan_lokasi` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `latitude` VARCHAR(50) NOT NULL,
    `longitude` VARCHAR(50) NOT NULL,
    `waktu_pesan` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================================
-- SEED DATA: KATEGORI
-- ========================================================
INSERT INTO `kategori` (`id`, `nama_kategori`, `slug`, `icon`, `deskripsi`, `urutan`) VALUES
(1, 'Ayam & Bebek', 'ayam-bebek', 'fa-drumstick-bite', 'Olahan ayam & bebek gurih, pedas, dan lezat', 1),
(2, 'Nasi & Mie', 'nasi-mie', 'fa-bowl-rice', 'Aneka menu nasi, mie goreng, dan olahan karbohidrat mengenyangkan', 2),
(3, 'Burger & Fast Food', 'burger-fastfood', 'fa-burger', 'Burger lezat, kentang goreng renyah, dan ayam krispi', 3),
(4, 'Minuman & Kopi', 'minuman-kopi', 'fa-mug-hot', 'Kopi susu kekinian, boba, matcha, dan aneka minuman segar', 4),
(5, 'Dimsum & Snack', 'dimsum-snack', 'fa-cookie-bite', 'Camilan ringan, dimsum mentai, siomay, dan pempek', 5),
(6, 'Martabak & Dessert', 'martabak-dessert', 'fa-cake-candles', 'Martabak manis legit, croffle, dan hidangan penutup istimewa', 6)
ON DUPLICATE KEY UPDATE `nama_kategori`=VALUES(`nama_kategori`), `icon`=VALUES(`icon`);

-- ========================================================
-- SEED DATA: MAKANAN & MINUMAN
-- ========================================================
INSERT INTO `makanan` (`id`, `kategori_id`, `nama_makanan`, `deskripsi`, `harga`, `harga_coret`, `rating`, `terjual`, `estimasi_waktu`, `gambar`, `is_promo`, `is_populer`, `tersedia`) VALUES
-- Kategori 1: Ayam & Bebek
(1, 1, 'Ayam Geprek Sambal Korek Super Pedas', 'Ayam krispi gurih digeprek dengan sambal korek bawang asli rawit merah segar, gurih, dan nampol!', 22000, 28000, 4.9, 320, '15-25 min', 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?w=600&auto=format&fit=crop&q=80', 1, 1, 1),
(2, 1, 'Bebek Goreng Madura Bumbu Hitam', 'Bebek empuk goreng renyah disajikan dengan bumbu hitam rempah khas Madura gurih meresap dan sambal mangga.', 38000, 46000, 4.8, 210, '20-30 min', 'https://images.unsplash.com/photo-1514944298352-f47ec0538a7c?w=600&auto=format&fit=crop&q=80', 1, 1, 1),
(3, 1, 'Ayam Bakar Madu Taliwang', 'Ayam bakar berbalut kecap manis madu alami dan rempah khas lombok pedas manis yang meresap hingga ke tulang.', 27000, 32000, 4.8, 185, '15-25 min', 'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?w=600&auto=format&fit=crop&q=80', 0, 1, 1),

-- Kategori 2: Nasi & Mie
(4, 2, 'Nasi Goreng Spesial Komplit Sosis Telur', 'Nasi goreng harum wangi asap (wok hei) dengan topping telur mata sapi, sosis premium, bakso sapi, dan kerupuk renyah.', 25000, 30000, 4.9, 450, '15-20 min', 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?w=600&auto=format&fit=crop&q=80', 1, 1, 1),
(5, 2, 'Mie Goreng Aceh Seafood Pedas Mantap', 'Mie tebal kuning khas Aceh dimasak dengan bumbu kari rempah kuat, cumi, udang segar, acar bawang, dan emping.', 29000, 35000, 4.8, 190, '20-25 min', 'https://images.unsplash.com/photo-1612927601601-6638404737ce?w=600&auto=format&fit=crop&q=80', 1, 0, 1),
(6, 2, 'Nasi Gila Menteng Topping Melimpah', 'Nasi putih pulen disiram tumisan sosis, bakso, suwiran ayam, telur orak-arik dengan saus racikan menteng gurih pedas.', 24000, NULL, 4.7, 160, '15-20 min', 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=600&auto=format&fit=crop&q=80', 0, 1, 1),

-- Kategori 3: Burger & Fast Food
(7, 3, 'Double Cheese Beef Burger Deluxe', 'Dua lapis daging sapi murni dipanggang juicy, lelehan double keju cheddar gurih, selada segar, tomat, dan saus rahasia.', 35000, 44000, 4.9, 280, '15-25 min', 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=600&auto=format&fit=crop&q=80', 1, 1, 1),
(8, 3, 'Crispy Spicy Chicken Burger', 'Fillet paha ayam krispi pedas tebal dengan saus spicy mayo gurih di dalam bun roti brioche lembut bertabur wijen.', 28000, 34000, 4.8, 240, '15-20 min', 'https://images.unsplash.com/photo-1586190848861-99aa4a171e90?w=600&auto=format&fit=crop&q=80', 1, 0, 1),
(9, 3, 'Loaded Cheesy French Fries BBQ', 'Kentang goreng impor renyah keemasan bertabur keju lumer mozarella dan saus BBQ smoky lezat.', 19000, NULL, 4.7, 140, '10-15 min', 'https://images.unsplash.com/photo-1576107232684-1279f3908594?w=600&auto=format&fit=crop&q=80', 0, 0, 1),

-- Kategori 4: Minuman & Kopi
(10, 4, 'Es Kopi Susu Gula Aren Creamy', 'Espresso biji kopi arabika pilihan berpadu susu segar gurih dan manis legit gula aren murni alami.', 18000, 22000, 4.9, 520, '5-10 min', 'https://images.unsplash.com/photo-1517701550927-30cf4ba1dba5?w=600&auto=format&fit=crop&q=80', 1, 1, 1),
(11, 4, 'Iced Matcha Green Tea Latte', 'Bubuk matcha murni dari Uji Jepang dipadukan dengan fresh milk creamy yang menyegarkan dahaga.', 22000, 26000, 4.8, 230, '5-10 min', 'https://images.unsplash.com/photo-1536256263959-770b48d82b0a?w=600&auto=format&fit=crop&q=80', 1, 0, 1),
(12, 4, 'Es Teh Leci Jumbo Segar Manis', 'Teh melati wangi dingin berpadu sirup leci manis alami dengan buah leci asli di dalamnya.', 12000, NULL, 4.8, 380, '5-10 min', 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=600&auto=format&fit=crop&q=80', 0, 1, 1),

-- Kategori 5: Dimsum & Snack
(13, 5, 'Dimsum Ayam Udang Mentai Panggang', 'Isi 4 pcs dimsum padat daging ayam udang dengan siraman saus mentai gurih dibakar torch aromatik.', 25000, 32000, 4.9, 310, '15-20 min', 'https://images.unsplash.com/photo-1496116218417-1a781b1c416c?w=600&auto=format&fit=crop&q=80', 1, 1, 1),
(14, 5, 'Siomay Bandung Asli Bumbu Kacang Kental', 'Siomay ikan tenggiri asli, tahu kukus, kentang, dan telur rebus dengan siraman saus kacang gurih medok.', 22000, 26000, 4.7, 180, '10-15 min', 'https://images.unsplash.com/photo-1541696432-82c6da8ce7bf?w=600&auto=format&fit=crop&q=80', 0, 0, 1),
(15, 5, 'Pempek Palembang Kapal Selam Cuko Asli', 'Pempek kapal selam isi telur bebek utuh digoreng renyah dengan siraman kuah cuko asam manis pedas khas Palembang.', 24000, NULL, 4.8, 175, '15-20 min', 'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?w=600&auto=format&fit=crop&q=80', 0, 1, 1),

-- Kategori 6: Martabak & Dessert
(16, 6, 'Martabak Manis Spesial Cokelat Keju Tebal', 'Martabak manis bersarang lembut bertabur keju cheddar parut melimpah, meses cokelat Belanda, dan mentega Wijsman.', 36000, 45000, 4.9, 290, '20-30 min', 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=600&auto=format&fit=crop&q=80', 1, 1, 1),
(17, 6, 'Croffle Salted Caramel Lotus Biscoff', 'Croissant waffle renyah berlapis saus salted caramel lezat dan remahan biskuit Lotus Biscoff premium.', 26000, 32000, 4.8, 220, '15-20 min', 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=600&auto=format&fit=crop&q=80', 1, 0, 1),
(18, 6, 'Pisang Goreng Crispy Brown Sugar Keju', 'Pisang kepok manis berbalut tepung krispi keemasan dengan taburan bubuk brown sugar wangi dan parutan keju.', 18000, NULL, 4.7, 160, '15-20 min', 'https://images.unsplash.com/photo-1528735602780-2552fd46c7af?w=600&auto=format&fit=crop&q=80', 0, 0, 1)
ON DUPLICATE KEY UPDATE `nama_makanan`=VALUES(`nama_makanan`), `harga`=VALUES(`harga`), `gambar`=VALUES(`gambar`);
