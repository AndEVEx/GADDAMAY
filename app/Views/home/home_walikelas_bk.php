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
                                    <div class="card">
                                        <div class="card-header">
                                            <h5><i class="feather icon-calendar"></i> Absensi Harian - <?= tgl_indo($tgl) ?></h5>
                                        </div>
                                        <div class="card-body table-border-style">
                                            <div class="table-responsive">
                                                <table class="table table-striped table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>NIS</th>
                                                            <th>Nama Siswa</th>
                                                            <th>Kelas</th>
                                                            <th>Jam Masuk</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $no = 0; foreach ($students as $s) { $no++; ?>
                                                        <tr>
                                                            <td><?= $no ?></td>
                                                            <td><?= $s['no_induk'] ?></td>
                                                            <td><?= $s['nm_siswa'] ?></td>
                                                            <td><?= $s['nm_rombel'] ?></td>
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
                                                        </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- [ Daily Attendance ] end -->

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
    </script>
