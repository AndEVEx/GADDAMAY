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

    /* Hide number input arrows */
    input[type="number"]::-webkit-outer-spin-button,
    input[type="number"]::-webkit-inner-spin-button {
      -webkit-appearance: none;
      margin: 0;
    }
    input[type="number"] {
      -moz-appearance: textfield;
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
      border: 3px solid rgba(255, 255, 255, 0.2);
      border-radius: 20px;
      backdrop-filter: blur(12px);
      padding: 1.5rem;
      display: flex;
      flex-direction: column;
      transition: all 0.3s ease;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
    }

    .scan-panel:hover {
      background: rgba(255, 255, 255, 0.08);
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.3);
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
    .qr-panel { border-color: rgba(74, 144, 226, 0.4); }
    .qr-panel:hover { border-color: rgba(74, 144, 226, 0.8); }
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
    .rfid-panel { border-color: rgba(245, 166, 35, 0.4); }
    .rfid-panel:hover { border-color: rgba(245, 166, 35, 0.8); }
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
      border-radius: 28px;
      padding: 3rem 3.5rem;
      max-width: 600px;
      width: 92%;
      text-align: center;
      color: #fff;
      box-shadow: 0 24px 80px rgba(0,0,0,0.6);
      border: 2px solid rgba(255,255,255,0.15);
      transform: scale(0.8);
      transition: transform 0.3s ease;
    }

    .result-overlay.show .result-card {
      transform: scale(1);
    }

    .result-card.success {
      border-color: rgba(46, 204, 113, 0.5);
      box-shadow: 0 24px 80px rgba(46, 204, 113, 0.15);
    }

    .result-card.error {
      border-color: rgba(231, 76, 60, 0.5);
      box-shadow: 0 24px 80px rgba(231, 76, 60, 0.15);
    }

    .result-photo {
      width: 140px;
      height: 140px;
      border-radius: 50%;
      object-fit: cover;
      border: 4px solid rgba(255,255,255,0.4);
      margin-bottom: 1.2rem;
      box-shadow: 0 8px 24px rgba(0,0,0,0.3);
    }

    .result-name {
      font-size: 2rem;
      font-weight: 800;
      margin-bottom: 0.4rem;
      letter-spacing: 0.5px;
    }

    .result-details {
      text-align: left;
      margin: 1.2rem 0;
      padding: 1.2rem 1.5rem;
      background: rgba(255,255,255,0.06);
      border-radius: 14px;
    }

    .result-details p {
      margin: 0.5rem 0;
      font-size: 1.15rem;
      color: rgba(255,255,255,0.85);
    }

    .result-details strong {
      color: rgba(255,255,255,0.5);
      min-width: 100px;
      display: inline-block;
      font-size: 1rem;
    }

    .result-status {
      display: inline-block;
      padding: 0.7rem 2rem;
      border-radius: 50px;
      font-weight: 800;
      font-size: 1.4rem;
      margin-top: 0.8rem;
      letter-spacing: 1px;
    }

    .result-status.masuk { background: rgba(46, 204, 113, 0.25); color: #2ecc71; border: 2px solid rgba(46,204,113,0.3); }
    .result-status.pulang { background: rgba(52, 152, 219, 0.25); color: #3498db; border: 2px solid rgba(52,152,219,0.3); }
    .result-status.terlambat { background: rgba(241, 196, 15, 0.25); color: #f1c40f; border: 2px solid rgba(241,196,15,0.3); }
    .result-status.gagal { background: rgba(231, 76, 60, 0.25); color: #e74c3c; border: 2px solid rgba(231,76,60,0.3); }

    .result-message {
      font-size: 1.1rem;
      margin-top: 0.8rem;
      color: rgba(255,255,255,0.6);
    }

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

      <!-- RFID / Barcode / QR HID Panel -->
      <div class="scan-panel rfid-panel" id="rfidPanel">
        <div class="panel-header">
          <div class="panel-icon"><i class="bi bi-credit-card-2-front"></i></div>
          <h4>Scan RFID / Barcode / QR HID</h4>
          <p>Tempelkan kartu, scan barcode, atau gunakan QR Scanner HID</p>
        </div>

        <div class="rfid-input-wrapper">
          <input type="text" id="rfidInput" class="rfid-input"
            placeholder="Tap Kartu / Scan Barcode / QR" autofocus autocomplete="off" />
          <div class="rfid-hint">
            <i class="bi bi-info-circle"></i> Mendukung: RFID Reader, Barcode Scanner, QR Scanner HID (USB)
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

    // Unlock audio on ANY user interaction (keydown for RFID, click, touchstart)
    // IMPORTANT: unlock BOTH audio files sequentially (not parallel)
    // Some browsers only allow one play() per user gesture
    let audioSuccessUnlocked = false;
    let audioErrorUnlocked = false;

    async function unlockAudio() {
      const aS = document.getElementById('audioSuccess');
      const aE = document.getElementById('audioError');

      // Unlock success audio
      if (!audioSuccessUnlocked && aS) {
        try {
          aS.volume = 0;
          await aS.play();
          aS.pause();
          aS.currentTime = 0;
          aS.volume = 1.0;
          audioSuccessUnlocked = true;
        } catch(e) {}
      }

      // Unlock error audio (sequential, after success)
      if (!audioErrorUnlocked && aE) {
        try {
          aE.volume = 0;
          await aE.play();
          aE.pause();
          aE.currentTime = 0;
          aE.volume = 1.0;
          audioErrorUnlocked = true;
        } catch(e) {}
      }
    }

    // Listen on multiple events to catch any user interaction
    ['click','keydown','touchstart','focus'].forEach(evt => {
      document.addEventListener(evt, unlockAudio, { capture: true });
    });
    // Also try to unlock immediately when RFID input gets focus
    const rfidEl = document.getElementById('rfidInput');
    if(rfidEl) rfidEl.addEventListener('focus', unlockAudio);

    // Robust play functions with retry
    function playSuccessSound() {
      const a = document.getElementById('audioSuccess');
      if (!a) return;
      a.currentTime = 0;
      a.volume = 1.0;
      a.play().catch(() => {
        // If play fails, try unlock first then play again
        unlockAudio().then(() => {
          a.currentTime = 0;
          a.volume = 1.0;
          a.play().catch(() => {});
        });
      });
    }

    function playErrorSound() {
      const a = document.getElementById('audioError');
      if (!a) return;
      a.currentTime = 0;
      a.volume = 1.0;
      a.play().catch(() => {
        // If play fails, try unlock first then play again
        unlockAudio().then(() => {
          a.currentTime = 0;
          a.volume = 1.0;
          a.play().catch(() => {});
        });
      });
    }

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
    let resultTimer = null;

    async function showResult(data, isSuccess) {
      // Clear any previous timer so new scan can interrupt
      if (resultTimer) { clearTimeout(resultTimer); resultTimer = null; }

      const overlay = document.getElementById('resultOverlay');
      const card = document.getElementById('resultCard');
      const photo = document.getElementById('resultPhoto');
      const name = document.getElementById('resultName');
      const details = document.getElementById('resultDetails');
      const status = document.getElementById('resultStatus');
      const msg = document.getElementById('resultMessage');

      card.className = 'result-card ' + (isSuccess ? 'success' : 'error');

      if (isSuccess && data.siswa) {
        // Load photo from Cache API first (instant), fallback to server
        const fotoUrl = await getCachedPhotoUrl(data.siswa.file || 'noimage.png');
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
        status.textContent = data.status_absen || data.message || '';
      }

      msg.textContent = data.message || '';
      overlay.classList.add('show');

      // Play audio using robust helper functions
      if (isSuccess) {
        playSuccessSound();
      } else {
        playErrorSound();
      }

      // Allow next scan immediately (don't block with isProcessing during popup display)
      // Reset input and refocus right away so next student can scan
      document.getElementById('rfidInput').value = '';
      document.getElementById('rfidInput').focus();

      // Auto-hide popup after 2.5s (shorter so it's faster)
      resultTimer = setTimeout(() => {
        overlay.classList.remove('show');
        // Restart QR scanner if stopped
        const selectedCamera = document.getElementById('cameraSelect').value;
        if (selectedCamera) startScanner(selectedCamera);
        document.getElementById('rfidInput').focus();
      }, 2500);

      // Unlock processing immediately so next scan can happen
      isProcessing = false;
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

    // ===== RFID / QR HID INPUT =====
    // Works with: RFID readers, Barcode scanners, QR HID scanners (all act as keyboard)
    const rfidInput = document.getElementById('rfidInput');
    let rfidTimeout = null;
    let rfidBuffer = '';
    let lastKeyTime = 0;

    rfidInput.addEventListener('input', function () {
      clearTimeout(rfidTimeout);
      const now = Date.now();
      const val = this.value.trim();

      if (val.length >= 3) {
        // HID scanners type very fast (<50ms between chars), humans type slower
        // Wait 300ms after last input to auto-submit (covers fast HID scanners)
        rfidTimeout = setTimeout(() => {
          if (!isProcessing && this.value.trim()) {
            isProcessing = true;
            // Dismiss any existing popup before showing new one
            const overlay = document.getElementById('resultOverlay');
            if (overlay.classList.contains('show')) {
              overlay.classList.remove('show');
              if (resultTimer) { clearTimeout(resultTimer); resultTimer = null; }
            }
            processAttendance('rfid', { rfid: this.value.trim() });
          }
        }, 300);
      }
      lastKeyTime = now;
    });

    rfidInput.addEventListener('keypress', function (e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        clearTimeout(rfidTimeout);
        if (!isProcessing && this.value.trim()) {
          isProcessing = true;
          // Dismiss any existing popup
          const overlay = document.getElementById('resultOverlay');
          if (overlay.classList.contains('show')) {
            overlay.classList.remove('show');
            if (resultTimer) { clearTimeout(resultTimer); resultTimer = null; }
          }
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

    // ===== SMART PHOTO PRELOADER (Cache API) =====
    // Uses browser Cache API for persistent storage across reloads
    // Only downloads photos that aren't already cached
    const PHOTO_CACHE_NAME = 'smkn2-siswa-photos-v1';
    const PHOTO_BASE_URL = '<?= base_url("image/siswa") ?>/';

    (async function preloadPhotos() {
      const indicator = document.getElementById('preloadIndicator');
      const progress = document.getElementById('preloadProgress');
      const counter = document.getElementById('preloadCounter');
      const statusText = document.getElementById('preloadStatus');

      try {
        // Check if Cache API is supported
        if (!('caches' in window)) {
          console.warn('Cache API not supported, falling back to Image preload');
          fallbackPreload();
          return;
        }

        const res = await fetch('<?= base_url("ScanAll/photoList") ?>');
        const data = await res.json();

        if (!data.photos || data.photos.length === 0) {
          indicator.style.display = 'none';
          return;
        }

        const cache = await caches.open(PHOTO_CACHE_NAME);
        const total = data.photos.length;
        let cached = 0;
        let downloaded = 0;
        let skipped = 0;

        // Check which photos are already cached
        const toDownload = [];
        for (const filename of data.photos) {
          const url = PHOTO_BASE_URL + filename;
          const match = await cache.match(url);
          if (match) {
            skipped++;
          } else {
            toDownload.push(filename);
          }
        }

        if (toDownload.length === 0) {
          // All photos already cached!
          indicator.style.display = 'flex';
          progress.style.width = '100%';
          progress.style.background = 'linear-gradient(90deg,#00b894,#2ecc71)';
          counter.textContent = total + '/' + total;
          statusText.textContent = '✅ Semua foto dari cache';
          setTimeout(() => {
            indicator.style.opacity = '0';
            setTimeout(() => indicator.style.display = 'none', 500);
          }, 2000);
          return;
        }

        // Show indicator with download info
        indicator.style.display = 'flex';
        const pctCached = Math.round((skipped / total) * 100);
        progress.style.width = pctCached + '%';
        counter.textContent = skipped + '/' + total;
        statusText.textContent = 'Memuat ' + toDownload.length + ' foto baru...';

        // Download uncached photos in batches
        let batch = 0;
        const batchSize = 5;

        function loadBatch() {
          const start = batch * batchSize;
          const end = Math.min(start + batchSize, toDownload.length);
          let batchDone = 0;

          for (let i = start; i < end; i++) {
            const url = PHOTO_BASE_URL + toDownload[i];
            fetch(url)
              .then(response => {
                if (response.ok) {
                  // Store in cache
                  return cache.put(url, response);
                }
              })
              .catch(() => {})
              .finally(() => {
                downloaded++;
                batchDone++;
                const totalDone = skipped + downloaded;
                const pct = Math.round((totalDone / total) * 100);
                progress.style.width = pct + '%';
                counter.textContent = totalDone + '/' + total;

                if (downloaded >= toDownload.length) {
                  // All done
                  progress.style.background = 'linear-gradient(90deg,#00b894,#2ecc71)';
                  statusText.textContent = '✅ Selesai (' + skipped + ' cache, ' + downloaded + ' baru)';
                  setTimeout(() => {
                    indicator.style.opacity = '0';
                    setTimeout(() => indicator.style.display = 'none', 500);
                  }, 2000);
                }

                // Start next batch when current is done
                if (batchDone >= (end - start) && end < toDownload.length) {
                  batch++;
                  setTimeout(loadBatch, 50);
                }
              });
          }
        }

        loadBatch();

      } catch (err) {
        console.warn('Photo preload error:', err);
        indicator.style.display = 'none';
      }

      // Fallback for browsers without Cache API
      function fallbackPreload() {
        fetch('<?= base_url("ScanAll/photoList") ?>')
          .then(r => r.json())
          .then(data => {
            if (!data.photos) return;
            data.photos.forEach(f => { const img = new Image(); img.src = PHOTO_BASE_URL + f; });
          });
      }
    })();

    // Helper: get photo from cache for popup display
    async function getCachedPhotoUrl(filename) {
      if (!filename || !('caches' in window)) return PHOTO_BASE_URL + (filename || 'noimage.png');
      try {
        const cache = await caches.open(PHOTO_CACHE_NAME);
        const url = PHOTO_BASE_URL + filename;
        const match = await cache.match(url);
        if (match) {
          const blob = await match.blob();
          return URL.createObjectURL(blob);
        }
      } catch(e) {}
      return PHOTO_BASE_URL + filename;
    }
  </script>

  <!-- Preload progress indicator (subtle, bottom-left) -->
  <div id="preloadIndicator" style="
    display: none;
    position: fixed;
    bottom: 1rem;
    left: 1rem;
    background: rgba(0,0,0,0.6);
    backdrop-filter: blur(8px);
    border-radius: 12px;
    padding: 8px 14px;
    color: rgba(255,255,255,0.7);
    font-size: 0.75rem;
    align-items: center;
    gap: 10px;
    z-index: 1000;
    transition: opacity 0.5s ease;
    border: 1px solid rgba(255,255,255,0.1);
  ">
    <i class="bi bi-image" style="font-size:0.9rem;"></i>
    <div style="flex:1;">
      <div id="preloadStatus" style="margin-bottom:3px;">Memuat foto siswa...</div>
      <div style="background: rgba(255,255,255,0.15); border-radius:4px; height:4px; width:120px; overflow:hidden;">
        <div id="preloadProgress" style="height:100%; width:0%; background:linear-gradient(90deg,#4a90e2,#00f2fe); border-radius:4px; transition: width 0.2s;"></div>
      </div>
    </div>
    <span id="preloadCounter" style="font-weight:600; min-width:45px; text-align:right;">0/0</span>
  </div>

</body>

</html>
