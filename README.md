# GADDAMAY - Sistem Informasi Terpadu SMKN 2 Indramayu

**GADDAMAY** adalah platform monorepo terpadu yang menggabungkan dua ekosistem utama sistem informasi sekolah:
1. **Aplikasi Agenda & Jurnal Pembelajaran Guru** (pps/agenda)
2. **Aplikasi Presensi Gerbang, Kepegawaian & Notifikasi WhatsApp** (pps/absensi)

---

## 📁 Struktur Monorepo

`	ext
GADDAMAY/
├── apps/
│   ├── agenda/          # Aplikasi Agenda Harian & KKTP (Laravel 13 + Livewire 4)
│   └── absensi/         # Aplikasi Presensi Gate, RFID/ADMS & WA Gateway (CodeIgniter 4)
├── docs/                # Dokumentasi sistem & panduan integrasi
├── scripts/             # Skrip helper & otomatisasi dev/deploy
├── docker-compose.yml   # Konfigurasi container service
├── .gitignore           # Git ignore level monorepo
└── README.md            # Dokumentasi utama proyek
`

---

## 🚀 Modul Aplikasi

### 1. Modul Agenda (pps/agenda)
* **Framework**: Laravel 13 / Livewire 4 / Tailwind CSS
* **Fitur**:
  - Jurnal Harian Mengajar Guru & Materi Pembelajaran
  - Presensi Kehadiran Siswa per Jam Pelajaran / Mapel
  - Kriteria Ketercapaian Tujuan Pembelajaran (KKTP) & Nilai
  - Manajemen & Penukaran Jadwal Mengajar (Jadwal Swap)
  - Notifikasi WebPush PWA & Audit Trail
* **Port Standar**: http://localhost:8000

### 2. Modul Absensi Sekolah (pps/absensi)
* **Framework**: CodeIgniter 4 / Bootstrap / AdminLTE
* **Fitur**:
  - Presensi Masuk & Pulang Siswa (Gate Scanner Barcode, QR & RFID)
  - Presensi Pegawai & Integrasi Mesin Sidik Jari ADMS
  - Antrian Notifikasi Otomatis WhatsApp ke Orang Tua Wali Murid
  - Manajemen Poin Keterlambatan & Pelanggaran Kedisiplinan
  - Cetak Kartu Pelajar dengan Barcode / QR Code
* **Port Standar**: http://localhost:8080 (atau via VirtualHost / XAMPP Apache)

---

## 🛠️ Panduan Memulai Cepat (Local Development)

### Prasyarat
- PHP >= 8.2 (dengan ekstensi intl, mbstring, curl, pdo_mysql, gd, zip)
- Composer
- Node.js & NPM
- MySQL Server

### Menjalankan Modul Agenda (Laravel)
`ash
cd apps/agenda
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install && npm run build
php artisan serve --port=8000
`

### Menjalankan Modul Absensi (CodeIgniter 4)
`ash
cd apps/absensi
# Konfigurasikan file .env atau app/Config/Database.php dengan database MySQL Anda
# Import database: absen_smkn2_indramayu_complete.sql
php spark serve --port=8080
`

---

## 📝 Lisensi & Hak Cipta
Dikembangkan untuk SMKN 2 Indramayu. Hak Cipta & Hak Penggunaan dilindungi.
