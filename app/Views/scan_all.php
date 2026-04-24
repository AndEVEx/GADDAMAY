<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Absensi Digital - Scan All (QR + RFID)</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <script src="https://unpkg.com/html5-qrcode@2.3.7/html5-qrcode.min.js"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

    * {
      box-sizing: border-box;
    }

    body {
      background: linear-gradient(135deg, #0f1729 0%, #1a2744 40%, #1e3a5f 70%, #2a5298 100%);
      min-height: 100vh;
      font-family: 'Inter', 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
    }

    .main-wrapper {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* Header */
    .header-section {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(20px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      padding: 1rem 2rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 1rem;
    }

    .header-left {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .logo-sekolah {
      max-height: 55px;
      width: auto;
      filter: drop-shadow(0 2px 8px rgba(0,0,0,0.3));
    }

    .school-info h3 {
      color: #fff;
      font-size: 1.4rem;
      font-weight: 700;
      margin: 0;
      letter-spacing: 0.5px;
    }

    .school-info span {
      color: rgba(255,255,255,0.7);
      font-size: 0.85rem;
      font-weight: 400;
    }

    .header-right {
      color: rgba(255,255,255,0.9);
      font-size: 0.95rem;
      font-weight: 500;
      text-align: right;
    }

    /* Clock */
    .clock-display {
      text-align: center;
      padding: 1.5rem 0 0.5rem;
    }

    .digital-clock {
      font-size: 4.5rem;
      font-weight: 800;
      color: #fff;
      text-shadow: 0 0 40px rgba(74, 144, 226, 0.5);
      letter-spacing: 4px;
      line-height: 1;
    }

    .date-display {
      color: rgba(255,255,255,0.6);
      font-size: 1rem;
      margin-top: 0.3rem;
    }

    /* Content area */
    .content-area {
      flex: 1;
      display: flex;
      gap: 1.5rem;
      padding: 1rem 2rem 2rem;
      max-width: 1400px;
      margin: 0 auto;
      width: 100%;
    }

    /* Panel styles */
    .scan-panel {
      flex: 1;
      background: rgba(255, 255, 255, 0.06);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 20px;
      backdrop-filter: blur(12px);
      padding: 1.5rem;
      display: flex;
      flex-direction: column;
      transition: all 0.3s ease;
    }

    .scan-panel:hover {
      border-color: rgba(255, 255, 255, 0.2);
      background: rgba(255, 255, 255, 0.08);
    }

    .panel-header {
      text-align: center;
      margin-bottom: 1rem;
    }

    .panel-icon {
      font-size: 2rem;
      margin-bottom: 0.5rem;
    }

    .panel-header h4 {
      color: #fff;
      font-size: 1.2rem;
      font-weight: 600;
      margin: 0;
    }

    .panel-header p {
      color: rgba(255,255,255,0.5);
      font-size: 0.8rem;
      margin: 0.2rem 0 0;
    }

    /* QR Scanner */
    .qr-panel .panel-icon { color: #4a90e2; }

    #reader {
      width: 100%;
      max-width: 350px;
      margin: 0 auto;
      border-radius: 12px;
      overflow: hidden;
    }

    #reader video {
      border-radius: 12px;
    }

    .camera-select {
      margin-bottom: 0.8rem;
      text-align: center;
    }

    .camera-select select {
      background: rgba(255,255,255,0.1);
      border: 1px solid rgba(255,255,255,0.2);
      color: #fff;
      border-radius: 8px;
      padding: 0.4rem 1rem;
      font-size: 0.85rem;
    }

    .camera-select select option {
      background: #1a2744;
      color: #fff;
    }

    /* RFID Panel */
    .rfid-panel .panel-icon { color: #f5a623; }

    .rfid-input-wrapper {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      gap: 1rem;
    }

    .rfid-input {
      font-size: 1.8rem;
      padding: 18px 20px;
      text-align: center;
      border-radius: 14px;
      border: 2px solid rgba(245, 166, 35, 0.4);
      background: rgba(255, 255, 255, 0.08);
      color: #fff;
      width: 100%;
      max-width: 400px;
      transition: all 0.3s ease;
      font-weight: 600;
      letter-spacing: 2px;
    }

    .rfid-input::placeholder {
      color: rgba(255,255,255,0.3);
      font-size: 1rem;
      letter-spacing: 0;
      font-weight: 400;
    }

    .rfid-input:focus {
      outline: none;
      border-color: #f5a623;
      box-shadow: 0 0 20px rgba(245, 166, 35, 0.2);
      background: rgba(255, 255, 255, 0.12);
    }

    .rfid-hint {
      color: rgba(255,255,255,0.4);
      font-size: 0.8rem;
      text-align: center;
    }

    /* Result card */
    .result-overlay {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0,0,0,0.6);
      backdrop-filter: blur(8px);
      z-index: 1050;
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.3s ease;
    }

    .result-overlay.show {
      opacity: 1;
      pointer-events: auto;
    }

    .result-card {
      background: linear-gradient(135deg, #1a2744, #2a3a5a);
      border-radius: 24px;
      padding: 2.5rem;
      max-width: 450px;
      width: 90%;
      text-align: center;
      color: #fff;
      box-shadow: 0 20px 60px rgba(0,0,0,0.5);
      border: 1px solid rgba(255,255,255,0.1);
      transform: scale(0.8);
      transition: transform 0.3s ease;
    }

    .result-overlay.show .result-card {
      transform: scale(1);
    }

    .result-card.success {
      border-color: rgba(46, 204, 113, 0.4);
    }

    .result-card.error {
      border-color: rgba(231, 76, 60, 0.4);
    }

    .result-photo {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid rgba(255,255,255,0.3);
      margin-bottom: 1rem;
    }

    .result-name {
      font-size: 1.4rem;
      font-weight: 700;
      margin-bottom: 0.3rem;
    }

    .result-details {
      text-align: left;
      margin: 1rem 0;
      padding: 1rem;
      background: rgba(255,255,255,0.05);
      border-radius: 12px;
    }

    .result-details p {
      margin: 0.3rem 0;
      font-size: 0.9rem;
      color: rgba(255,255,255,0.8);
    }

    .result-details strong {
      color: rgba(255,255,255,0.5);
      min-width: 80px;
      display: inline-block;
    }

    .result-status {
      display: inline-block;
      padding: 0.5rem 1.5rem;
      border-radius: 50px;
      font-weight: 700;
      font-size: 1.1rem;
      margin-top: 0.5rem;
    }

    .result-status.masuk { background: rgba(46, 204, 113, 0.2); color: #2ecc71; }
    .result-status.pulang { background: rgba(52, 152, 219, 0.2); color: #3498db; }
    .result-status.terlambat { background: rgba(241, 196, 15, 0.2); color: #f1c40f; }
    .result-status.gagal { background: rgba(231, 76, 60, 0.2); color: #e74c3c; }

    /* Mode tabs */
    .mode-tabs {
      display: none;
    }

    /* Toast */
    .custom-toast {
      position: fixed;
      top: 1rem;
      right: 1rem;
      z-index: 1060;
      min-width: 300px;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .content-area {
        flex-direction: column;
        padding: 1rem;
      }

      .header-section {
        padding: 0.8rem 1rem;
      }

      .school-info h3 {
        font-size: 1.1rem;
      }

      .digital-clock {
        font-size: 3rem;
      }

      .rfid-input {
        font-size: 1.3rem;
        padding: 14px;
      }

      .mode-tabs {
        display: flex;
        gap: 0.5rem;
        padding: 0 1rem;
        margin-bottom: 0.5rem;
      }

      .mode-tab {
        flex: 1;
        padding: 0.7rem;
        border: 1px solid rgba(255,255,255,0.2);
        background: rgba(255,255,255,0.05);
        color: #fff;
        border-radius: 12px;
        text-align: center;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
      }

      .mode-tab.active {
        background: rgba(74, 144, 226, 0.3);
        border-color: #4a90e2;
      }

      .scan-panel.hidden-mobile {
        display: none;
      }
    }

    /* Pulse animation for RFID */
    @keyframes pulse-border {
      0% { box-shadow: 0 0 0 0 rgba(245, 166, 35, 0.4); }
      70% { box-shadow: 0 0 0 10px rgba(245, 166, 35, 0); }
      100% { box-shadow: 0 0 0 0 rgba(245, 166, 35, 0); }
    }

    .rfid-input:focus {
      animation: pulse-border 2s infinite;
    }
  </style>
</head>

<body>
  <div class="main-wrapper">
    <!-- HEADER -->
    <div class="header-section">
      <div class="header-left">
        <img src="<?= base_url() ?>image/<?= $getLogo ?>" alt="Logo Sekolah" class="logo-sekolah" />
        <div class="school-info">
          <h3>SMKN 2 Indramayu</h3>
          <span>Absensi Digital - Scan All</span>
        </div>
      </div>
      <div class="header-right">
        <div id="datetime"></div>
      </div>
    </div>

    <!-- CLOCK -->
    <div class="clock-display">
      <div class="digital-clock" id="digitalClock">--:--:--</div>
      <div class="date-display" id="dateDisplay"></div>
    </div>

    <!-- MOBILE TABS -->
    <div class="mode-tabs">
      <div class="mode-tab active" onclick="switchMode('qr')">
        <i class="bi bi-qr-code-scan"></i> QR Code
      </div>
      <div class="mode-tab" onclick="switchMode('rfid')">
        <i class="bi bi-credit-card"></i> RFID / Kartu
      </div>
    </div>

    <!-- CONTENT -->
    <div class="content-area">
      <!-- QR Scanner Panel -->
      <div class="scan-panel qr-panel" id="qrPanel">
        <div class="panel-header">
          <div class="panel-icon"><i class="bi bi-qr-code-scan"></i></div>
          <h4>Scan QR Code</h4>
          <p>Arahkan QR Code ke kamera</p>
        </div>

        <div class="camera-select">
          <select id="cameraSelect" class="form-select-sm"></select>
        </div>

        <div id="reader"></div>
      </div>

      <!-- RFID Panel -->
      <div class="scan-panel rfid-panel" id="rfidPanel">
        <div class="panel-header">
          <div class="panel-icon"><i class="bi bi-credit-card-2-front"></i></div>
          <h4>Scan RFID / Kartu</h4>
          <p>Tempelkan kartu atau masukkan nomor RFID</p>
        </div>

        <div class="rfid-input-wrapper">
          <input type="number" id="rfidInput" class="rfid-input"
            placeholder="Tempelkan Kartu RFID" autofocus />
          <div class="rfid-hint">
            <i class="bi bi-info-circle"></i> Kartu akan otomatis diproses setelah scan
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- RESULT OVERLAY -->
  <div class="result-overlay" id="resultOverlay">
    <div class="result-card" id="resultCard">
      <img id="resultPhoto" class="result-photo" src="" alt="Foto" />
      <div class="result-name" id="resultName"></div>
      <div class="result-details" id="resultDetails"></div>
      <div class="result-status" id="resultStatus"></div>
      <div class="mt-3" id="resultMessage" style="color:rgba(255,255,255,0.6);font-size:0.85rem;"></div>
    </div>
  </div>

  <!-- TOAST -->
  <div class="position-fixed top-0 end-0 p-3" style="z-index: 1060">
    <div id="liveToast" class="toast align-items-center text-white bg-primary border-0" role="alert">
      <div class="d-flex">
        <div class="toast-body" id="toastBody"></div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>
  </div>

  <!-- AUDIO - volume set to maximum -->
  <audio id="audioSuccess" src="<?= base_url() ?>mp3/berhasil.mp3" preload="auto"></audio>
  <audio id="audioError" src="<?= base_url() ?>mp3/gagal.mp3" preload="auto"></audio>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Set audio volume to maximum
    document.getElementById('audioSuccess').volume = 1.0;
    document.getElementById('audioError').volume = 1.0;

    // Unlock audio on first interaction
    document.body.addEventListener('click', () => {
      document.getElementById('audioSuccess').play().catch(() => { });
      document.getElementById('audioError').play().catch(() => { });
    }, { once: true });

    const html5QrCode = new Html5Qrcode("reader");
    let isProcessing = false;

    // ===== CLOCK =====
    function updateClock() {
      const now = new Date();
      document.getElementById('digitalClock').textContent =
        String(now.getHours()).padStart(2, '0') + ':' +
        String(now.getMinutes()).padStart(2, '0') + ':' +
        String(now.getSeconds()).padStart(2, '0');

      const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
      document.getElementById('dateDisplay').textContent = now.toLocaleDateString('id-ID', options);
    }
    setInterval(updateClock, 1000);
    updateClock();

    // ===== DATETIME HEADER =====
    function updateDateTime() {
      const now = new Date();
      const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
      const dateStr = now.toLocaleDateString('id-ID', options);
      const timeStr = now.toLocaleTimeString('id-ID');
      document.getElementById('datetime').innerText = `${dateStr} | ${timeStr}`;
    }
    setInterval(updateDateTime, 1000);
    updateDateTime();

    // ===== TOAST =====
    function showToast(message, type = 'primary') {
      const toastEl = document.getElementById('liveToast');
      const toastBody = document.getElementById('toastBody');
      toastEl.classList.remove('bg-primary', 'bg-success', 'bg-danger', 'bg-warning');
      toastEl.classList.add('bg-' + type);
      toastBody.innerText = message;
      const toast = new bootstrap.Toast(toastEl, { delay: 2500 });
      toast.show();
    }

    // ===== RESULT DISPLAY =====
    function showResult(data, isSuccess) {
      const overlay = document.getElementById('resultOverlay');
      const card = document.getElementById('resultCard');
      const photo = document.getElementById('resultPhoto');
      const name = document.getElementById('resultName');
      const details = document.getElementById('resultDetails');
      const status = document.getElementById('resultStatus');
      const msg = document.getElementById('resultMessage');

      card.className = 'result-card ' + (isSuccess ? 'success' : 'error');

      if (isSuccess && data.siswa) {
        const fotoUrl = data.siswa.file
          ? '<?= base_url("image/siswa") ?>/' + data.siswa.file
          : '<?= base_url("image/siswa/noimage.png") ?>';
        photo.src = fotoUrl;
        photo.style.display = 'block';
        name.textContent = data.siswa.nm_siswa || '-';
        details.innerHTML = `
          <p><strong>No. Induk</strong> ${data.siswa.no_induk || '-'}</p>
          <p><strong>Kelas</strong> ${data.kelas || '-'}</p>
          <p><strong>Tanggal</strong> ${data.tanggal || '-'}</p>
          <p><strong>Jam</strong> ${data.jam || '-'}</p>
        `;

        const stsAbsen = (data.status_absen || '').toLowerCase();
        status.className = 'result-status';
        if (stsAbsen.includes('masuk')) status.classList.add('masuk');
        else if (stsAbsen.includes('pulang')) status.classList.add('pulang');
        else if (stsAbsen.includes('terlambat')) status.classList.add('terlambat');
        status.textContent = data.status_absen || '';
      } else {
        photo.style.display = 'none';
        name.textContent = isSuccess ? '✅ Berhasil' : '❌ Gagal';
        details.innerHTML = '';
        status.className = 'result-status gagal';
        status.textContent = data.status_absen || '';
      }

      msg.textContent = data.message || '';
      overlay.classList.add('show');

      // Play audio
      if (isSuccess) {
        document.getElementById('audioSuccess').currentTime = 0;
        document.getElementById('audioSuccess').play().catch(() => {});
      } else {
        document.getElementById('audioError').currentTime = 0;
        document.getElementById('audioError').play().catch(() => {});
      }

      // Auto-hide after 3.5 seconds
      setTimeout(() => {
        overlay.classList.remove('show');
        isProcessing = false;
        // Restart QR scanner
        const selectedCamera = document.getElementById('cameraSelect').value;
        if (selectedCamera) startScanner(selectedCamera);
        // Refocus RFID input
        document.getElementById('rfidInput').value = '';
        document.getElementById('rfidInput').focus();
      }, 3500);
    }

    // ===== QR SCANNER =====
    async function startScanner(cameraId = null) {
      try {
        const config = {
          fps: 15,
          qrbox: { width: 280, height: 280 },
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

    async function onScanSuccess(decodedText) {
      if (isProcessing) return;
      isProcessing = true;
      await stopScanner();
      showToast("✅ QR Terdeteksi!", "success");
      processAttendance('qr', { kode: decodedText });
    }

    function onScanFailure(error) { }

    // Initialize camera
    Html5Qrcode.getCameras().then(cameras => {
      const cameraSelect = document.getElementById('cameraSelect');

      if (cameras && cameras.length) {
        cameras.forEach(cam => {
          const option = document.createElement('option');
          option.value = cam.id;
          option.text = cam.label || `Camera ${cameraSelect.length + 1}`;
          cameraSelect.appendChild(option);
        });

        startScanner(cameras[0].id);

        cameraSelect.addEventListener('change', async () => {
          await stopScanner();
          startScanner(cameraSelect.value);
        });
      } else {
        showToast("❌ Kamera tidak ditemukan", "warning");
      }
    }).catch(err => {
      console.error("Camera list error:", err);
      showToast("❌ Gagal mengambil daftar kamera", "danger");
    });

    // ===== RFID INPUT =====
    const rfidInput = document.getElementById('rfidInput');
    let rfidTimeout = null;

    rfidInput.addEventListener('input', function () {
      clearTimeout(rfidTimeout);
      if (this.value.length >= 3) {
        rfidTimeout = setTimeout(() => {
          if (!isProcessing && this.value.trim()) {
            isProcessing = true;
            processAttendance('rfid', { rfid: this.value.trim() });
          }
        }, 500);
      }
    });

    rfidInput.addEventListener('keypress', function (e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        clearTimeout(rfidTimeout);
        if (!isProcessing && this.value.trim()) {
          isProcessing = true;
          processAttendance('rfid', { rfid: this.value.trim() });
        }
      }
    });

    // ===== PROCESS ATTENDANCE =====
    async function processAttendance(type, payload) {
      const url = type === 'qr'
        ? '<?= base_url("ScanAll/processQr") ?>'
        : '<?= base_url("ScanAll/processRfid") ?>';

      try {
        const res = await fetch(url, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
          },
          body: JSON.stringify(payload)
        });
        const data = await res.json();
        showResult(data, data.status);
      } catch (err) {
        console.error("Fetch error:", err);
        showResult({ message: 'Gagal mengirim data ke server', status_absen: 'Error' }, false);
      }
    }

    // ===== MOBILE MODE SWITCH =====
    function switchMode(mode) {
      const tabs = document.querySelectorAll('.mode-tab');
      tabs.forEach(t => t.classList.remove('active'));

      if (mode === 'qr') {
        tabs[0].classList.add('active');
        document.getElementById('qrPanel').classList.remove('hidden-mobile');
        document.getElementById('rfidPanel').classList.add('hidden-mobile');
      } else {
        tabs[1].classList.add('active');
        document.getElementById('rfidPanel').classList.remove('hidden-mobile');
        document.getElementById('qrPanel').classList.add('hidden-mobile');
        setTimeout(() => document.getElementById('rfidInput').focus(), 100);
      }
    }

    // Keep RFID input focused
    setInterval(() => {
      if (!isProcessing && document.activeElement !== rfidInput && window.innerWidth > 768) {
        // Don't steal focus from camera select
        if (document.activeElement.id !== 'cameraSelect') {
          rfidInput.focus();
        }
      }
    }, 3000);
  </script>

</body>

</html>
