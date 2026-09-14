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
                                            <h5 class="m-b-10"><?= $title; ?></h5>
                                        </div>
                                        <ul class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="<?= base_url('Home'); ?>"><i class="feather icon-home"></i></a></li>
                                            <li class="breadcrumb-item"><a href="#">WhatsApp</a></li>
                                            <li class="breadcrumb-item"><a href="<?= base_url('WaNotification'); ?>"><?= $nav; ?></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- [ breadcrumb ] end -->

                        <!-- Flash Messages -->
                        <?php if(session()->get('success')) : ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="feather icon-check-circle mr-2"></i><?= session()->getFlashdata('success'); ?>
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                            </div>
                        <?php endif; ?>

                        <?php if(session()->get('error')) : ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="feather icon-alert-circle mr-2"></i><?= session()->getFlashdata('error'); ?>
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                            </div>
                        <?php endif; ?>

                        <!-- [ Main Content ] start -->
                        
                        <!-- Gateway Status Banner -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card bg-gradient-dark text-white border-0 shadow-lg" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);">
                                    <div class="card-body py-4">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <div class="wa-icon-container" style="width: 70px; height: 70px; background: linear-gradient(135deg, #25D366, #128C7E); border-radius: 20px; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 30px rgba(37, 211, 102, 0.3);">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="white" viewBox="0 0 16 16">
                                                        <path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <h4 class="mb-1 text-white font-weight-bold">WhatsApp Notification Gateway</h4>
                                                <p class="mb-0 text-white-50">
                                                    <span class="badge badge-<?= ($gatewaySettings['gateway_status'] ?? 'disconnected') == 'connected' ? 'success' : 'secondary' ?> mr-2">
                                                        <i class="feather icon-<?= ($gatewaySettings['gateway_status'] ?? 'disconnected') == 'connected' ? 'wifi' : 'wifi-off' ?> mr-1"></i>
                                                        <?= ucfirst($gatewaySettings['gateway_status'] ?? 'Disconnected') ?>
                                                    </span>
                                                    Kirim notifikasi otomatis ke orang tua siswa setiap minggu
                                                </p>
                                            </div>
                                            <div class="col-auto">
                                                <a href="<?= base_url('WaNotification/settings') ?>" class="btn btn-light btn-rounded shadow-sm">
                                                    <i class="feather icon-settings mr-1"></i> Pengaturan
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Stats Cards -->
                        <div class="row">
                            <!-- Today's Queue -->
                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-gradient-primary text-white overflow-hidden" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 15px;">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col">
                                                <h6 class="text-uppercase text-white-50 mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">Jadwal Hari Ini</h6>
                                                <span class="h2 font-weight-bold mb-0 text-white" id="stat-today"><?= $stats['today'] ?></span>
                                            </div>
                                            <div class="col-auto">
                                                <div class="icon-shape" style="width: 50px; height: 50px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="feather icon-clock text-white" style="font-size: 24px;"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="mt-3 mb-0 text-sm text-white-50">
                                            <span class="text-success-light mr-2"><i class="feather icon-arrow-up"></i> <?= $stats['todaySent'] ?> terkirim</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Pending -->
                            <div class="col-xl-3 col-md-6">
                                <div class="card overflow-hidden" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 15px;">
                                    <div class="card-body text-white">
                                        <div class="row">
                                            <div class="col">
                                                <h6 class="text-uppercase text-white-50 mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">Menunggu</h6>
                                                <span class="h2 font-weight-bold mb-0" id="stat-pending"><?= $stats['pending'] ?></span>
                                            </div>
                                            <div class="col-auto">
                                                <div class="icon-shape" style="width: 50px; height: 50px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="feather icon-loader text-white" style="font-size: 24px;"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="<?= base_url('WaNotification/queue?status=pending') ?>" class="stretched-link" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0;"></a>
                                    </div>
                                </div>
                            </div>

                            <!-- Sent -->
                            <div class="col-xl-3 col-md-6">
                                <div class="card overflow-hidden" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); border-radius: 15px;">
                                    <div class="card-body text-white">
                                        <div class="row">
                                            <div class="col">
                                                <h6 class="text-uppercase text-white-50 mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">Terkirim</h6>
                                                <span class="h2 font-weight-bold mb-0" id="stat-sent"><?= $stats['sent'] ?></span>
                                            </div>
                                            <div class="col-auto">
                                                <div class="icon-shape" style="width: 50px; height: 50px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="feather icon-check-circle text-white" style="font-size: 24px;"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="<?= base_url('WaNotification/queue?status=sent') ?>" class="stretched-link" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0;"></a>
                                    </div>
                                </div>
                            </div>

                            <!-- Failed -->
                            <div class="col-xl-3 col-md-6">
                                <div class="card overflow-hidden" style="background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%); border-radius: 15px;">
                                    <div class="card-body text-white">
                                        <div class="row">
                                            <div class="col">
                                                <h6 class="text-uppercase text-white-50 mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">Gagal</h6>
                                                <span class="h2 font-weight-bold mb-0" id="stat-failed"><?= $stats['failed'] ?></span>
                                            </div>
                                            <div class="col-auto">
                                                <div class="icon-shape" style="width: 50px; height: 50px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="feather icon-alert-triangle text-white" style="font-size: 24px;"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if($stats['failed'] > 0): ?>
                                        <a href="<?= base_url('WaNotification/resendFailed') ?>" class="btn btn-sm btn-light mt-2" style="border-radius: 20px;">
                                            <i class="feather icon-refresh-cw mr-1"></i> Kirim Ulang
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions & Preview -->
                        <div class="row">
                            <!-- Quick Actions -->
                            <div class="col-lg-4">
                                <div class="card shadow-sm" style="border-radius: 15px; border: none;">
                                    <div class="card-header bg-white border-0 pb-0" style="border-radius: 15px 15px 0 0;">
                                        <h5 class="mb-0"><i class="feather icon-zap text-warning mr-2"></i>Aksi Cepat</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-grid gap-2">
                                            <a href="<?= base_url('WeeklyReport/generate') ?>" class="btn btn-lg btn-primary mb-3" style="border-radius: 12px; padding: 15px;">
                                                <i class="feather icon-send mr-2"></i> Generate Laporan Mingguan
                                            </a>
                                            <a href="<?= base_url('WaNotification/queue') ?>" class="btn btn-lg btn-outline-secondary mb-3" style="border-radius: 12px; padding: 15px;">
                                                <i class="feather icon-list mr-2"></i> Lihat Antrian Pesan
                                            </a>
                                            <a href="<?= base_url('WaNotification/schedule') ?>" class="btn btn-lg btn-outline-info mb-3" style="border-radius: 12px; padding: 15px;">
                                                <i class="feather icon-calendar mr-2"></i> Jadwal Broadcast
                                            </a>
                                            <a href="<?= base_url('WaNotification/settings') ?>" class="btn btn-lg btn-outline-dark" style="border-radius: 12px; padding: 15px;">
                                                <i class="feather icon-settings mr-2"></i> Pengaturan
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Search Student for Preview -->
                                <div class="card shadow-sm mt-4" style="border-radius: 15px; border: none;">
                                    <div class="card-header bg-white border-0 pb-0" style="border-radius: 15px 15px 0 0;">
                                        <h5 class="mb-0"><i class="feather icon-eye text-info mr-2"></i>Preview Pesan</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label>Cari Siswa</label>
                                            <input type="text" class="form-control" id="searchStudent" placeholder="Ketik nama atau NIS..." style="border-radius: 10px;">
                                        </div>
                                        <div id="studentResults" class="list-group" style="max-height: 200px; overflow-y: auto;"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Recent Activity -->
                            <div class="col-lg-8">
                                <div class="card shadow-sm" style="border-radius: 15px; border: none;">
                                    <div class="card-header bg-white border-0" style="border-radius: 15px 15px 0 0;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0"><i class="feather icon-activity text-primary mr-2"></i>Aktivitas Terbaru</h5>
                                            <ul class="nav nav-pills card-header-pills" id="activityTab" role="tablist">
                                                <li class="nav-item">
                                                    <a class="nav-link active" data-toggle="pill" href="#pending-tab" style="border-radius: 20px; padding: 5px 15px;">Menunggu</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" data-toggle="pill" href="#sent-tab" style="border-radius: 20px; padding: 5px 15px;">Terkirim</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" data-toggle="pill" href="#failed-tab" style="border-radius: 20px; padding: 5px 15px;">Gagal</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="tab-content" id="activityTabContent">
                                            <!-- Pending Tab -->
                                            <div class="tab-pane fade show active" id="pending-tab">
                                                <?php if(empty($recentPending)): ?>
                                                    <div class="text-center py-5 text-muted">
                                                        <i class="feather icon-inbox" style="font-size: 48px;"></i>
                                                        <p class="mt-3">Tidak ada pesan menunggu</p>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="table-responsive">
                                                        <table class="table table-hover">
                                                            <thead class="thead-light">
                                                                <tr>
                                                                    <th>No HP</th>
                                                                    <th>Jadwal</th>
                                                                    <th>Periode</th>
                                                                    <th>Aksi</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach($recentPending as $msg): ?>
                                                                <tr>
                                                                    <td><?= substr($msg['phone_number'], 0, -4) . '****' ?></td>
                                                                    <td><span class="badge badge-warning"><?= date('d M', strtotime($msg['scheduled_date'])) ?></span></td>
                                                                    <td><?= date('d/m', strtotime($msg['week_start'])) ?> - <?= date('d/m', strtotime($msg['week_end'])) ?></td>
                                                                    <td>
                                                                        <a href="<?= base_url('WaNotification/preview/' . $msg['id_siswa']) ?>" class="btn btn-sm btn-info" title="Preview">
                                                                            <i class="feather icon-eye"></i>
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                <?php endif; ?>
                                            </div>

                                            <!-- Sent Tab -->
                                            <div class="tab-pane fade" id="sent-tab">
                                                <?php if(empty($recentSent)): ?>
                                                    <div class="text-center py-5 text-muted">
                                                        <i class="feather icon-check-circle" style="font-size: 48px;"></i>
                                                        <p class="mt-3">Belum ada pesan terkirim</p>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="table-responsive">
                                                        <table class="table table-hover">
                                                            <thead class="thead-light">
                                                                <tr>
                                                                    <th>No HP</th>
                                                                    <th>Terkirim</th>
                                                                    <th>Periode</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach($recentSent as $msg): ?>
                                                                <tr>
                                                                    <td><?= substr($msg['phone_number'], 0, -4) . '****' ?></td>
                                                                    <td><span class="badge badge-success"><?= date('d M H:i', strtotime($msg['sent_at'])) ?></span></td>
                                                                    <td><?= date('d/m', strtotime($msg['week_start'])) ?> - <?= date('d/m', strtotime($msg['week_end'])) ?></td>
                                                                </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                <?php endif; ?>
                                            </div>

                                            <!-- Failed Tab -->
                                            <div class="tab-pane fade" id="failed-tab">
                                                <?php if(empty($recentFailed)): ?>
                                                    <div class="text-center py-5 text-muted">
                                                        <i class="feather icon-smile" style="font-size: 48px;"></i>
                                                        <p class="mt-3">Tidak ada pesan gagal</p>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="table-responsive">
                                                        <table class="table table-hover">
                                                            <thead class="thead-light">
                                                                <tr>
                                                                    <th>No HP</th>
                                                                    <th>Error</th>
                                                                    <th>Retry</th>
                                                                    <th>Aksi</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach($recentFailed as $msg): ?>
                                                                <tr>
                                                                    <td><?= substr($msg['phone_number'], 0, -4) . '****' ?></td>
                                                                    <td><small class="text-danger"><?= substr($msg['error_message'] ?? 'Unknown', 0, 30) ?>...</small></td>
                                                                    <td><span class="badge badge-secondary"><?= $msg['retry_count'] ?>/3</span></td>
                                                                    <td>
                                                                        <a href="<?= base_url('WaNotification/deleteMessage/' . $msg['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus pesan ini?')">
                                                                            <i class="feather icon-trash-2"></i>
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- [ Main Content ] end -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-dark { position: relative; overflow: hidden; }
    .bg-gradient-dark::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 200%;
        background: radial-gradient(circle, rgba(37, 211, 102, 0.1) 0%, transparent 70%);
        animation: pulse 4s ease-in-out infinite;
    }
    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 0.5; }
        50% { transform: scale(1.1); opacity: 0.8; }
    }
    .card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
    .card:hover { transform: translateY(-5px); box-shadow: 0 10px 40px rgba(0,0,0,0.15) !important; }
    .wa-icon-container { animation: float 3s ease-in-out infinite; }
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    .btn { transition: all 0.3s ease; }
    .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 20px rgba(0,0,0,0.2); }
    .list-group-item { cursor: pointer; transition: all 0.2s ease; }
    .list-group-item:hover { background: #f8f9fa; transform: translateX(5px); }
</style>

<script>
// Search Student
document.getElementById('searchStudent').addEventListener('input', function() {
    const keyword = this.value;
    const resultsDiv = document.getElementById('studentResults');
    
    if (keyword.length < 2) {
        resultsDiv.innerHTML = '';
        return;
    }
    
    fetch('<?= base_url('WaNotification/searchStudent') ?>?q=' + encodeURIComponent(keyword))
        .then(response => response.json())
        .then(students => {
            resultsDiv.innerHTML = '';
            students.forEach(student => {
                const item = document.createElement('a');
                item.href = '<?= base_url('WaNotification/preview/') ?>' + student.id_siswa;
                item.className = 'list-group-item list-group-item-action';
                item.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${student.nm_siswa}</strong>
                            <small class="text-muted d-block">${student.no_induk} • ${student.nm_rombel}</small>
                        </div>
                        <i class="feather icon-chevron-right text-muted"></i>
                    </div>
                `;
                resultsDiv.appendChild(item);
            });
        });
});

// Auto refresh stats every 30 seconds
setInterval(function() {
    fetch('<?= base_url('WaNotification/apiStats') ?>')
        .then(response => response.json())
        .then(stats => {
            document.getElementById('stat-today').textContent = stats.today;
            document.getElementById('stat-pending').textContent = stats.pending;
            document.getElementById('stat-sent').textContent = stats.sent;
            document.getElementById('stat-failed').textContent = stats.failed;
        });
}, 30000);
</script>
