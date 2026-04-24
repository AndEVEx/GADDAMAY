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

                        <!-- Filter Cards -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card shadow-sm" style="border-radius: 15px; border: none;">
                                    <div class="card-body py-3">
                                        <div class="row align-items-center">
                                            <div class="col-md-4">
                                                <h5 class="mb-0"><i
                                                        class="feather icon-filter mr-2 text-primary"></i>Filter Pesan
                                                </h5>
                                            </div>
                                            <div class="col-md-8">
                                                <form class="form-inline justify-content-end" method="GET">
                                                    <div class="form-group mr-3">
                                                        <label class="mr-2">Status:</label>
                                                        <select name="status" class="form-control"
                                                            style="border-radius: 10px;" onchange="this.form.submit()">
                                                            <option value="all" <?= $currentStatus == 'all' ? 'selected' : '' ?>>Semua</option>
                                                            <option value="pending" <?= $currentStatus == 'pending' ? 'selected' : '' ?>>Menunggu</option>
                                                            <option value="processing" <?= $currentStatus == 'processing' ? 'selected' : '' ?>>Diproses</option>
                                                            <option value="sent" <?= $currentStatus == 'sent' ? 'selected' : '' ?>>Terkirim</option>
                                                            <option value="failed" <?= $currentStatus == 'failed' ? 'selected' : '' ?>>Gagal</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group mr-3">
                                                        <label class="mr-2">Tanggal:</label>
                                                        <input type="date" name="date" class="form-control"
                                                            style="border-radius: 10px;" value="<?= $currentDate ?>"
                                                            onchange="this.form.submit()">
                                                    </div>
                                                    <a href="<?= base_url('WaNotification/queue') ?>"
                                                        class="btn btn-outline-secondary" style="border-radius: 10px;">
                                                        <i class="feather icon-x"></i> Reset
                                                    </a>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Stats Summary -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card text-center border-0"
                                    style="border-radius: 12px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                    <div class="card-body text-white py-3">
                                        <h4 class="mb-0">
                                            <?= $stats['pending'] ?>
                                        </h4>
                                        <small>Menunggu</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center border-0"
                                    style="border-radius: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    <div class="card-body text-white py-3">
                                        <h4 class="mb-0">
                                            <?= $stats['processing'] ?>
                                        </h4>
                                        <small>Diproses</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center border-0"
                                    style="border-radius: 12px; background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                                    <div class="card-body text-white py-3">
                                        <h4 class="mb-0">
                                            <?= $stats['sent'] ?>
                                        </h4>
                                        <small>Terkirim</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center border-0"
                                    style="border-radius: 12px; background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);">
                                    <div class="card-body text-white py-3">
                                        <h4 class="mb-0">
                                            <?= $stats['failed'] ?>
                                        </h4>
                                        <small>Gagal</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Messages Table -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card shadow-sm" style="border-radius: 15px; border: none;">
                                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center"
                                        style="border-radius: 15px 15px 0 0;">
                                        <h5 class="mb-0"><i
                                                class="feather icon-message-square mr-2 text-primary"></i>Daftar Pesan (
                                            <?= count($messages) ?>)
                                        </h5>
                                        <div>
                                            <a href="<?= base_url('WaNotification/deleteAllQueue') ?>" class="btn btn-outline-danger mr-2"
                                                style="border-radius: 10px;" onclick="return confirm('APAKAH ANDA YAKIN? Tindakan ini akan menghapus semua pesan dari antrean secara permanen!')">
                                                <i class="feather icon-trash-2 mr-1"></i> Hapus Semua
                                            </a>
                                            <a href="<?= base_url('WaNotification') ?>" class="btn btn-outline-primary"
                                                style="border-radius: 10px;">
                                                <i class="feather icon-arrow-left mr-1"></i> Kembali
                                            </a>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <?php if (empty($messages)): ?>
                                            <div class="text-center py-5">
                                                <div
                                                    style="width: 100px; height: 100px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="feather icon-inbox text-white" style="font-size: 40px;"></i>
                                                </div>
                                                <h5 class="text-muted">Tidak ada pesan ditemukan</h5>
                                                <p class="text-muted">Coba ubah filter atau generate laporan baru</p>
                                                <a href="<?= base_url('WeeklyReport/generate') ?>" class="btn btn-primary"
                                                    style="border-radius: 10px;">
                                                    <i class="feather icon-plus mr-1"></i> Generate Laporan
                                                </a>
                                            </div>
                                        <?php else: ?>
                                            <div class="table-responsive">
                                                <table class="table table-hover" id="messageTable">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Siswa</th>
                                                            <th>No HP</th>
                                                            <th>Jadwal</th>
                                                            <th>Periode</th>
                                                            <th>Status</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $no = 0;
                                                        foreach ($messages as $msg):
                                                            $no++; ?>
                                                            <tr>
                                                                <td>
                                                                    <?= $no ?>
                                                                </td>
                                                                <td>
                                                                    <strong>
                                                                        <?= $msg['nm_siswa'] ?? 'N/A' ?>
                                                                    </strong>
                                                                    <small class="d-block text-muted">
                                                                        <?= $msg['no_induk'] ?? '' ?>
                                                                    </small>
                                                                </td>
                                                                <td>
                                                                    <code><?= $msg['phone_number'] ?></code>
                                                                </td>
                                                                <td>
                                                                    <?= date('d M Y', strtotime($msg['scheduled_date'])) ?>
                                                                </td>
                                                                <td>
                                                                    <small>
                                                                        <?= date('d/m', strtotime($msg['week_start'])) ?> -
                                                                        <?= date('d/m', strtotime($msg['week_end'])) ?>
                                                                    </small>
                                                                </td>
                                                                <td>
                                                                    <?php
                                                                    $statusClass = [
                                                                        'pending' => 'warning',
                                                                        'processing' => 'info',
                                                                        'sent' => 'success',
                                                                        'failed' => 'danger'
                                                                    ];
                                                                    $statusIcon = [
                                                                        'pending' => 'clock',
                                                                        'processing' => 'loader',
                                                                        'sent' => 'check-circle',
                                                                        'failed' => 'x-circle'
                                                                    ];
                                                                    ?>
                                                                    <span
                                                                        class="badge badge-<?= $statusClass[$msg['status']] ?>"
                                                                        style="padding: 8px 12px; border-radius: 20px;">
                                                                        <i
                                                                            class="feather icon-<?= $statusIcon[$msg['status']] ?> mr-1"></i>
                                                                        <?= ucfirst($msg['status']) ?>
                                                                    </span>
                                                                    <?php if ($msg['status'] == 'sent' && $msg['sent_at']): ?>
                                                                        <small class="d-block text-muted mt-1">
                                                                            <?= date('H:i', strtotime($msg['sent_at'])) ?>
                                                                        </small>
                                                                    <?php endif; ?>
                                                                    <?php if ($msg['status'] == 'failed'): ?>
                                                                        <small class="d-block text-danger mt-1"
                                                                            title="<?= $msg['error_message'] ?>">
                                                                            <?= substr($msg['error_message'] ?? '', 0, 20) ?>...
                                                                        </small>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td>
                                                                    <div class="btn-group">
                                                                        <button type="button" class="btn btn-sm btn-info"
                                                                            data-toggle="modal"
                                                                            data-target="#msgModal<?= $msg['id'] ?>"
                                                                            title="Lihat Pesan">
                                                                            <i class="feather icon-eye"></i>
                                                                        </button>
                                                                        <?php if ($msg['id_siswa']): ?>
                                                                            <a href="<?= base_url('WaNotification/preview/' . $msg['id_siswa']) ?>"
                                                                                class="btn btn-sm btn-primary" title="Preview">
                                                                                <i class="feather icon-external-link"></i>
                                                                            </a>
                                                                        <?php endif; ?>
                                                                        <a href="<?= base_url('WaNotification/deleteMessage/' . $msg['id']) ?>"
                                                                            class="btn btn-sm btn-danger"
                                                                            onclick="return confirm('Hapus pesan ini?')"
                                                                            title="Hapus">
                                                                            <i class="feather icon-trash-2"></i>
                                                                        </a>
                                                                    </div>

                                                                    <!-- Modal -->
                                                                    <div class="modal fade" id="msgModal<?= $msg['id'] ?>"
                                                                        tabindex="-1">
                                                                        <div class="modal-dialog modal-dialog-centered">
                                                                            <div class="modal-content"
                                                                                style="border-radius: 15px;">
                                                                                <div class="modal-header bg-primary text-white"
                                                                                    style="border-radius: 15px 15px 0 0;">
                                                                                    <h5 class="modal-title"><i
                                                                                            class="feather icon-message-circle mr-2"></i>Detail
                                                                                        Pesan</h5>
                                                                                    <button type="button"
                                                                                        class="close text-white"
                                                                                        data-dismiss="modal">&times;</button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <div class="wa-message-preview"
                                                                                        style="background: #e5ddd5; padding: 20px; border-radius: 10px;">
                                                                                        <div
                                                                                            style="background: white; padding: 15px; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                                                                                            <pre
                                                                                                style="white-space: pre-wrap; margin: 0; font-family: inherit;"><?= htmlspecialchars($msg['message']) ?></pre>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
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

    .btn {
        border-radius: 8px !important;
        transition: all 0.2s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
    }

    .table td {
        vertical-align: middle;
    }

    .badge {
        font-weight: 500;
    }
</style>