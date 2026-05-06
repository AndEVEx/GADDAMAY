<!-- views/absensi/kartu.php -->
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Absensi Digital</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(120deg, rgb(50, 85, 103), rgb(45, 126, 225));
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }

        .absen-container {
            background: rgba(255, 255, 255, 0.07);
            padding: 40px;
            border-radius: 20px;
            backdrop-filter: blur(12px);
            width: 100%;
            max-width: 900px;
            color: #fff;
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.4);
            animation: fadeIn 0.6s ease-out;
        }

        .clock {
            font-size: 4rem;
            font-weight: bold;
        }

        .rfid-input {
            font-size: 1.5rem;
            padding: 15px;
            text-align: center;
            border-radius: 10px;
            border: none;
        }

        .profile-card {
            background: rgba(255, 255, 255, 0.15);
            padding: 20px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            gap: 20px;
            margin-top: 25px;
            animation: slideUp 0.5s ease-in-out;
        }

        .profile-img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid white;
        }

        .status-masuk {
            background: rgba(0, 255, 127, 0.2);
            padding: 5px 10px;
            border-radius: 8px;
        }

        .status-pulang {
            background: rgba(255, 69, 58, 0.2);
            padding: 5px 10px;
            border-radius: 8px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
</head>

<body>
    <div class="absen-container text-center">
        <img src="<?= base_url() ?>image/<?= $getLogo ?>" alt="Logo" style="width:80px;" class="mb-3">
        <h2 class="fw-bold">ABSENSI DIGITAL</h2>
        <p><?= formatTanggal(date('Y-m-d')); ?></p>

        <div class="clock mb-4" id="digitalClock">--:--:--</div>

        <form id="rfidForm" onsubmit="return submitRFID(event)">
            <input type="text" name="rfid" id="rfidInput" class="rfid-input w-100 mb-3"
                placeholder="Tempelkan Kartu / Masukkan RFID / Scan Qr" autofocus required>
            <button type="submit" class="btn btn-light btn-lg w-100">
                <i class="bi bi-check2-circle"></i> Absen
            </button>
        </form>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
      // Web Audio API - reliable playback from AJAX callbacks
      // Once AudioContext is resumed via user gesture, audio can play anytime
      let _actx = null;
      const _bufs = {};

      async function _getCtx() {
        if (!_actx) _actx = new (window.AudioContext || window.webkitAudioContext)();
        if (_actx.state === 'suspended') await _actx.resume();
        return _actx;
      }

      // Pre-load both mp3 files as decoded buffers
      (async function() {
        try {
          const ctx = await _getCtx();
          const files = { success: '<?= base_url() ?>mp3/berhasil.mp3', error: '<?= base_url() ?>mp3/gagal.mp3' };
          for (const [k, url] of Object.entries(files)) {
            const r = await fetch(url);
            const ab = await r.arrayBuffer();
            _bufs[k] = await ctx.decodeAudioData(ab);
          }
        } catch(e) {}
      })();

      // Resume AudioContext on any user interaction (required by browser policy)
      async function unlockAudio() {
        try { await _getCtx(); } catch(e) {}
      }
      ['click','keydown','touchstart','focus'].forEach(evt => {
        document.addEventListener(evt, unlockAudio, { capture: true });
      });
      const rfidEl = document.getElementById('rfidInput');
      if(rfidEl) rfidEl.addEventListener('focus', unlockAudio);

      function _playBuf(name) {
        if (!_actx || !_bufs[name]) return;
        try {
          const src = _actx.createBufferSource();
          src.buffer = _bufs[name];
          src.connect(_actx.destination);
          src.start(0);
        } catch(e) {}
      }

      function playSuccessSound() { _playBuf('success'); }
      function playErrorSound()   { _playBuf('error'); }
    </script>

    <script>
        function updateClock() {
            const now = new Date();
            document.getElementById('digitalClock').textContent =
                String(now.getHours()).padStart(2, '0') + ':' +
                String(now.getMinutes()).padStart(2, '0') + ':' +
                String(now.getSeconds()).padStart(2, '0');
        }
        setInterval(updateClock, 1000);
        updateClock();

        let isProcessing = false;

        function submitRFID(e) {
            e.preventDefault();
            if (isProcessing) return false;
            isProcessing = true;

            const rfid = document.getElementById('rfidInput').value.trim();
            if (!rfid) { isProcessing = false; return false; }

            fetch('<?= base_url("Dashboard/addabsensiAjax") ?>', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
                body: 'rfid=' + encodeURIComponent(rfid)
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    const fotoUrl = data.foto || '<?= base_url("image/siswa/noimage.png") ?>';
                    Swal.fire({
                        icon: 'success',
                        title: '✅ Absensi Berhasil',
                        html: `
                            <div style="text-align:center;">
                                <img src="${fotoUrl}" alt="Foto Siswa"
                                     style="width:110px;height:110px;object-fit:cover;border-radius:50%;
                                     border:3px solid #28a745;margin-bottom:10px;box-shadow:0 0 10px rgba(0,0,0,0.2);">
                                <h4 style="margin-bottom:6px;color:#333;">${data.nama}</h4>
                                <div style="text-align:left;display:inline-block;font-size:14px;color:#555;">
                                    <p><b>NIS:</b> ${data.nis}</p>
                                    <p><b>Kelas:</b> ${data.kelas}</p>
                                    <p><b>Status:</b> <span style="color:#007bff;">${data.status_absen}</span></p>
                                    <p><b>Jam:</b> ${data.jam}</p>
                                </div>
                            </div>`,
                        timer: 4500,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                    playSuccessSound();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message,
                        timer: 2500,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                    playErrorSound();
                }

                // Reset input and refocus
                document.getElementById('rfidInput').value = '';
                document.getElementById('rfidInput').focus();
                isProcessing = false;
            })
            .catch(err => {
                console.error('Error:', err);
                Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Koneksi error', timer: 2000, showConfirmButton: false });
                playErrorSound();
                document.getElementById('rfidInput').value = '';
                document.getElementById('rfidInput').focus();
                isProcessing = false;
            });

            return false;
        }

        // RFID scanner sends Enter key to submit
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('rfidInput');
            if (input) {
                input.focus();
                input.addEventListener('keypress', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        submitRFID(e);
                    }
                });
            }
        });
    </script>
</body>

</html>