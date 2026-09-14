# Panduan Deployment GADDAMAY: Server Lokal + VPS Tunnel

Dokumen ini menjelaskan arsitektur dan langkah-langkah implementasi deployment sistem terpadu **GADDAMAY (SMKN 2 Indramayu)** yang dijalankan pada **Server Fisik Lokal Sekolah** dan dapat diakses dari internet via **Tunneling VPS**.

---

## 1. Arsitektur Deployment

```
[ Internet / Luar Jaringan ]
   │
   ├─ Siswa / Ortu (HP) ─────────┐
   ├─ Industri / DUDI (Laptop) ──┤
   └─ Guru / Kepsek (Luar) ──────┤
                                 ▼
                    [ VPS Publik (Cloud) ]
                    Domain: gaddamay.smkn2indramayu.sch.id
                    Nginx Reverse Proxy / Cloudflare Tunnel
                                 │
                     (Encrypted WireGuard / Cloudflared / SSH Tunnel)
                                 │
                                 ▼
[ Jaringan Internal SMKN 2 Indramayu ]
   │
   ├─ Mesin Scanner Gerbang (Absensi RFID / QR) ──┐
   ├─ Komputer Guru Piket / Ruang TU ────────────┼─► [ Server Fisik Lokal ]
   └─ WhatsApp Gateway (GOWA / WA-AKG) ──────────┘   ├─ Nginx / Apache
                                                     ├─ PHP 8.3 (Laravel + CI4)
                                                     ├─ MariaDB / SQLite
                                                     └─ Redis / Queue Worker
```

### Keuntungan Model Ini:
1. **Kecepatan di Dalam Sekolah**: Fingerprint/QR gate dan guru di kelas mengakses server via LAN (sangat kencang, tidak membebani kuota internet sekolah).
2. **Keamanan Data**: Database utama tetap berada di server fisik milik sekolah.
3. **Akses Luar Fleksibel**: Siswa PKL di DUDI dan orang tua di rumah tetap bisa mengakses portal tanpa perlu IP Publik Statis di sekolah.
4. **Biaya Hemat**: Cukup menggunakan 1 VPS kecil (spesifikasi 1 core, 1 GB RAM) hanya sebagai jembatan tunnel (reverse proxy).

---

## 2. Pilihan Metode Tunneling

### Opsi A: Cloudflare Tunnel (Sangat Direkomendasikan - Gratis & Praktis)
*Tidak memerlukan VPS terpisah dan tidak memerlukan IP publik di sekolah.*

1. Buat akun di [Cloudflare Zero Trust](https://one.dash.cloudflare.com/) (Gratis).
2. Di Server Lokal Sekolah, pasang program `cloudflared`:
   ```powershell
   winget install Cloudflare.cloudflared
   ```
3. Login dan sambungkan tunnel ke domain sekolah:
   ```bash
   cloudflared tunnel login
   cloudflared tunnel create gaddamay-tunnel
   ```
4. Konfigurasikan file `config.yml`:
   ```yaml
   tunnel: <TUNNEL_ID>
   credentials-file: C:\Users\Server\.cloudflared\<TUNNEL_ID>.json

   ingress:
     # Portal Utama & Agenda
     - hostname: gaddamay.smkn2indramayu.sch.id
       service: http://localhost:8000
     # Presensi Gate
     - hostname: absensi.smkn2indramayu.sch.id
       service: http://localhost:8080
     - service: http_status:404
   ```
5. Jalankan sebagai Windows Service agar otomatis nyala saat PC server dihidupkan:
   ```powershell
   cloudflared service install
   ```

---

### Opsi B: VPS Reverse Proxy (FRP / WireGuard)

Jika menggunakan VPS Linux sendiri dengan IP Publik (misal: `103.xxx.xxx.xxx`):

1. **Gunakan FRP (Fast Reverse Proxy)**:
   - **Di VPS (Server Linux)**:
     Unduh `frps`, buat file `frps.ini`:
     ```ini
     [common]
     bind_port = 7000
     vhost_http_port = 8080
     token = KATA_KUNCI_RAHASIA_SEKOLAH
     ```
     Jalankan: `./frps -c frps.ini`
   - **Di Server Fisik Sekolah (Client Windows)**:
     Unduh `frpc.exe`, buat file `frpc.ini`:
     ```ini
     [common]
     server_addr = 103.xxx.xxx.xxx
     server_port = 7000
     token = KATA_KUNCI_RAHASIA_SEKOLAH

     [gaddamay-web]
     type = http
     local_ip = 127.0.0.1
     local_port = 8000
     custom_domains = gaddamay.smkn2indramayu.sch.id
     ```
     Jalankan: `frpc.exe -c frpc.ini`

2. **Nginx di VPS** sebagai SSL Terminator (HTTPS Certbot/Let's Encrypt):
   ```nginx
   server {
       server_name gaddamay.smkn2indramayu.sch.id;
       listen 443 ssl;
       # SSL Certs ...
       location / {
           proxy_pass http://127.0.0.1:8080;
           proxy_set_header Host $host;
           proxy_set_header X-Real-IP $remote_addr;
           proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
           proxy_set_header X-Forwarded-Proto https;
       }
   }
   ```

---

## 3. Cara Menjalankan Sistem GADDAMAY

### Di Komputer Lokal / Saat Uji Coba:
Di folder repositori `c:\Users\User\Downloads\ProjectX`:
1. Klik ganda file:
   `scripts\run-all.bat`
2. Dua terminal akan terbuka otomatis:
   - **Agenda & Portal**: `http://localhost:8000`
   - **Absensi Gerbang**: `http://localhost:8080`
3. Buka browser dan arahkan ke:
   👉 **`http://localhost:8000`**

### Menjalankan Produksi via Windows Service (NSSM):
Agar aplikasi otomatis berjalan saat PC dinyalakan tanpa perlu membuka terminal:
1. Unduh **NSSM (Non-Sucking Service Manager)**.
2. Daftarkan service untuk Laravel:
   ```cmd
   nssm install GaddamayAgenda "php" "artisan serve --host=0.0.0.0 --port=8000"
   nssm set GaddamayAgenda AppDirectory "C:\GADDAMAY\apps\agenda"
   nssm start GaddamayAgenda
   ```
3. Daftarkan service untuk Absensi CI4:
   ```cmd
   nssm install GaddamayAbsensi "php" "spark serve --host=0.0.0.0 --port=8080"
   nssm set GaddamayAbsensi AppDirectory "C:\GADDAMAY\apps\absensi"
   nssm start GaddamayAbsensi
   ```