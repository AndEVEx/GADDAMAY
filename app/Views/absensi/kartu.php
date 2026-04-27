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

        <form method="post" action="<?= base_url('Dashboard/addabsensi'); ?>">
            <input type="text" name="rfid" class="rfid-input w-100 mb-3"
                placeholder="Tempelkan Kartu / Masukkan RFID / Scan Qr" autofocus required>
            <input type="hidden" name="hari" value="<?= $getHari; ?>">
            <input type="hidden" name="sts" value="<?= $stsAbsen; ?>">
            <input type="hidden" name="id_tapel" value="<?= $getIdtapel; ?>">
            <button type="submit" class="btn btn-light btn-lg w-100">
                <i class="bi bi-check2-circle"></i> Absen
            </button>
        </form>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Audio elements -->
    <audio id="audioSuccess" src="<?= base_url() ?>mp3/berhasil.mp3" preload="auto"></audio>
    <audio id="audioError" src="<?= base_url() ?>mp3/gagal.mp3" preload="auto"></audio>

    <script>
      // Unlock audio on first interaction silently
      document.body.addEventListener('click', () => {
        const aS = document.getElementById('audioSuccess');
        const aE = document.getElementById('audioError');
        if(aS && aE) {
            aS.volume = 0; aE.volume = 0;
            aS.play().then(() => { aS.pause(); aS.currentTime = 0; aS.volume = 1.0; }).catch(() => {});
            aE.play().then(() => { aE.pause(); aE.currentTime = 0; aE.volume = 1.0; }).catch(() => {});
        }
      }, { once: true });

      function playSuccessSound() {
        const aS = document.getElementById('audioSuccess');
        if(aS) { aS.currentTime = 0; aS.play().catch(()=>{}); }
      }
      function playErrorSound() {
        const aE = document.getElementById('audioError');
        if(aE) { aE.currentTime = 0; aE.play().catch(()=>{}); }
      }
      
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

        // Auto reload tiap 15 detik
        setTimeout(function () {
            location.reload();
        }, 15000);
    </script>
    <?php if (session()->getFlashdata('success')): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Data berhasil disimpan',
                html: 'Menutup otomatis dalam <b></b> detik.',
                timer: 5000,
                timerProgressBar: true,
                didOpen: () => {
                    const b = Swal.getHtmlContainer().querySelector('b');
                    let timerInterval = setInterval(() => {
                        b.textContent = Math.ceil(Swal.getTimerLeft() / 1000);
                    }, 100);
                }
            });
            playSuccessSound();
        </script>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                html: '<?= session()->getFlashdata('error'); ?><br><small>Menutup otomatis dalam <b></b> detik...</small>',
                timer: 2500,
                timerProgressBar: true,
                showConfirmButton: false,
                didOpen: () => {
                    const b = Swal.getHtmlContainer().querySelector('b');
                    const timerInterval = setInterval(() => {
                        b.textContent = Math.ceil(Swal.getTimerLeft() / 1000);
                    }, 100);
                },
                willClose: () => {
                    document.querySelector('.rfid-input').focus();
                }
            });
            playErrorSound();
        </script>
    <?php endif; ?>

    <?php if (session()->getFlashdata('Pesanabsen')): ?>
        <?php
        $foto = session()->getFlashdata('Fotoabsen');
        $fotoUrl = !empty($foto) ? base_url('image/siswa/' . $foto) : base_url('image/siswa/noimage.png');
        ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: '✅ Absensi Berhasil',
                html: `
        <div style="text-align:center;">
            <img src="<?= $fotoUrl ?>" alt="Foto Siswa"
                 style="width:110px;height:110px;object-fit:cover;border-radius:50%;
                 border:3px solid #28a745;margin-bottom:10px;box-shadow:0 0 10px rgba(0,0,0,0.2);">

            <h4 style="margin-bottom:6px;color:#333;"><?= session()->getFlashdata('Nama'); ?></h4>

            <div style="text-align:left;display:inline-block;font-size:14px;color:#555;">
                <p><i class="fas fa-id-card"></i> <b>NIS:</b> <?= session()->getFlashdata('NIS'); ?></p>
                <p><i class="fas fa-school"></i> <b>Kelas:</b> <?= session()->getFlashdata('Kelas'); ?></p>
                <p><i class="fas fa-user-check"></i> <b>Status:</b> 
                    <span style="color:#007bff;"><?= session()->getFlashdata('Pesanabsen'); ?></span>
                </p>
                <p><i class="fas fa-clock"></i> <b>Jam Absen:</b> <?= session()->getFlashdata('Jamabsen'); ?></p>
            </div>
        </div>
    `,
                timer: 4500,
                timerProgressBar: true,
                showConfirmButton: false,
                background: '#f9f9f9',
                backdrop: `
        rgba(0,0,0,0.3)
        left top
        no-repeat
    `
            });
            playSuccessSound();
        </script>
    <?php endif; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.querySelector('.rfid-input');
            if (input) {
                input.focus();
                // kalau scanner mengirim "Enter" otomatis submit form
                input.addEventListener('keypress', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        this.form.submit();
                    }
                });
            }
        });
    </script>
</body>

</html>