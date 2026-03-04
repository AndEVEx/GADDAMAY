<div class="pcoded-main-container">
    <div class="pcoded-content">
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h5 class="m-b-10">Laporan Peringatan Kehadiran</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= base_url('home') ?>"><i
                                        class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="<?= base_url('AttendanceAlert') ?>">Peringatan</a></li>
                            <li class="breadcrumb-item">Laporan</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="feather icon-file-text mr-2"></i>Filter Laporan</h5>
                    </div>
                    <div class="card-body">
                        <form method="get" class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Tanggal Mulai</label>
                                    <input type="date" name="start_date" class="form-control"
                                        value="<?= $filters['start_date'] ?? date('Y-m-01') ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Tanggal Akhir</label>
                                    <input type="date" name="end_date" class="form-control"
                                        value="<?= $filters['end_date'] ?? date('Y-m-d') ?>">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Jenis</label>
                                    <select name="type" class="form-control">
                                        <option value="">Semua</option>
                                        <option value="consecutive" <?= ($filters['alert_type'] ?? '') === 'consecutive' ? 'selected' : '' ?>>2 Hari Berturut</option>
                                        <option value="weekly" <?= ($filters['alert_type'] ?? '') === 'weekly' ? 'selected' : '' ?>>3x Seminggu</option>
                                        <option value="monthly" <?= ($filters['alert_type'] ?? '') === 'monthly' ? 'selected' : '' ?>>10+ Hari/Bulan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Kelas</label>
                                    <select name="rombel" class="form-control">
                                        <option value="">Semua</option>
                                        <?php foreach ($rombels as $rombel): ?>
                                            <option value="<?= $rombel['id_rombel'] ?>" <?= ($filters['id_rombel'] ?? '') == $rombel['id_rombel'] ? 'selected' : '' ?>>
                                                <?= esc($rombel['nm_rombel']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <div class="form-group w-100">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="feather icon-search mr-1"></i>Filter
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5><i class="feather icon-list mr-2"></i>Hasil Laporan (
                            <?= count($alerts) ?> data)
                        </h5>
                        <button class="btn btn-success btn-sm" onclick="exportExcel()">
                            <i class="feather icon-download mr-1"></i>Export Excel
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="reportTable">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>NIS</th>
                                        <th>Nama Siswa</th>
                                        <th>Kelas</th>
                                        <th>Jenis Peringatan</th>
                                        <th>Tidak Hadir</th>
                                        <th>Status</th>
                                        <th>Notif Wali Kelas</th>
                                        <th>Notif Guru BK</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1;
                                    foreach ($alerts as $alert): ?>
                                        <tr>
                                            <td>
                                                <?= $no++ ?>
                                            </td>
                                            <td>
                                                <?= date('d/m/Y', strtotime($alert['alert_date'])) ?>
                                            </td>
                                            <td>
                                                <?= esc($alert['no_induk']) ?>
                                            </td>
                                            <td>
                                                <?= esc($alert['nm_siswa']) ?>
                                            </td>
                                            <td>
                                                <?= esc($alert['nm_rombel']) ?>
                                            </td>
                                            <td>
                                                <?php
                                                $types = [
                                                    'consecutive' => '2 Hari Berturut',
                                                    'weekly' => '3x Seminggu',
                                                    'monthly' => '10+ Hari/Bulan',
                                                ];
                                                echo $types[$alert['alert_type']] ?? $alert['alert_type'];
                                                ?>
                                            </td>
                                            <td>
                                                <?= $alert['absence_count'] ?> hari
                                            </td>
                                            <td>
                                                <?php
                                                $statuses = [
                                                    'new' => 'Baru',
                                                    'notified_walikelas' => 'Dikirim ke Wali Kelas',
                                                    'notified_bk' => 'Dikirim ke BK',
                                                    'resolved' => 'Selesai',
                                                ];
                                                echo $statuses[$alert['status']] ?? $alert['status'];
                                                ?>
                                            </td>
                                            <td>
                                                <?= $alert['notified_walikelas_at'] ? date('d/m/Y H:i', strtotime($alert['notified_walikelas_at'])) : '-' ?>
                                            </td>
                                            <td>
                                                <?= $alert['notified_bk_at'] ? date('d/m/Y H:i', strtotime($alert['notified_bk_at'])) : '-' ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#reportTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            },
            dom: 'Bfrtip',
            buttons: ['excel', 'pdf', 'print'],
            pageLength: 50
        });
    });

    function exportExcel() {
        // Trigger DataTables Excel export
        $('#reportTable').DataTable().button('.buttons-excel').trigger();
    }
</script>