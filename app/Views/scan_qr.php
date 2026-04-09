<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Absensi Digital - Scan QR Code</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <script src="https://unpkg.com/html5-qrcode@2.3.7/html5-qrcode.min.js"></script>
  <style>
    body {
      background: linear-gradient(135deg, #1e3c72, #2a5298, #4776E6);
      min-height: 100vh;
    }

    #reader {
      width: 100%;
      max-width: 400px;
      margin: auto;
    }

    .logo-sekolah {
      max-height: 70px;
      width: auto;
    }

    .main-container {
      background: white;
      border: 4px solid #ffc107;
      border-radius: 1rem;
      padding: 2rem;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
    }

    .header-section {
      background-color: rgb(35, 117, 205);
      color: white;
      border-radius: 0.5rem;
      padding: 1.5rem;
      margin-bottom: 2rem;
    }

    .school-name {
      font-size: 1.8rem;
      font-weight: bold;
      margin-bottom: 0.3rem;
      letter-spacing: 1px;
    }

    .school-subtitle {
      font-size: 1rem;
      opacity: 0.9;
    }
  </style>
</head>

<body>

  <div class="container py-4">
    <div class="main-container">
      <!-- HEADER -->
      <header class="header-section d-flex flex-column flex-md-row align-items-center justify-content-between">
        <div class="d-flex align-items-center mb-3 mb-md-0">
          <img src="<?= base_url() ?>image/<?= $getLogo ?>" alt="Logo Sekolah" class="logo-sekolah me-4" />
          <div>
            <h3 class="school-name mb-0">SMKN 2 Indramayu</h3>
            <span class="school-subtitle">Absensi Digital</span>
          </div>
        </div>
        <div class="text-end">
          <div id="datetime" class="fw-semibold" style="font-size: 1.1rem;"></div>
        </div>
      </header>

      <!-- CONTENT -->
      <h1 class="text-center mb-4">Scan QR Code dengan Kamera</h1>

      <!-- CAMERA SELECT -->
      <div class="mb-3 text-center">
        <label for="cameraSelect" class="form-label fw-semibold">Pilih Kamera:</label>
        <select id="cameraSelect" class="form-select w-auto d-inline-block"></select>
      </div>

      <!-- QR READER -->
      <div id="reader"></div>

      <div id="resultCard" class="card mt-4 d-none border-warning border-3">
        <div class="card-body">
          <h5 class="card-title" id="resultTitle"></h5>
          <p class="card-text" id="resultMessage"></p>
        </div>
      </div>
    </div>
  </div>

  <!-- TOAST -->
  <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1055">
    <div id="liveToast" class="toast align-items-center text-white bg-primary border-0" role="alert"
      aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body" id="toastBody"></div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
          aria-label="Close"></button>
      </div>
    </div>
  </div>

  <!-- AudioContext for native beeps -->
  <script>
    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    
    function playTone(freq, type, duration, vol=0.1) {
      if(audioCtx.state === 'suspended') audioCtx.resume();
      const osc = audioCtx.createOscillator();
      const gain = audioCtx.createGain();
      osc.type = type;
      osc.frequency.setValueAtTime(freq, audioCtx.currentTime);
      gain.gain.setValueAtTime(vol, audioCtx.currentTime);
      gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + duration);
      osc.connect(gain);
      gain.connect(audioCtx.destination);
      osc.start();
      osc.stop(audioCtx.currentTime + duration);
    }

    function playSuccessSound() {
      // Dua beep nada tinggi cepat
      playTone(880, 'sine', 0.1, 0.2); // A5
      setTimeout(() => playTone(1108.73, 'sine', 0.2, 0.2), 100); // C#6
    }

    function playErrorSound() {
      // Nada rendah untuk error
      playTone(300, 'sawtooth', 0.3, 0.1);
      setTimeout(() => playTone(250, 'sawtooth', 0.4, 0.1), 150);
    }
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const html5QrCode = new Html5Qrcode("reader");

    document.body.addEventListener('click', () => {
      // Initialize AudioContext on first click to unlock it
      if(audioCtx.state === 'suspended') audioCtx.resume();
    }, { once: true });

    function showToast(message, type = 'primary') {
      const toastEl = document.getElementById('liveToast');
      const toastBody = document.getElementById('toastBody');
      toastEl.classList.remove('bg-primary', 'bg-success', 'bg-danger');
      toastEl.classList.add('bg-' + type);
      toastBody.innerText = message;
      const toast = new bootstrap.Toast(toastEl, { delay: 2000 });
      toast.show();
    }

    function updateDateTime() {
      const now = new Date();
      const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
      const dateStr = now.toLocaleDateString('id-ID', options);
      const timeStr = now.toLocaleTimeString('id-ID');
      document.getElementById('datetime').innerText = `${dateStr} | ${timeStr}`;
    }
    setInterval(updateDateTime, 1000);
    updateDateTime();

    async function startScanner(cameraId = null) {
      try {
        const config = {
          fps: 15,
          qrbox: { width: 350, height: 350 },
          aspectRatio: 1.0,
          disableFlip: false
        };
        await html5QrCode.start(cameraId, config, onScanSuccess, onScanFailure);
      } catch (err) {
        console.error("Scanner error:", err);
        showToast("❌ Gagal mengakses kamera", "danger");
      }
    }

    async function stopScanner() {
      try {
        await html5QrCode.stop();
      } catch (err) {
        console.error("Stop error:", err);
      }
    }

    async function onScanSuccess(decodedText, decodedResult) {
      await stopScanner();
      showToast("✅ QR Terdeteksi!", "success");

      try {
        const res = await fetch('<?= base_url('ScanQr/process') ?>', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
          },
          body: JSON.stringify({ kode: decodedText })
        });
        const data = await res.json();

        const resultCard = document.getElementById('resultCard');
        const resultTitle = document.getElementById('resultTitle');
        const resultMessage = document.getElementById('resultMessage');

        if (data.status) {
          const fotoSiswa = data.siswa?.file ? `<?= base_url('image/siswa') ?>/` + data.siswa.file : 'https://via.placeholder.com/100?text=No+Image';
          resultTitle.innerText = "✅ Berhasil!";
          resultMessage.innerHTML = `
          <div class="d-flex align-items-center mb-2">
            <img src="${fotoSiswa}" alt="Foto Siswa" style="width:80px; height:80px; object-fit:cover; border-radius:50%; margin-right:15px;">
            <div>
              <strong>Nama:</strong> ${data.siswa?.nm_siswa ?? '-'}<br>
              <strong>No Induk:</strong> ${data.siswa?.no_induk ?? '-'}<br>
              <strong>Kelas:</strong> ${data.kelas ?? '-'}<br>
              <strong>Tanggal:</strong> ${data.tanggal ?? '-'}<br>
              <strong>Jam:</strong> ${data.jam ?? '-'}<br>
              <strong>Status Absen:</strong> ${data.status_absen ?? '-'}
            </div>
          </div>
          <div>${data.message}</div>
        `;
          showToast('Absensi berhasil untuk ' + (data.siswa?.nm_siswa ?? 'Siswa'), 'success');
          playSuccessSound();
        } else {
          resultTitle.innerText = "❌ Gagal!";
          resultMessage.innerText = data.message;
          showToast(data.message, 'danger');
          playErrorSound();
        }

        resultCard.classList.remove('d-none');

        setTimeout(() => {
          resultCard.classList.add('d-none');
          resultTitle.innerText = '';
          resultMessage.innerText = '';
          const selectedCamera = document.getElementById('cameraSelect').value;
          startScanner(selectedCamera);
        }, 3000);

      } catch (err) {
        console.error("Fetch error:", err);
        showToast("❌ Gagal kirim data ke server", "danger");
        playErrorSound();
        setTimeout(() => {
          const selectedCamera = document.getElementById('cameraSelect').value;
          startScanner(selectedCamera);
        }, 3000);
      }
    }

    function onScanFailure(error) {
      // Boleh kosong
    }

    // Inisialisasi kamera
    Html5Qrcode.getCameras().then(cameras => {
      const cameraSelect = document.getElementById('cameraSelect');

      if (cameras && cameras.length) {
        cameras.forEach(cam => {
          const option = document.createElement('option');
          option.value = cam.id;
          option.text = cam.label || `Camera ${cameraSelect.length + 1}`;
          cameraSelect.appendChild(option);
        });

        // Auto-start kamera pertama
        startScanner(cameras[0].id);

        // Ganti kamera saat dropdown berubah
        cameraSelect.addEventListener('change', async () => {
          await stopScanner();
          startScanner(cameraSelect.value);
        });
      } else {
        showToast("❌ Kamera tidak ditemukan", "danger");
      }
    }).catch(err => {
      console.error("Camera list error:", err);
      showToast("❌ Gagal mengambil daftar kamera", "danger");
    });
  </script>

</body>

</html>