# AeroTrack Maintenance System ✈️🚀

**AeroTrack Maintenance System** adalah platform manajemen pemeliharaan aset penerbangan (pesawat & roket) berbasis web. Sistem ini dirancang untuk memastikan kelaikan udara (*airworthiness*) melalui pemantauan jam terbang, penjadwalan servis, serta manajemen penggantian komponen secara terintegrasi.

Proyek ini dibuat sebagai **Tugas Akhir Mata Kuliah Workshop Basis Data**, dengan fokus pada implementasi struktur database relasional yang kompleks.

---

## 🛠️ Fitur Utama & Kebutuhan Database
Sistem ini memenuhi 7 kriteria tugas yang diberikan:

1.  **CRUD Implementation**: Pengelolaan data pesawat, teknisi, dan inventaris komponen secara lengkap.
2.  **Aggregate Functions**: 
    * Menghitung total jam terbang armada (`SUM`).
    * Menghitung jumlah pesawat yang sedang *grounded* (`COUNT`).
    * Menampilkan biaya rata-rata penggantian komponen (`AVG`).
3.  **Database View**: Halaman khusus "Ready-to-Flight" yang menampilkan daftar pesawat layak terbang berdasarkan status pemeliharaan terbaru.
4.  **Join Queries**: Menghubungkan data teknisi, log servis, dan unit pesawat dalam satu laporan terpadu.
5.  **Relasi Antar Tabel**:
    * **1:1**: Pesawat dengan Detail Spesifikasi Mesin.
    * **1:N**: Hangar dengan Pesawat (Satu hangar menampung banyak unit).
    * **N:N**: Log Perawatan dengan Komponen (Satu servis bisa mengganti banyak komponen, dan satu jenis komponen bisa digunakan di banyak log).
6.  **Minimal 7 Tabel**: Terdiri dari tabel `Aircraft`, `Engine_Specs`, `Technicians`, `Maintenance_Logs`, `Components`, `Component_Replacements`, dan `Hangars`.
7.  **Timeline**: Dikumpulkan pada Minggu ke-13.

---

## 📊 Skema Database (Ringkasan)
Sistem ini menggunakan 7 tabel utama untuk mengelola siklus hidup aset aerospace:
* **Aircrafts**: Data utama unit pesawat dan jam terbang.
* **Engine_Specs**: Detail teknis mesin (Relasi 1:1).
* **Technicians**: Data personel berlisensi.
* **Hangars**: Lokasi penyimpanan armada.
* **Components**: Katalog suku cadang/komponen.
* **Maintenance_Logs**: Catatan aktivitas servis.
* **Component_Replacements**: Detail komponen yang diganti (Pivot Table N:N).

---

## 🚀 Teknologi yang Digunakan
* **Bahasa Pemrograman**: PHP
* **Database**: MySQL / MariaDB
* **Framework CSS**: Bootstrap 5
* **Server**: XAMPP / Laragon

---

## 📝 Instalasi
1. Clone repository ini.
2. Import file database `.sql` yang tersedia di folder `/db`.
3. Sesuaikan konfigurasi database di file `config.php`.
4. Jalankan pada server lokal.

---
**Dibuat oleh:** [Maheswara]  
**Matkul:** Workshop Basis Data - Politeknik Elektronika Negeri Surabaya (PENS-EEPIS)
