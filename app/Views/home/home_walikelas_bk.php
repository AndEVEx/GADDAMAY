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
                                                <h5 class="m-b-10"><?=$title;?></h5>
                                            </div>
                                            <ul class="breadcrumb">
                                                <li class="breadcrumb-item"><a href="<?= base_url('Home'); ?>"><i class="feather icon-home"></i></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- [ breadcrumb ] end -->

                            <!-- Flash messages -->
                            <?php if(session()->get('success')) : ?>
                                <div class="alert alert-success alert-dismissible fade show">
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                    <strong><i class="feather icon-check-circle"></i></strong> <?= session()->getFlashdata('success'); ?>
                                </div>
                            <?php endif; ?>
                            <?php if(session()->get('error')) : ?>
                                <div class="alert alert-danger alert-dismissible fade show">
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                    <strong><i class="feather icon-alert-circle"></i></strong> <?= session()->getFlashdata('error'); ?>
                                </div>
                            <?php endif; ?>

                            <!-- [ Stats Cards ] start -->
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="card text-white bg-primary">
                                        <div class="card-body">
                                            <h2 class="text-white"><?= $totalSiswa ?></h2>
                                            <span>Total Siswa</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card text-white bg-success">
                                        <div class="card-body">
                                            <h2 class="text-white"><?= $hadirCount ?></h2>
                                            <span>Hadir Hari Ini</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card text-white bg-danger">
                                        <div class="card-body">
                                            <h2 class="text-white"><?= $tidakHadirCount ?></h2>
                                            <span>Tidak Hadir</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card text-white bg-warning">
                                        <div class="card-body">
                                            <h2 class="text-white"><?= $monitorCount ?></h2>
                                            <span>Dalam Monitoring</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- [ Stats Cards ] end -->

                            <!-- [ Daily Attendance ] start -->
                            <div class="row">
                                <div class="col-sm-12">
                                    <form method="post" action="<?= base_url('MuridMonitoring/bulkUpdateHadir') ?>">
                                    <div class="card">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h5><i class="feather icon-users"></i> Absensi Masuk <?= formatTanggal($tgl) ?></h5>
                                            <button type="button" class="btn btn-primary btn-sm btn-bulk-masuk" style="display:none;" data-toggle="modal" data-target="#modalBulkMasuk">
                                                <i class="feather icon-check-square"></i> Koreksi Masal (<span class="count-masuk">0</span>)
                                            </button>
                                        </div>
                                        <div class="card-body">
                                            
                                            <!-- Modal Koreksi Masal Masuk -->
                                            <div class="modal fade" id="modalBulkMasuk">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Koreksi Masal Absen Datang</h5>
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label>Ubah Status Menjadi:</label>
                                                                <select name="status" class="form-control" required>
                                                                    <option value="Masuk">Masuk</option>
                                                                    <option value="Terlambat">Terlambat</option>
                                                                    <option value="Sakit">Sakit</option>
                                                                    <option value="Izin">Izin</option>
                                                                    <option value="Alpha">Alpha</option>
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Jam Absen (Opsional, Default sekarang)</label>
                                                                <input type="time" name="jam" class="form-control" value="<?= date('H:i') ?>">
                                                            </div>
                                                            <button type="submit" class="btn btn-primary">Simpan Koreksi</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="table-responsive">
                                                <table class="table table-striped table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th><input type="checkbox" id="checkAllMasuk"></th>
                                                            <th>#</th>
                                                            <th>NIS</th>
                                                            <th>Nama Siswa</th>
                                                            <th>Kelas</th>
                                                            <th>No. Orangtua</th>
                                                            <th>Jam Masuk</th>
                                                            <th>Status</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $no = 0; foreach ($students as $s) { $no++; ?>
                                                        <tr>
                                                            <td><input type="checkbox" name="ids[]" value="<?= $s['id_siswa'] ?>" class="cb-masuk"></td>
                                                            <td><?= $no ?></td>
                                                            <td><?= $s['no_induk'] ?></td>
                                                            <td><?= $s['nm_siswa'] ?></td>
                                                            <td><?= $s['nm_rombel'] ?></td>
                                                            <td><?= $s['hp'] ?: '-' ?></td>
                                                            <td><?= jammasuk($s['id_siswa'], $tgl) ?></td>
                                                            <td>
                                                                <?php 
                                                                $sts = sts_absen($s['id_siswa'], $tgl);
                                                                $badge = 'secondary';
                                                                if($sts == 'Masuk') $badge = 'success';
                                                                elseif($sts == 'Terlambat') $badge = 'warning';
                                                                elseif($sts == 'Alpha') $badge = 'danger';
                                                                elseif($sts == 'Sakit' || $sts == 'Izin') $badge = 'info';
                                                                ?>
                                                                <span class="badge badge-<?= $badge ?>"><?= $sts ?></span>
                                                            </td>
                                                            <td>
                                                                <?php if (!empty($s['hp'])) { ?>
                                                                <a href="tel:<?= $s['hp'] ?>" class="btn btn-success btn-sm" title="Telepon Orangtua">
                                                                    <i class="feather icon-phone"></i>
                                                                </a>
                                                                <?php } ?>
                                                                <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editSiswa<?= $s['id_siswa'] ?>" title="Edit Data Siswa">
                                                                    <i class="feather icon-edit-2"></i>
                                                                </button>
                                                            </td>
                                                        </tr>

                                                        <!-- Edit Siswa Modal -->
                                                        <div class="modal fade" id="editSiswa<?= $s['id_siswa'] ?>">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Edit Data: <?= $s['nm_siswa'] ?></h5>
                                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                    </div>
                                                                    <form method="post" action="<?= base_url('MuridMonitoring/updateSiswa') ?>" enctype="multipart/form-data">
                                                                        <div class="modal-body">
                                                                            <input type="hidden" name="id_siswa" value="<?= $s['id_siswa'] ?>">
                                                                            <div class="form-group">
                                                                                <label>Nomor HP Orangtua</label>
                                                                                <input type="text" class="form-control" name="hp" value="<?= $s['hp'] ?>">
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label>Alamat</label>
                                                                                <input type="text" class="form-control" name="alamat" value="">
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label>Tempat Lahir</label>
                                                                                <input type="text" class="form-control" name="tempat_lahir" value="">
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label>Tanggal Lahir</label>
                                                                                <input type="date" class="form-control" name="tgl_lahir" value="">
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label>Foto (opsional)</label>
                                                                                <input type="file" name="file" class="form-control-file">
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    </form>
                                </div>
                            </div>
                            <!-- [ Daily Attendance ] end -->

                            <!-- [ Absen Pulang Table ] start -->
                            <div class="row">
                                <div class="col-sm-12">
                                    <form method="post" action="<?= base_url('MuridMonitoring/bulkUpdateHadir') ?>">
                                    <input type="hidden" name="status" value="Pulang">
                                    <div class="card">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h5><i class="feather icon-log-out"></i> Daftar Siswa Sudah Pulang</h5>
                                            <button type="button" class="btn btn-primary btn-sm btn-bulk-pulang" style="display:none;" data-toggle="modal" data-target="#modalBulkPulang">
                                                <i class="feather icon-check-square"></i> Koreksi Masal (<span class="count-pulang">0</span>)
                                            </button>
                                        </div>
                                        <div class="card-body">
                                            <!-- Modal Koreksi Masal Pulang -->
                                            <div class="modal fade" id="modalBulkPulang">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Koreksi Masal Absen Pulang</h5>
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label>Jam Pulang (Opsional, Default sekarang)</label>
                                                                <input type="time" name="jam" class="form-control" value="<?= date('H:i') ?>">
                                                            </div>
                                                            <button type="submit" class="btn btn-primary">Tandai Pulang</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="table-responsive">
                                                <table class="table table-striped table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th><input type="checkbox" id="checkAllPulang"></th>
                                                            <th>#</th>
                                                            <th>NIS</th>
                                                            <th>Nama Siswa</th>
                                                            <th>Kelas</th>
                                                            <th>Jam Pulang</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if(empty($pulangList)) { ?>
                                                        <tr><td colspan="6" class="text-center text-muted">Belum ada siswa yang pulang</td></tr>
                                                        <?php } else { $no = 0; foreach ($pulangList as $p) { $no++; ?>
                                                        <tr>
                                                            <td><input type="checkbox" name="ids[]" value="<?= $p['id_siswa'] ?>" class="cb-pulang"></td>
                                                            <td><?= $no ?></td>
                                                            <td><?= $p['no_induk'] ?></td>
                                                            <td><?= $p['nm_siswa'] ?></td>
                                                            <td><?= $p['nm_rombel'] ?></td>
                                                            <td><?= substr($p['jam_pulang'], 0, 5) ?></td>
                                                        </tr>
                                                        <?php } } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    </form>
                                </div>
                            </div>
                            <!-- [ Absen Pulang Table ] end -->

                            <!-- [ Attendance Chart ] start -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5><i class="feather icon-pie-chart"></i> Chart Kehadiran Hari Ini</h5>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="attendanceChart" height="200"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5><i class="feather icon-info"></i> Ringkasan</h5>
                                        </div>
                                        <div class="card-body">
                                            <table class="table">
                                                <tr><td>Total Siswa</td><td><strong><?= $totalSiswa ?></strong></td></tr>
                                                <tr><td>Hadir Tepat Waktu</td><td><span class="badge badge-success"><?= $hadirCount - ($terlambatCount ?? 0) ?></span></td></tr>
                                                <tr><td>Terlambat</td><td><span class="badge badge-warning"><?= $terlambatCount ?? 0 ?></span></td></tr>
                                                <tr><td>Tidak Hadir</td><td><span class="badge badge-danger"><?= $tidakHadirCount ?></span></td></tr>
                                                <tr><td>Sudah Pulang</td><td><span class="badge badge-info"><?= count($pulangList) ?></span></td></tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                            <script>
                            new Chart(document.getElementById('attendanceChart'), {
                                type: 'doughnut',
                                data: {
                                    labels: ['Hadir', 'Terlambat', 'Tidak Hadir'],
                                    datasets: [{
                                        data: [<?= $hadirCount - ($terlambatCount ?? 0) ?>, <?= $terlambatCount ?? 0 ?>, <?= $tidakHadirCount ?>],
                                        backgroundColor: ['#28a745', '#ffc107', '#dc3545']
                                    }]
                                },
                                options: { responsive: true }
                            });
                            </script>
                            <!-- [ Attendance Chart ] end -->

                            <!-- [ Monitoring Table ] start -->
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col">
                                                    <h5><i class="feather icon-alert-triangle"></i> Monitoring Murid</h5>
                                                </div>
                                                <div class="col-md-auto">
                                                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalTambah">
                                                        <i class="feather icon-plus"></i> Tambah Murid
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body table-border-style">
                                            <div class="table-responsive">
                                                <table class="table table-striped table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>NIS</th>
                                                            <th>Nama</th>
                                                            <th>Kelas</th>
                                                            <th>No. Orangtua</th>
                                                            <th>Wali Kelas</th>
                                                            <th>Guru BK</th>
                                                            <th>Progress</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if(empty($monitorList)) { ?>
                                                        <tr><td colspan="9" class="text-center text-muted">Tidak ada murid dalam monitoring</td></tr>
                                                        <?php } else { $no = 0; foreach ($monitorList as $m) { $no++; ?>
                                                        <tr>
                                                            <td><?= $no ?></td>
                                                            <td><?= $m['no_induk'] ?></td>
                                                            <td><?= $m['nm_siswa'] ?></td>
                                                            <td><?= $m['nm_rombel'] ?></td>
                                                            <td><?= $m['hp_siswa'] ?: '-' ?></td>
                                                            <td><?= $m['nm_walikelas'] ?: '-' ?></td>
                                                            <td><?= $m['nm_guru_bk'] ?: '-' ?></td>
                                                            <td>
                                                                <?php foreach ($m['progress'] as $p) { ?>
                                                                    <span class="badge badge-<?= $p['is_done'] ? 'success' : 'danger' ?>" 
                                                                          title="<?= $p['is_done'] ? 'Selesai' : 'Belum' ?>"
                                                                          style="font-size:14px; margin:1px; min-width:28px; display:inline-block;">
                                                                        <?= $p['step'] ?>
                                                                    </span>
                                                                <?php } ?>
                                                            </td>
                                                            <td>
                                                                <a href="<?= base_url('MuridMonitoring/action/' . $m['id_monitoring']) ?>" 
                                                                   class="btn btn-info btn-sm" title="Detail & Aksi">
                                                                    <i class="feather icon-eye"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        <?php } } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- [ Monitoring Table ] end -->

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Murid ke Monitoring -->
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="<?= base_url('MuridMonitoring/add') ?>">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Murid ke Monitoring</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Pilih Kelas</label>
                            <select class="form-control" name="id_rombel" id="selectRombel" required>
                                <option value="">-- Pilih Kelas --</option>
                                <?php foreach ($myRombels as $r) { ?>
                                <option value="<?= $r['id_rombel'] ?>"><?= $r['nm_rombel'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Pilih Siswa</label>
                            <select class="form-control" name="id_siswa" id="selectSiswa" required>
                                <option value="">-- Pilih Siswa --</option>
                                <?php foreach ($students as $s) { ?>
                                <option value="<?= $s['id_siswa'] ?>" data-rombel="<?= $s['id_rombel'] ?>"><?= $s['nm_siswa'] ?> (<?= $s['no_induk'] ?>)</option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Alasan / Pelanggaran</label>
                            <textarea class="form-control" name="alasan" rows="3" required placeholder="Jelaskan pelanggaran yang dilakukan..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Tambahkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    // Filter siswa by rombel
    document.getElementById('selectRombel').addEventListener('change', function() {
        var selectedRombel = this.value;
        var siswaOptions = document.querySelectorAll('#selectSiswa option');
        siswaOptions.forEach(function(opt) {
            if (opt.value === '') return;
            if (selectedRombel === '' || opt.getAttribute('data-rombel') === selectedRombel) {
                opt.style.display = '';
            } else {
                opt.style.display = 'none';
            }
        });
        document.getElementById('selectSiswa').value = '';
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Masuk Checkboxes
        const saMasuk = document.getElementById('checkAllMasuk');
        const cbMasuk = document.querySelectorAll('.cb-masuk');
        const btnMasuk = document.querySelector('.btn-bulk-masuk');
        const cntMasuk = document.querySelector('.count-masuk');
        
        if(saMasuk) {
            saMasuk.addEventListener('change', e => {
                cbMasuk.forEach(cb => cb.checked = e.target.checked);
                updateMasuk();
            });
            cbMasuk.forEach(cb => cb.addEventListener('change', updateMasuk));
        }
        function updateMasuk() {
            let checked = document.querySelectorAll('.cb-masuk:checked').length;
            cntMasuk.innerText = checked;
            btnMasuk.style.display = checked > 0 ? 'inline-block' : 'none';
        }

        // Pulang Checkboxes
        const saPulang = document.getElementById('checkAllPulang');
        const cbPulang = document.querySelectorAll('.cb-pulang');
        const btnPulang = document.querySelector('.btn-bulk-pulang');
        const cntPulang = document.querySelector('.count-pulang');

        if(saPulang) {
            saPulang.addEventListener('change', e => {
                cbPulang.forEach(cb => cb.checked = e.target.checked);
                updatePulang();
            });
            cbPulang.forEach(cb => cb.addEventListener('change', updatePulang));
        }
        function updatePulang() {
            let checked = document.querySelectorAll('.cb-pulang:checked').length;
            cntPulang.innerText = checked;
            btnPulang.style.display = checked > 0 ? 'inline-block' : 'none';
        }
    });
    </script>
