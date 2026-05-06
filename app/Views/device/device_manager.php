<!-- [ Main Content ] start -->
<div class="pcoded-main-container">
    <div class="pcoded-wrapper">
        <div class="pcoded-content">
            <div class="pcoded-inner-content">
                <div class="main-body">
                    <div class="page-wrapper">
                        <div class="page-header">
                            <div class="page-block">
                                <div class="row align-items-center">
                                    <div class="col-md-12">
                                        <div class="page-header-title">
                                            <h5 class="m-b-10">Manajemen Perangkat Scanner</h5>
                                        </div>
                                        <ul class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="<?= base_url('Home'); ?>"><i class="feather icon-home"></i></a></li>
                                            <li class="breadcrumb-item"><a href="#">Manajemen Perangkat</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <style>
                            .device-card {
                                border: none;
                                border-radius: 16px;
                                overflow: hidden;
                                box-shadow: 0 4px 24px rgba(0,0,0,0.06);
                                transition: all 0.3s ease;
                            }
                            .device-card:hover {
                                box-shadow: 0 8px 32px rgba(0,0,0,0.12);
                                transform: translateY(-2px);
                            }
                            .device-card .card-header {
                                border: none;
                                padding: 1.2rem 1.5rem;
                                font-weight: 700;
                                font-size: 1.05rem;
                                letter-spacing: 0.5px;
                            }
                            .device-card .card-header .badge {
                                font-size: 0.75rem;
                                padding: 5px 12px;
                                border-radius: 20px;
                            }
                            .device-header-rfid { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; }
                            .device-header-barcode { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: #fff; }
                            .device-header-camera { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: #fff; }

                            .device-slot {
                                display: flex;
                                align-items: center;
                                gap: 10px;
                                padding: 12px 16px;
                                border-bottom: 1px solid #f0f0f0;
                                transition: background 0.2s;
                            }
                            .device-slot:last-child { border-bottom: none; }
                            .device-slot:hover { background: #f8f9fe; }

                            .device-slot .slot-number {
                                width: 36px;
                                height: 36px;
                                border-radius: 10px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                font-weight: 700;
                                font-size: 0.85rem;
                                flex-shrink: 0;
                            }
                            .slot-rfid .slot-number { background: rgba(102,126,234,0.12); color: #667eea; }
                            .slot-barcode .slot-number { background: rgba(245,87,108,0.12); color: #f5576c; }
                            .slot-camera .slot-number { background: rgba(79,172,254,0.12); color: #4facfe; }

                            .device-slot .input-group { flex: 1; }
                            .device-slot .form-control {
                                border-radius: 8px;
                                border: 2px solid #e9ecef;
                                font-size: 0.85rem;
                                padding: 8px 12px;
                                transition: border-color 0.2s;
                            }
                            .device-slot .form-control:focus {
                                border-color: #667eea;
                                box-shadow: 0 0 0 3px rgba(102,126,234,0.1);
                            }
                            .device-slot .form-control:disabled {
                                background: #f8f9fa;
                                color: #999;
                                cursor: not-allowed;
                            }

                            .btn-lock {
                                width: 38px;
                                height: 38px;
                                border-radius: 10px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                border: 2px solid #e9ecef;
                                background: #fff;
                                color: #6c757d;
                                cursor: pointer;
                                transition: all 0.2s;
                                flex-shrink: 0;
                            }
                            .btn-lock:hover { border-color: #667eea; color: #667eea; background: rgba(102,126,234,0.05); }
                            .btn-lock.locked { border-color: #dc3545; color: #dc3545; background: rgba(220,53,69,0.05); }
                            .btn-lock.locked:hover { background: rgba(220,53,69,0.1); }

                            .btn-status {
                                width: 38px;
                                height: 38px;
                                border-radius: 10px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                border: 2px solid #e9ecef;
                                background: #fff;
                                cursor: pointer;
                                transition: all 0.2s;
                                flex-shrink: 0;
                            }
                            .btn-status.online { border-color: #28a745; color: #28a745; background: rgba(40,167,69,0.05); }
                            .btn-status.offline { border-color: #6c757d; color: #6c757d; }
                            .btn-status:hover { opacity: 0.8; }

                            .status-dot {
                                width: 8px; height: 8px;
                                border-radius: 50%;
                                display: inline-block;
                                margin-right: 6px;
                            }
                            .status-dot.online { background: #28a745; box-shadow: 0 0 6px rgba(40,167,69,0.5); animation: pulse-green 2s infinite; }
                            .status-dot.offline { background: #6c757d; }

                            @keyframes pulse-green {
                                0%, 100% { box-shadow: 0 0 4px rgba(40,167,69,0.4); }
                                50% { box-shadow: 0 0 12px rgba(40,167,69,0.8); }
                            }

                            .summary-card {
                                border: none;
                                border-radius: 14px;
                                padding: 20px;
                                text-align: center;
                                box-shadow: 0 4px 16px rgba(0,0,0,0.06);
                                overflow: hidden;
                                position: relative;
                            }
                            .summary-card h2 { font-size: 2rem; font-weight: 800; margin-bottom: 4px; }
                            .summary-card span { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 600; }
                        </style>

                        <!-- Summary Cards -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="summary-card" style="background-color: #ffffff; background-image: linear-gradient(135deg, #667eea22, #764ba222);">
                                    <h2 style="color:#667eea;" id="totalDevices">15</h2>
                                    <span style="color:#764ba2;">Total Perangkat</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="summary-card" style="background-color: #ffffff; background-image: linear-gradient(135deg, #28a74522, #20c99722);">
                                    <h2 style="color:#28a745;" id="onlineCount">0</h2>
                                    <span style="color:#20c997;">Terhubung</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="summary-card" style="background-color: #ffffff; background-image: linear-gradient(135deg, #dc354522, #e8437522);">
                                    <h2 style="color:#dc3545;" id="lockedCount">0</h2>
                                    <span style="color:#e84375;">Terkunci</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="summary-card" style="background-color: #ffffff; background-image: linear-gradient(135deg, #ffc10722, #fd7e1422);">
                                    <h2 style="color:#fd7e14;" id="offlineCount">15</h2>
                                    <span style="color:#ffc107;">Tidak Aktif</span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <?php
                            $typeConfig = [
                                'rfid' => ['label' => 'RFID Scanner', 'icon' => '📡', 'header' => 'device-header-rfid', 'slot' => 'slot-rfid'],
                                'barcode' => ['label' => 'Barcode Scanner', 'icon' => '🔖', 'header' => 'device-header-barcode', 'slot' => 'slot-barcode'],
                                'camera' => ['label' => 'Camera / QR Scanner', 'icon' => '📷', 'header' => 'device-header-camera', 'slot' => 'slot-camera'],
                            ];
                            foreach ($typeConfig as $type => $cfg):
                                $slots = $devices[$type] ?? [];
                                $onlineSlots = array_filter($slots, fn($s) => $s['device_status'] == 1);
                            ?>
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card device-card">
                                    <div class="card-header <?= $cfg['header'] ?> d-flex justify-content-between align-items-center">
                                        <span><?= $cfg['icon'] ?> <?= $cfg['label'] ?></span>
                                        <span class="badge badge-light"><?= count($onlineSlots) ?>/<?= count($slots) ?> Online</span>
                                    </div>
                                    <div class="card-body p-0">
                                        <?php foreach ($slots as $slot): ?>
                                        <div class="device-slot <?= $cfg['slot'] ?>" data-id="<?= $slot['id'] ?>">
                                            <div class="slot-number"><?= $slot['slot_number'] ?></div>
                                            <div class="input-group">
                                                <input type="text"
                                                    class="form-control device-name"
                                                    placeholder="Nama Perangkat"
                                                    value="<?= esc($slot['device_name']) ?>"
                                                    data-id="<?= $slot['id'] ?>"
                                                    data-field="device_name"
                                                    <?= $slot['is_locked'] ? 'disabled' : '' ?>
                                                >
                                            </div>
                                            <div class="input-group" style="max-width:140px;">
                                                <input type="text"
                                                    class="form-control device-ip"
                                                    placeholder="IP Address"
                                                    value="<?= esc($slot['device_ip']) ?>"
                                                    data-id="<?= $slot['id'] ?>"
                                                    data-field="device_ip"
                                                    <?= $slot['is_locked'] ? 'disabled' : '' ?>
                                                >
                                            </div>
                                            <button class="btn-status <?= $slot['device_status'] ? 'online' : 'offline' ?>"
                                                data-id="<?= $slot['id'] ?>"
                                                title="<?= $slot['device_status'] ? 'Online - Klik untuk nonaktifkan' : 'Offline - Klik untuk aktifkan' ?>">
                                                <span class="status-dot <?= $slot['device_status'] ? 'online' : 'offline' ?>"></span>
                                            </button>
                                            <button class="btn-lock <?= $slot['is_locked'] ? 'locked' : '' ?>"
                                                data-id="<?= $slot['id'] ?>"
                                                title="<?= $slot['is_locked'] ? 'Terkunci - Klik untuk buka' : 'Terbuka - Klik untuk kunci' ?>">
                                                <i class="feather <?= $slot['is_locked'] ? 'icon-lock' : 'icon-unlock' ?>"></i>
                                            </button>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Connection Log -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card device-card">
                                    <div class="card-header" style="background: linear-gradient(135deg, #2d3436, #636e72); color:#fff;">
                                        🖥️ Log Koneksi Terakhir
                                    </div>
                                    <div class="card-body p-0">
                                        <div style="max-height:200px; overflow-y:auto; font-family: 'Courier New', monospace; font-size:0.8rem; padding:16px; background:#1e272e; color:#dfe6e9;" id="connectionLog">
                                            <div style="color:#00b894;">[<?= date('H:i:s') ?>] System initialized. Waiting for device connections...</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function updateSummary() {
    const total = document.querySelectorAll('.device-slot').length;
    const online = document.querySelectorAll('.btn-status.online').length;
    const locked = document.querySelectorAll('.btn-lock.locked').length;
    document.getElementById('totalDevices').textContent = total;
    document.getElementById('onlineCount').textContent = online;
    document.getElementById('lockedCount').textContent = locked;
    document.getElementById('offlineCount').textContent = total - online;
}

function addLog(msg, color) {
    const log = document.getElementById('connectionLog');
    const time = new Date().toLocaleTimeString('id-ID');
    const line = document.createElement('div');
    line.style.color = color || '#dfe6e9';
    line.textContent = '[' + time + '] ' + msg;
    log.appendChild(line);
    log.scrollTop = log.scrollHeight;
}

// Save on blur
document.querySelectorAll('.device-name, .device-ip').forEach(input => {
    input.addEventListener('change', function() {
        const id = this.dataset.id;
        const field = this.dataset.field;
        const value = this.value;
        fetch('<?= base_url("DeviceManager/update") ?>', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
            body: 'id=' + id + '&field=' + field + '&value=' + encodeURIComponent(value)
        }).then(r => r.json()).then(data => {
            if(data.status) {
                addLog('Device #' + id + ' ' + field + ' updated to "' + value + '"', '#74b9ff');
            }
        });
    });
});

// Toggle lock
document.querySelectorAll('.btn-lock').forEach(btn => {
    btn.addEventListener('click', async function() {
        const id = this.dataset.id;
        const slot = this.closest('.device-slot');
        
        const { value: code } = await Swal.fire({
            title: 'Masukkan Kode Akses',
            input: 'password',
            inputLabel: 'Kode akses diperlukan untuk mengubah pengaturan ini',
            inputPlaceholder: 'Masukkan PIN',
            showCancelButton: true,
            confirmButtonText: 'Submit',
            cancelButtonText: 'Batal',
            inputValidator: (value) => {
                if (!value) {
                    return 'Kode akses tidak boleh kosong!'
                }
                if (value !== '696969') {
                    return 'Kode akses salah!'
                }
            }
        });

        if (code === '696969') {
            fetch('<?= base_url("DeviceManager/toggleLock") ?>', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
                body: 'id=' + id
            }).then(r => r.json()).then(data => {
                if(data.status) {
                    const icon = this.querySelector('i');
                    const inputs = slot.querySelectorAll('.form-control');
                    if(data.locked) {
                        this.classList.add('locked');
                        icon.className = 'feather icon-lock';
                        inputs.forEach(i => i.disabled = true);
                        addLog('Device #' + id + ' LOCKED 🔒', '#e17055');
                        Swal.fire('Berhasil', 'Perangkat berhasil dikunci.', 'success');
                    } else {
                        this.classList.remove('locked');
                        icon.className = 'feather icon-unlock';
                        inputs.forEach(i => i.disabled = false);
                        addLog('Device #' + id + ' UNLOCKED 🔓', '#00b894');
                        Swal.fire('Berhasil', 'Perangkat berhasil dibuka.', 'success');
                    }
                    updateSummary();
                }
            });
        }
    });
});

// Toggle status (online/offline)
document.querySelectorAll('.btn-status').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        fetch('<?= base_url("DeviceManager/toggleStatus") ?>', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
            body: 'id=' + id
        }).then(r => r.json()).then(data => {
            if(data.status) {
                const dot = this.querySelector('.status-dot');
                if(data.device_status) {
                    this.classList.add('online');
                    this.classList.remove('offline');
                    dot.classList.add('online');
                    dot.classList.remove('offline');
                    addLog('Device #' + id + ' connected ✅', '#00b894');
                } else {
                    this.classList.remove('online');
                    this.classList.add('offline');
                    dot.classList.remove('online');
                    dot.classList.add('offline');
                    addLog('Device #' + id + ' disconnected ❌', '#e17055');
                }
                updateSummary();
            }
        });
    });
});

// Init summary
updateSummary();
</script>
