<div class="pcoded-main-container">
    <div class="pcoded-content">
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h5 class="m-b-10">Peringatan Kehadiran Siswa</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= base_url('home') ?>"><i
                                        class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="<?= base_url('AttendanceAlert') ?>">Peringatan</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="feather icon-check-circle mr-2"></i>
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        <?php endif; ?>

        <!-- Notification Pause Banner -->
        <?php $isPaused = ($settings['notification_paused'] ?? '0') === '1'; ?>
        <?php if ($isPaused): ?>
            <div class="alert alert-warning d-flex align-items-center justify-content-between" role="alert"
                style="border-left: 5px solid #ffc107;">
                <div>
                    <i class="feather icon-pause-circle mr-2" style="font-size: 24px;"></i>
                    <strong>Notifikasi Dijeda</strong> — Scan & pengiriman WA tidak aktif.
                    <?php if (!empty($settings['notification_pause_reason'])): ?>
                        <br><small class="text-muted">Alasan: <?= esc($settings['notification_pause_reason']) ?></small>
                    <?php endif; ?>
                </div>
                <form action="<?= base_url('AttendanceAlert/togglePause') ?>" method="post" class="d-inline">
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="feather icon-play mr-1"></i>Aktifkan Kembali
                    </button>
                </form>
            </div>
        <?php else: ?>
            <div class="mb-3 text-right">
                <button class="btn btn-outline-warning btn-sm" data-toggle="modal" data-target="#pauseModal">
                    <i class="feather icon-pause-circle mr-1"></i>Jeda Notifikasi
                </button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Stats Cards -->
            <div class="col-md-6 col-xl-3">
                <div class="card bg-c-red order-card">
                    <div class="card-body">
                        <h6 class="text-white">Peringatan Baru</h6>
                        <h2 class="text-white">
                            <?= $stats['total_new'] ?? 0 ?>
                        </h2>
                        <i class="card-icon feather icon-alert-triangle"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card bg-c-yellow order-card">
                    <div class="card-body">
                        <h6 class="text-white">Sudah Dikirim ke Wali Kelas</h6>
                        <h2 class="text-white">
                            <?= $stats['total_notified_walikelas'] ?? 0 ?>
                        </h2>
                        <i class="card-icon feather icon-send"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card bg-c-blue order-card">
                    <div class="card-body">
                        <h6 class="text-white">Sudah Dikirim ke Guru BK</h6>
                        <h2 class="text-white">
                            <?= $stats['total_notified_bk'] ?? 0 ?>
                        </h2>
                        <i class="card-icon feather icon-user-check"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card bg-c-green order-card">
                    <div class="card-body">
                        <h6 class="text-white">Diselesaikan</h6>
                        <h2 class="text-white">
                            <?= $stats['total_resolved'] ?? 0 ?>
                        </h2>
                        <i class="card-icon feather icon-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5><i class="feather icon-alert-circle mr-2"></i>Daftar Peringatan</h5>
                        <div>
                            <a href="<?= base_url('AttendanceAlert/settings') ?>"
                                class="btn btn-outline-primary btn-sm mr-2">
                                <i class="feather icon-settings mr-1"></i>Pengaturan
                            </a>
                            <a href="<?= base_url('AttendanceAlert/report') ?>"
                                class="btn btn-outline-info btn-sm mr-2">
                                <i class="feather icon-file-text mr-1"></i>Laporan
                            </a>
                            <button class="btn btn-primary btn-sm mr-2" onclick="scanAlerts()">
                                <i class="feather icon-refresh-cw mr-1"></i>Scan Sekarang
                            </button>
                            <a href="<?= base_url('AttendanceAlert/deleteAll') ?>"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Yakin ingin MENGHAPUS SEMUA peringatan? Aksi ini tidak bisa dibatalkan.')">
                                <i class="feather icon-trash-2 mr-1"></i>Hapus Semua
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filter Tabs -->
                        <ul class="nav nav-tabs mb-3" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link <?= empty($currentType) ? 'active' : '' ?>"
                                    href="<?= base_url('AttendanceAlert') ?>">
                                    Semua <span class="badge badge-secondary">
                                        <?= count($alerts) ?>
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $currentType === 'consecutive' ? 'active' : '' ?>"
                                    href="<?= base_url('AttendanceAlert?type=consecutive') ?>">
                                    <i class="feather icon-arrow-right mr-1"></i>2 Hari Berturut
                                    <span class="badge badge-warning">
                                        <?= $stats['by_type']['consecutive'] ?? 0 ?>
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $currentType === 'weekly' ? 'active' : '' ?>"
                                    href="<?= base_url('AttendanceAlert?type=weekly') ?>">
                                    <i class="feather icon-calendar mr-1"></i>3x Seminggu
                                    <span class="badge badge-danger">
                                        <?= $stats['by_type']['weekly'] ?? 0 ?>
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $currentType === 'monthly' ? 'active' : '' ?>"
                                    href="<?= base_url('AttendanceAlert?type=monthly') ?>">
                                    <i class="feather icon-alert-triangle mr-1"></i>10+ Hari/Bulan
                                    <span class="badge badge-dark">
                                        <?= $stats['by_type']['monthly'] ?? 0 ?>
                                    </span>
                                </a>
                            </li>
                        </ul>

                        <!-- Alerts Table -->
                        <div class="table-responsive">
                            <table class="table table-hover" id="alertsTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Siswa</th>
                                        <th>Kelas</th>
                                        <th>Jenis</th>
                                        <th>Tidak Hadir</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($alerts)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                <i class="feather icon-check-circle" style="font-size: 48px;"></i>
                                                <p class="mt-2">Tidak ada peringatan aktif</p>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($alerts as $alert): ?>
                                            <tr>
                                                <td>
                                                    <?= date('d/m/Y', strtotime($alert['alert_date'])) ?>
                                                </td>
                                                <td>
                                                    <strong>
                                                        <?= esc($alert['nm_siswa']) ?>
                                                    </strong><br>
                                                    <small class="text-muted">
                                                        <?= esc($alert['no_induk']) ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <?= esc($alert['nm_rombel']) ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $typeLabels = [
                                                        'consecutive' => '<span class="badge badge-warning">2 Hari Berturut</span>',
                                                        'weekly' => '<span class="badge badge-danger">3x Seminggu</span>',
                                                        'monthly' => '<span class="badge badge-dark">10+ Hari/Bulan</span>',
                                                    ];
                                                    echo $typeLabels[$alert['alert_type']] ?? $alert['alert_type'];
                                                    ?>
                                                </td>
                                                <td>
                                                    <strong>
                                                        <?= $alert['absence_count'] ?> hari
                                                    </strong>
                                                    <?php if ($alert['absence_dates']): ?>
                                                        <br><small class="text-muted">
                                                            <?= $alert['absence_dates'] ?>
                                                        </small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $statusLabels = [
                                                        'new' => '<span class="badge badge-pill badge-danger">Baru</span>',
                                                        'notified_walikelas' => '<span class="badge badge-pill badge-warning">Dikirim ke Wali Kelas</span>',
                                                        'notified_bk' => '<span class="badge badge-pill badge-info">Dikirim ke BK</span>',
                                                        'resolved' => '<span class="badge badge-pill badge-success">Selesai</span>',
                                                    ];
                                                    echo $statusLabels[$alert['status']] ?? $alert['status'];
                                                    ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <?php if ($alert['status'] === 'new'): ?>
                                                            <button class="btn btn-warning" title="Kirim ke Wali Kelas"
                                                                onclick="notifyWalikelas(<?= $alert['id'] ?>)"
                                                                <?= empty($alert['wali_kelas_hp']) ? 'disabled' : '' ?>>
                                                                <i class="feather icon-send"></i>
                                                            </button>
                                                        <?php endif; ?>

                                                        <?php if (in_array($alert['status'], ['new', 'notified_walikelas'])): ?>
                                                            <button class="btn btn-info" title="Kirim ke Guru BK"
                                                                onclick="notifyBK(<?= $alert['id'] ?>)"
                                                                <?= empty($alert['guru_bk_hp']) ? 'disabled' : '' ?>>
                                                                <i class="feather icon-user-check"></i>
                                                            </button>
                                                        <?php endif; ?>

                                                        <button class="btn btn-success" title="Selesaikan"
                                                            onclick="resolveAlert(<?= $alert['id'] ?>)">
                                                            <i class="feather icon-check"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Resolve Modal -->
<div class="modal fade" id="resolveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="resolveForm" method="post">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="feather icon-check-circle mr-2"></i>Selesaikan Peringatan</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Catatan (opsional)</label>
                        <textarea name="notes" class="form-control" rows="3"
                            placeholder="Tambahkan catatan penyelesaian..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="feather icon-check mr-1"></i>Selesaikan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Pause Modal -->
<div class="modal fade" id="pauseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= base_url('AttendanceAlert/togglePause') ?>" method="post">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title text-white"><i class="feather icon-pause-circle mr-2"></i>Jeda Notifikasi
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="feather icon-info mr-1"></i>
                        Saat dijeda, sistem <strong>tidak akan</strong> mengirim scan kehadiran dan notifikasi WA.
                        Gunakan saat libur sekolah atau ada masalah database.
                    </div>
                    <div class="form-group">
                        <label>Alasan (opsional)</label>
                        <input type="text" name="reason" class="form-control"
                            placeholder="Contoh: Libur semester, Maintenance database...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="feather icon-pause mr-1"></i>Jeda Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .order-card {
        border-radius: 15px;
        position: relative;
        overflow: hidden;
    }

    .order-card .card-icon {
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 48px;
        opacity: 0.3;
    }

    .order-card h2 {
        font-size: 32px;
        font-weight: 700;
    }

    .bg-c-red {
        background: linear-gradient(45deg, #FF5370, #ff869a);
    }

    .bg-c-yellow {
        background: linear-gradient(45deg, #FFB64D, #ffcb80);
    }

    .bg-c-blue {
        background: linear-gradient(45deg, #4099ff, #73b4ff);
    }

    .bg-c-green {
        background: linear-gradient(45deg, #2ed8b6, #59e0c5);
    }
</style>

<script>
    $(document).ready(function () {
        $('#alertsTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            },
            order: [[0, 'desc']],
            pageLength: 25
        });
    });

    function scanAlerts() {
        if (!confirm('Scan peringatan kehadiran sekarang?')) return;

        $.get('<?= base_url("AttendanceAlert/scan") ?>', function (response) {
            if (response.success) {
                alert(response.message);
                location.reload();
            } else {
                alert('Gagal melakukan scan');
            }
        });
    }

    function notifyWalikelas(id) {
        if (!confirm('Kirim notifikasi ke wali kelas?')) return;

        $.get('<?= base_url("AttendanceAlert/notifyWalikelas") ?>/' + id, function (response) {
            if (response.success) {
                alert(response.message);
                location.reload();
            } else {
                alert(response.message || 'Gagal mengirim notifikasi');
            }
        });
    }

    function notifyBK(id) {
        if (!confirm('Kirim notifikasi ke guru BK?')) return;

        $.get('<?= base_url("AttendanceAlert/notifyBK") ?>/' + id, function (response) {
            if (response.success) {
                alert(response.message);
                location.reload();
            } else {
                alert(response.message || 'Gagal mengirim notifikasi');
            }
        });
    }

    function resolveAlert(id) {
        $('#resolveForm').attr('action', '<?= base_url("AttendanceAlert/resolve") ?>/' + id);
        $('#resolveModal').modal('show');
    }
</script>