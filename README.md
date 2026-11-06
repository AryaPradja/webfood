# 🍔 WebFood - Layanan Pesan Antar Makanan Online Ala GoFood

Aplikasi pemesanan makanan berbasis web dengan antarmuka modern yang terinspirasi dari **GoFood**. Dibangun menggunakan **PHP Native**, **MySQL**, **Bootstrap 5**, dan **FontAwesome**.

---

## ✨ Fitur Unggulan

- **Hero Section Dinamis**: Banner promo menggugah selera, kotak pencarian instan, dan badge keunggulan (20 Menit Antar Kilat, Resto Pilihan, Higienis).
- **Jenis Makanan & Kategori**: Filter instan berdasarkan kategori (*Ayam & Bebek, Nasi & Mie, Burger & Fast Food, Minuman & Kopi, Dimsum & Snack, Martabak & Dessert*).
- **Foto Menu Berkualitas Tinggi**: Setiap hidangan dilengkapi foto HD, estimasi waktu masak, rating bintang, label promo, dan harga diskon.
- **Keranjang Belanja Interaktif**: Drawer keranjang untuk menambah/mengurangi jumlah pesanan dengan kalkulasi subtotal, diskon otomatis (belanja di atas Rp 75.000), dan ongkir flat.
- **Deteksi Lokasi GPS Otomatis**: Integrasi Geolocation browser dan *OpenStreetMap Reverse Geocoding* untuk mengisi alamat tujuan secara presisi.
- **Pilihan Metode Pembayaran**: Mendukung GoPay, OVO, DANA, COD (Bayar di Tempat), dan Transfer Bank.
- **Halaman Status & Pelacakan Pesanan**: Konfirmasi pesanan dengan nomor unik (contoh: `WF-20261107-XXXXX`), status progres pembuatan makanan, dan simulasi penugasan driver GoFood.

---

## 🗄️ Struktur Database (`db_antarmakanan`)

- `kategori`: Menyimpan kategori dan jenis hidangan.
- `makanan`: Menyimpan menu, harga, foto, rating, dan ketersediaan stok.
- `pesanan`: Menyimpan informasi transaksi, pelanggan, lokasi GPS, dan metode pembayaran.
- `pesanan_detail`: Menyimpan rincian makanan yang dipesan per invoice.
- `pesanan_lokasi`: Menyimpan riwayat titik koordinat GPS.

---

## 🚀 Panduan Menjalankan di Lokal (XAMPP)

1. Letakkan folder ini di direktori:
   ```
   C:\xampp\htdocs\webfood
   ```
2. Nyalakan service **Apache** dan **MySQL** melalui XAMPP Control Panel.
3. Jalankan inisialisasi database otomatis dengan membuka browser ke:
   ```
   http://localhost/webfood/setup_database.php
   ```
4. Buka halaman utama aplikasi:
   ```
   http://localhost/webfood/
   ```
