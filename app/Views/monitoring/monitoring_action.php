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
                                                <li class="breadcrumb-item"><a href="<?= base_url('MuridMonitoring'); ?>">Monitoring</a></li>
                                                <li class="breadcrumb-item"><a href="#!">Detail</a></li>
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

                            <!-- [ Student Info ] start -->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <?php 
                                            $foto = (!empty($monitoring['foto_siswa'])) 
                                                ? base_url('image/siswa/' . $monitoring['foto_siswa']) 
                                                : base_url('image/siswa/noimage.png');
                                            ?>
                                            <img src="<?= $foto ?>" class="rounded-circle mb-3" width="100" height="100" style="object-fit:cover;">
                                            <h5><?= $monitoring['nm_siswa'] ?></h5>
                                            <p class="text-muted mb-1">NIS: <?= $monitoring['no_induk'] ?></p>
                                            <p class="text-muted mb-1">Kelas: <?= $monitoring['nm_rombel'] ?></p>
                                            <p class="text-muted mb-1">No. HP: <?= $monitoring['hp_siswa'] ?: '-' ?></p>
                                            <hr>
                                            <p class="mb-1"><strong>Wali Kelas:</strong> <?= $monitoring['nm_walikelas'] ?: '-' ?></p>
                                            <p class="mb-1"><strong>Guru BK:</strong> <?= $monitoring['nm_guru_bk'] ?: '-' ?></p>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-body">
                                            <h6>Alasan Monitoring</h6>
                                            <p><?= $monitoring['alasan'] ?></p>
                                            <small class="text-muted">Ditambahkan: <?= date('d M Y H:i', strtotime($monitoring['created_at'])) ?></small>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-8">
                                    <!-- [ Progress Steps ] -->
                                    <div class="card">
                                        <div class="card-header">
                                            <h5><i class="feather icon-list"></i> Progress Monitoring</h5>
                                        </div>
                                        <div class="card-body">
                                            <?php foreach ($progress as $p) { 
                                                $stepNum = $p['step'];
                                                $isDone = $p['is_done'];
                                                $label = $stepLabels[$stepNum] ?? 'Step ' . $stepNum;
                                            ?>
                                            <div class="card mb-3 border-<?= $isDone ? 'success' : 'danger' ?>">
                                                <div class="card-header bg-<?= $isDone ? 'success' : 'danger' ?> text-white d-flex justify-content-between align-items-center">
                                                    <span>
                                                        <strong>Langkah <?= $stepNum ?>:</strong> <?= $label ?>
                                                    </span>
                                                    <span class="badge badge-light">
                                                        <?= $isDone ? '✓ Selesai' : '✗ Belum' ?>
                                                    </span>
                                                </div>
                                                <div class="card-body">
                                                    <?php if ($isDone) { ?>
                                                        <!-- Show proof -->
                                                        <?php if (!empty($p['catatan'])) { ?>
                                                            <p><strong>Catatan:</strong> <?= $p['catatan'] ?></p>
                                                        <?php } ?>
                                                        <?php if (!empty($p['file_bukti'])) { ?>
                                                            <p><strong>Bukti:</strong></p>
                                                            <?php if ($p['file_type'] == 'image') { ?>
                                                                <img src="<?= base_url('uploads/monitoring/' . $p['file_bukti']) ?>" 
                                                                     class="img-fluid rounded" style="max-height:300px;">
                                                            <?php } else { ?>
                                                                <a href="<?= base_url('uploads/monitoring/' . $p['file_bukti']) ?>" 
                                                                   target="_blank" class="btn btn-outline-danger btn-sm">
                                                                    <i class="feather icon-file-text"></i> Lihat PDF
                                                                </a>
                                                            <?php } ?>
                                                        <?php } ?>
                                                        <?php if (!empty($p['done_at'])) { ?>
                                                            <p class="text-muted mt-2 mb-0">
                                                                <small>Diselesaikan: <?= date('d M Y H:i', strtotime($p['done_at'])) ?></small>
                                                            </p>
                                                        <?php } ?>
                                                    <?php } else { ?>
                                                        <!-- Upload form - only show if this is the current step to complete -->
                                                        <?php 
                                                        // Only allow completing the next uncompleted step in sequence
                                                        $canComplete = true;
                                                        foreach ($progress as $prevP) {
                                                            if ($prevP['step'] < $stepNum && !$prevP['is_done']) {
                                                                $canComplete = false;
                                                                break;
                                                            }
                                                        }
                                                        ?>
                                                        <?php if ($canComplete) { ?>
                                                        <form method="post" action="<?= base_url('MuridMonitoring/completeStep') ?>" 
                                                              enctype="multipart/form-data">
                                                            <input type="hidden" name="id_monitoring" value="<?= $monitoring['id_monitoring'] ?>">
                                                            <input type="hidden" name="step" value="<?= $stepNum ?>">
                                                            <div class="form-group">
                                                                <label>Upload Bukti (Foto/PDF) <span class="text-danger">*</span></label>
                                                                <input type="file" class="form-control" name="file_bukti" 
                                                                       accept=".jpg,.jpeg,.png,.pdf" required>
                                                                <small class="text-muted">Format: JPG, PNG, PDF. Maks: 5MB</small>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Catatan</label>
                                                                <textarea class="form-control" name="catatan" rows="2" 
                                                                          placeholder="Catatan tambahan..."></textarea>
                                                            </div>
                                                            <button type="submit" class="btn btn-success btn-sm">
                                                                <i class="feather icon-check"></i> Selesaikan Langkah <?= $stepNum ?>
                                                            </button>
                                                        </form>
                                                        <?php } else { ?>
                                                        <p class="text-muted mb-0">Selesaikan langkah sebelumnya terlebih dahulu.</p>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <?php } ?>
                                        </div>
                                    </div>

                                    <!-- [ Action Buttons ] -->
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col">
                                                    <?php
                                                    // Check if all 4 steps are done
                                                    $allDone = true;
                                                    foreach ($progress as $p) {
                                                        if (!$p['is_done']) { $allDone = false; break; }
                                                    }
                                                    ?>
                                                    <?php if ($allDone) { ?>
                                                    <a href="<?= base_url('MuridMonitoring/resolve/' . $monitoring['id_monitoring']) ?>"
                                                       class="btn btn-success"
                                                       onclick="return confirm('Tandai kasus ini selesai?')">
                                                        <i class="feather icon-check-circle"></i> Tandai Selesai
                                                    </a>
                                                    <?php } ?>
                                                </div>
                                                <div class="col-md-auto">
                                                    <?php
                                                    // Check if at least the current step is done - allow reopen for repeat offenders
                                                    $currentStepDone = false;
                                                    foreach ($progress as $p) {
                                                        if ($p['step'] == $monitoring['current_step'] && $p['is_done']) {
                                                            $currentStepDone = true;
                                                        }
                                                    }
                                                    ?>
                                                    <?php if ($currentStepDone && $monitoring['current_step'] < 4) { ?>
                                                    <button class="btn btn-warning" data-toggle="modal" data-target="#modalReopen">
                                                        <i class="feather icon-repeat"></i> Tambah Kembali (Pelanggaran Berulang)
                                                    </button>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- [ Student Info ] end -->

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Reopen -->
    <div class="modal fade" id="modalReopen" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="<?= base_url('MuridMonitoring/reopen/' . $monitoring['id_monitoring']) ?>">
                    <div class="modal-header">
                        <h5 class="modal-title">Pelanggaran Berulang</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <strong>Perhatian!</strong> Kasus sebelumnya akan ditandai selesai dan siswa akan ditambahkan kembali ke monitoring di langkah <?= min($monitoring['current_step'] + 1, 4) ?>.
                        </div>
                        <div class="form-group">
                            <label>Alasan Pelanggaran Baru</label>
                            <textarea class="form-control" name="alasan" rows="3" placeholder="Jelaskan pelanggaran baru..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">Tambahkan Kembali</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
