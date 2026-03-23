<!-- [ Main Content ] start -->
<div class="pcoded-main-container">
    <div class="pcoded-wrapper">
        <div class="pcoded-content">
            <div class="pcoded-inner-content">
                <div class="main-body">
                    <div class="page-wrapper">
                        <!-- [ breadcrumb ] start -->
                        <div class="page-header">
                            <div class="page-block">
                                <div class="row align-items-center">
                                    <div class="col-md-12">
                                        <div class="page-header-title">
                                            <h5 class="m-b-10">
                                                <?= $title; ?>
                                            </h5>
                                        </div>
                                        <ul class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="<?= base_url('Home'); ?>"><i
                                                        class="feather icon-home"></i></a></li>
                                            <li class="breadcrumb-item"><a
                                                    href="<?= base_url('WaNotification'); ?>">WhatsApp</a></li>
                                            <li class="breadcrumb-item"><a href="#">
                                                    <?= $nav; ?>
                                                </a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- [ breadcrumb ] end -->

                        <!-- Flash Messages -->
                        <?php if (session()->get('success')): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="feather icon-check-circle mr-2"></i>
                                <?= session()->getFlashdata('success'); ?>
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="<?= base_url('WaNotification/saveSettings') ?>">
                            <div class="row">
                                <!-- Gateway Settings -->
                                <div class="col-lg-6">
                                    <div class="card shadow-sm" style="border-radius: 15px; border: none;">
                                        <div class="card-header bg-white border-0"
                                            style="border-radius: 15px 15px 0 0;">
                                            <h5 class="mb-0"><i
                                                    class="feather icon-server mr-2 text-primary"></i>Pengaturan Gateway
                                                GOWA
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="alert alert-info" style="border-radius: 10px;">
                                                <i class="feather icon-info mr-2"></i>
                                                Menggunakan <a
                                                    href="https://github.com/aldinokemal/go-whatsapp-web-multidevice"
                                                    target="_blank">go-whatsapp-web-multidevice (GOWA)</a> gateway.
                                                Pastikan server gateway sudah berjalan.
                                            </div>

                                            <div class="form-group">
                                                <label><i class="feather icon-link mr-1"></i> Gateway URL</label>
                                                <input type="url" name="gateway_url" class="form-control"
                                                    style="border-radius: 10px;" placeholder="http://localhost:3000"
                                                    value="<?= $settings['gateway_url'] ?? '' ?>">
                                                <small class="text-muted">URL server GOWA Gateway (default:
                                                    http://localhost:3000)</small>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label><i class="feather icon-user mr-1"></i> Basic Auth
                                                            User</label>
                                                        <input type="text" name="basic_auth_user" class="form-control"
                                                            style="border-radius: 10px;" placeholder="admin"
                                                            value="<?= $settings['basic_auth_user'] ?? '' ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label><i class="feather icon-key mr-1"></i> Basic Auth
                                                            Password</label>
                                                        <input type="password" name="basic_auth_pass"
                                                            class="form-control" style="border-radius: 10px;"
                                                            placeholder="password"
                                                            value="<?= $settings['basic_auth_pass'] ?? '' ?>">
                                                    </div>
                                                </div>
                                            </div>

                                            <hr class="my-4">

                                            <h6 class="mb-3"><i class="feather icon-smartphone mr-2"></i>Pengirim 1
                                                (Utama)</h6>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Nomor WhatsApp</label>
                                                        <input type="text" name="sender_number_1" class="form-control"
                                                            style="border-radius: 10px;" placeholder="6281234567890"
                                                            value="<?= $settings['sender_number_1'] ?? $settings['sender_number'] ?? '' ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Device ID</label>
                                                        <input type="text" name="device_id_1" class="form-control"
                                                            style="border-radius: 10px;"
                                                            placeholder="6281234567890@s.whatsapp.net"
                                                            value="<?= $settings['device_id_1'] ?? '' ?>">
                                                    </div>
                                                </div>
                                            </div>

                                            <h6 class="mb-3"><i class="feather icon-smartphone mr-2"></i>Pengirim 2
                                                (Backup/Load Balancing)</h6>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Nomor WhatsApp</label>
                                                        <input type="text" name="sender_number_2" class="form-control"
                                                            style="border-radius: 10px;"
                                                            placeholder="6289876543210 (opsional)"
                                                            value="<?= $settings['sender_number_2'] ?? '' ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Device ID</label>
                                                        <input type="text" name="device_id_2" class="form-control"
                                                            style="border-radius: 10px;"
                                                            placeholder="6289876543210@s.whatsapp.net"
                                                            value="<?= $settings['device_id_2'] ?? '' ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <small class="text-muted d-block mb-3">
                                                <i class="feather icon-info mr-1"></i>
                                                Menggunakan 2 pengirim akan membagi beban pengiriman (~107 pesan/hari
                                                per nomor).
                                                Kosongkan Pengirim 2 jika hanya menggunakan 1 nomor.
                                            </small>

                                            <hr class="my-4">

                                            <h6 class="mb-3"><i class="feather icon-sliders mr-2"></i>Pengaturan
                                                Pengiriman</h6>

                                            <div class="form-group">
                                                <label><i class="feather icon-clock mr-1"></i> Delay Antar Pesan
                                                    (detik)</label>
                                                <input type="number" name="message_delay" class="form-control"
                                                    style="border-radius: 10px;"
                                                    value="<?= $settings['message_delay'] ?? '30' ?>" min="10"
                                                    max="120">
                                                <small class="text-muted">Jeda waktu antar pengiriman pesan (min 10
                                                    detik)</small>
                                            </div>

                                            <div class="form-group">
                                                <label><i class="feather icon-calendar mr-1"></i> Distribusi Pengiriman
                                                    (hari)</label>
                                                <input type="number" name="distribution_days" class="form-control"
                                                    style="border-radius: 10px;"
                                                    value="<?= $settings['distribution_days'] ?? '7' ?>" min="1"
                                                    max="14">
                                                <small class="text-muted">Pesan akan didistribusikan dalam beberapa hari
                                                    untuk menghindari ban</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Schedule Settings -->
                                    <div class="card shadow-sm mt-4" style="border-radius: 15px; border: none;">
                                        <div class="card-header bg-white border-0"
                                            style="border-radius: 15px 15px 0 0;">
                                            <h5 class="mb-0"><i class="feather icon-calendar mr-2 text-info"></i>Jadwal
                                                Otomatis</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Hari Generate</label>
                                                        <select name="schedule_day" class="form-control"
                                                            style="border-radius: 10px;">
                                                            <?php $days = ['Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat']; ?>
                                                            <?php foreach ($days as $val => $label): ?>
                                                                <option value="<?= $val ?>" <?= ($settings['schedule_day'] ?? 'Friday') == $val ? 'selected' : '' ?>>
                                                                    <?= $label ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Jam Generate</label>
                                                        <input type="time" name="schedule_time" class="form-control"
                                                            style="border-radius: 10px;"
                                                            value="<?= $settings['schedule_time'] ?? '16:00' ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="alert alert-info" style="border-radius: 10px;">
                                                <i class="feather icon-info mr-2"></i>
                                                Laporan mingguan akan di-generate setiap <strong>
                                                    <?= $days[$settings['schedule_day'] ?? 'Friday'] ?? 'Jumat' ?>
                                                </strong> pukul <strong>
                                                    <?= $settings['schedule_time'] ?? '16:00' ?>
                                                </strong>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- WA Channel Settings -->
                                    <div class="card shadow-sm mt-4" style="border-radius: 15px; border: none;">
                                        <div class="card-header bg-white border-0"
                                            style="border-radius: 15px 15px 0 0;">
                                            <h5 class="mb-0"><i class="feather icon-message-circle mr-2 text-success"></i>WA Channel
                                                - Notifikasi Harian Siswa Tidak Hadir</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="alert alert-success" style="border-radius: 10px;">
                                                <i class="feather icon-info mr-2"></i>
                                                Daftar siswa tidak hadir akan dikirim ke WA Channel setiap <strong>jam 10 pagi</strong> (Senin-Sabtu).
                                                <br>Buat channel WA dari masing-masing nomor pengirim, lalu isi Channel JID di bawah.
                                            </div>

                                            <div class="form-group">
                                                <label><i class="feather icon-hash mr-1"></i> Channel JID Pengirim 1</label>
                                                <input type="text" name="channel_jid_1" class="form-control"
                                                    style="border-radius: 10px;" placeholder="120363xxxxxxxxx@newsletter"
                                                    value="<?= $settings['channel_jid_1'] ?? '' ?>">
                                                <small class="text-muted">JID channel yang dibuat dari Nomor Pengirim 1</small>
                                            </div>

                                            <div class="form-group">
                                                <label><i class="feather icon-hash mr-1"></i> Channel JID Pengirim 2</label>
                                                <input type="text" name="channel_jid_2" class="form-control"
                                                    style="border-radius: 10px;" placeholder="120363xxxxxxxxx@newsletter (opsional)"
                                                    value="<?= $settings['channel_jid_2'] ?? '' ?>">
                                                <small class="text-muted">JID channel yang dibuat dari Nomor Pengirim 2</small>
                                            </div>

                                            <div class="alert alert-warning mt-3" style="border-radius: 10px;">
                                                <h6 class="alert-heading"><i class="feather icon-terminal mr-2"></i>Cron Setup</h6>
                                                <code>0 10 * * 1-6 curl -s <?= base_url('DailyAbsentNotification/send') ?></code>
                                                <br><small>Untuk test: <a href="<?= base_url('DailyAbsentNotification/preview') ?>" target="_blank">Preview Pesan</a></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Message Template -->
                                <div class="col-lg-6">
                                    <div class="card shadow-sm" style="border-radius: 15px; border: none;">
                                        <div class="card-header bg-white border-0"
                                            style="border-radius: 15px 15px 0 0;">
                                            <h5 class="mb-0"><i
                                                    class="feather icon-edit-3 mr-2 text-success"></i>Template Pesan
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label>Template Pesan WhatsApp</label>
                                                <textarea name="message_template" class="form-control" rows="15"
                                                    style="border-radius: 10px; font-family: monospace;"><?= htmlspecialchars($messageTemplate) ?></textarea>
                                                <small class="text-muted">Variabel yang tersedia: {nama_siswa}, {nis},
                                                    {kelas}, {periode}, {attendance_list}</small>
                                            </div>

                                            <div class="alert alert-warning" style="border-radius: 10px;">
                                                <h6 class="alert-heading"><i
                                                        class="feather icon-alert-triangle mr-2"></i>Format WhatsApp
                                                </h6>
                                                <ul class="mb-0 pl-3">
                                                    <li><code>*teks*</code> untuk <strong>bold</strong></li>
                                                    <li><code>_teks_</code> untuk <em>italic</em></li>
                                                    <li><code>~teks~</code> untuk <del>strikethrough</del></li>
                                                    <li>Gunakan emoji untuk tampilan lebih menarik</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Gateway Status -->
                                    <div class="card shadow-sm mt-4" style="border-radius: 15px; border: none;">
                                        <div class="card-header bg-white border-0"
                                            style="border-radius: 15px 15px 0 0;">
                                            <h5 class="mb-0"><i
                                                    class="feather icon-activity mr-2 text-warning"></i>Status Gateway
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="text-center py-4">
                                                <?php $gatewayStatus = $settings['gateway_status'] ?? 'disconnected'; ?>
                                                <div class="status-icon mb-3"
                                                    style="width: 80px; height: 80px; background: <?= $gatewayStatus == 'connected' ? 'linear-gradient(135deg, #11998e, #38ef7d)' : 'linear-gradient(135deg, #636e72, #b2bec3)' ?>; border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                                                    <i class="feather icon-<?= $gatewayStatus == 'connected' ? 'wifi' : 'wifi-off' ?> text-white"
                                                        style="font-size: 32px;"></i>
                                                </div>
                                                <h5
                                                    class="<?= $gatewayStatus == 'connected' ? 'text-success' : 'text-muted' ?>">
                                                    <?= $gatewayStatus == 'connected' ? 'Terhubung' : 'Tidak Terhubung' ?>
                                                </h5>
                                                <p class="text-muted mb-0">
                                                    <?php if ($gatewayStatus == 'connected'): ?>
                                                        Gateway siap mengirim pesan
                                                    <?php else: ?>
                                                        Pastikan server gateway berjalan
                                                    <?php endif; ?>
                                                </p>
                                                <button type="button" class="btn btn-outline-primary mt-3"
                                                    style="border-radius: 10px;" onclick="checkGateway()">
                                                    <i class="feather icon-refresh-cw mr-1"></i> Cek Koneksi
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Save Button -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card shadow-sm" style="border-radius: 15px; border: none;">
                                        <div class="card-body d-flex justify-content-between align-items-center">
                                            <div>
                                                <a href="<?= base_url('WaNotification') ?>"
                                                    class="btn btn-outline-secondary" style="border-radius: 10px;">
                                                    <i class="feather icon-arrow-left mr-1"></i> Kembali
                                                </a>
                                            </div>
                                            <div>
                                                <button type="submit" class="btn btn-primary btn-lg"
                                                    style="border-radius: 10px; padding: 12px 40px;">
                                                    <i class="feather icon-save mr-2"></i> Simpan Pengaturan
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        transition: transform 0.3s ease;
    }

    .card:hover {
        transform: translateY(-3px);
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .btn {
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
    }
</style>

<script>
    function checkGateway() {
        const url = document.querySelector('input[name="gateway_url"]').value;
        if (!url) {
            alert('Masukkan Gateway URL terlebih dahulu');
            return;
        }

        const btn = event.target;
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="feather icon-loader mr-1"></i> Memeriksa...';
        btn.disabled = true;

        fetch('<?= base_url("WaNotification/testGateway") ?>', {
            method: 'GET',
            headers: { 'Accept': 'application/json' }
        })
            .then(response => response.json())
            .then(data => {
                // Update visual status icon
                const statusIcon = document.querySelector('.status-icon');
                const statusTitle = statusIcon.parentElement.querySelector('h5');
                const statusDesc = statusIcon.parentElement.querySelector('p');
                const iconEl = statusIcon.querySelector('i');

                if (data.gateway_connected) {
                    statusIcon.style.background = 'linear-gradient(135deg, #11998e, #38ef7d)';
                    iconEl.className = 'feather icon-wifi text-white';
                    statusTitle.className = 'text-success';
                    statusTitle.textContent = 'Terhubung';
                    statusDesc.textContent = 'Gateway siap mengirim pesan';
                } else {
                    statusIcon.style.background = 'linear-gradient(135deg, #636e72, #b2bec3)';
                    iconEl.className = 'feather icon-wifi-off text-white';
                    statusTitle.className = 'text-muted';
                    statusTitle.textContent = 'Tidak Terhubung';
                    statusDesc.textContent = 'Pastikan server gateway berjalan';
                }

                // Show detail alert
                let message = 'Status Gateway:\n\n';
                message += 'URL: ' + data.gateway_url + '\n\n';

                message += 'Pengirim 1:\n';
                if (data.sender_1.connected && data.sender_1.logged_in) {
                    message += '✅ Terhubung & Login\n';
                } else if (data.sender_1.connected) {
                    message += '⚠️ Terhubung tapi belum login\n';
                } else {
                    message += '❌ Tidak terhubung\n';
                    if (data.sender_1.error) message += '   Error: ' + data.sender_1.error + '\n';
                }

                message += '\nPengirim 2:\n';
                if (data.sender_2.connected && data.sender_2.logged_in) {
                    message += '✅ Terhubung & Login\n';
                } else if (data.sender_2.connected) {
                    message += '⚠️ Terhubung tapi belum login\n';
                } else if (data.sender_2.error && !data.sender_2.error.includes('not found')) {
                    message += '❌ Tidak terhubung\n';
                    message += '   Error: ' + data.sender_2.error + '\n';
                } else {
                    message += '➖ Tidak dikonfigurasi\n';
                }

                alert(message);
            })
            .catch(error => {
                alert('Gagal memeriksa gateway:\n' + error.message);
            })
            .finally(() => {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            });
    }
</script>