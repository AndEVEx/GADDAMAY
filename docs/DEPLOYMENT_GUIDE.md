# Panduan Deployment GADDAMAY: Docker Multi-App & Tunnel VPS

Dokumen ini menjelaskan cara menjalankan **GADDAMAY** di server fisik/VPS yang menjalankan **beberapa aplikasi sekaligus menggunakan Docker**.

---

## 1. Arsitektur Docker Multi-Container

Di dalam server Anda yang memiliki berbagai aplikasi lain, GADDAMAY diisolasi di dalam network Docker tersendiri (`gaddamay-net`):

```
Server Fisik Sekolah (1 Mesin, Banyak Aplikasi)
│
├── [ Aplikasi Lain Anda ] (Moodle, Web Sekolah, CBT, dll)
│
└── [ GADDAMAY Docker Stack ] (Jaringan Terisolasi: gaddamay-net)
    ├── gaddamay_agenda  : Laravel 13 (Portal Utama, Perizinan, Agenda, PKL) -> Port 8000
    ├── gaddamay_absensi : CodeIgniter 4 (Presensi Gerbang Scanner RFID/QR) -> Port 8080
    ├── gaddamay_wa      : GOWA WhatsApp Gateway (Sangat ringan, ~40MB RAM) -> Port 3001
    └── gaddamay_db      : MariaDB 10.11 (Database bersama modul) -> Port 3307
```

---

## 2. Cara Menjalankan dengan Docker

### Langkah 1: Clone Repositori di Server
```bash
git clone https://github.com/AndEVEx/GADDAMAY.git
cd GADDAMAY
```

### Langkah 2: Sesuaikan Port agar Tidak Bentrok dengan Aplikasi Lain
Salin file `.env.example` ke `.env`:
```bash
cp .env.example .env
```
Edit file `.env` dan sesuaikan port jika port `8000`, `8080`, atau `3307` sudah dipakai aplikasi Anda yang lain:
```ini
AGENDA_PORT=8000      # Ganti misal 8100 jika 8000 sudah dipakai
ABSENSI_PORT=8080     # Ganti misal 8180 jika 8080 sudah dipakai
WA_PORT=3001          # Port dashboard WhatsApp Gateway
DB_HOST_PORT=3307     # Default 3307 agar tidak bentrok dengan MySQL 3306 server Anda
```

### Langkah 3: Build & Jalankan Container
```bash
docker compose up -d --build
```

### Langkah 4: Setup Awal Database (Pertama Kali Saja)
Jalankan migrasi di dalam container `gaddamay_agenda`:
```bash
docker exec -it gaddamay_agenda php artisan migrate --force
docker exec -it gaddamay_agenda php artisan key:generate
```

Selesai! Aplikasi langsung aktif:
- **Portal Utama & Agenda**: `http://IP-SERVER:8000`
- **Presensi Gerbang**: `http://IP-SERVER:8080`
- **WhatsApp Gateway QR Pairing**: `http://IP-SERVER:3001`

---

## 3. Integrasi Jika Server Sudah Memakai Reverse Proxy

Jika di server Anda sudah ada **Nginx Proxy Manager**, **Traefik**, atau **Nginx Utama**:

### Cara A: Sambungkan Port Host Langsung
Arahkan Proxy Host di Nginx Proxy Manager Anda ke:
- `http://172.17.0.1:8000` -> untuk domain `gaddamay.smkn2indramayu.sch.id`
- `http://172.17.0.1:8080` -> untuk domain `absensi.smkn2indramayu.sch.id`

### Cara B: Sambungkan ke Jaringan Docker Bersama (External Network)
Jika Anda menggunakan network proxy bersama (misal `proxy-network`):
Tambahkan di bagian bawah `docker-compose.yml`:
```yaml
networks:
  gaddamay-net:
    driver: bridge
  proxy-network:
    external: true
```
Lalu tambahkan `proxy-network` pada service `gaddamay-agenda` dan `gaddamay-absensi`. Anda cukup menggunakan nama container `http://gaddamay_agenda:80` di reverse proxy tanpa perlu mengekspos port ke host!

---

## 4. Akses dari Luar via Cloudflare Tunnel (Tanpa Port Forwarding)

Jika server fisik Anda di sekolah tidak memiliki IP Publik statis, pasang Cloudflare Tunnel di Docker compose yang sama.
Cukup tambahkan service berikut di `docker-compose.yml`:

```yaml
  cloudflared:
    image: cloudflare/cloudflared:latest
    container_name: gaddamay_tunnel
    restart: unless-stopped
    command: tunnel run --token YOUR_CLOUDFLARE_TUNNEL_TOKEN
    networks:
      - gaddamay-net
```
Dengan menambahkan 1 blok service di atas, seluruh sistem GADDAMAY langsung terhubung online dengan SSL HTTPS resmi, aman, dan bisa diakses oleh guru, orang tua, serta DUDI dari mana saja.