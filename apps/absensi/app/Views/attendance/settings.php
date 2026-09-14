<div class="pcoded-main-container">
    <div class="pcoded-content">
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h5 class="m-b-10">Pengaturan Peringatan Kehadiran</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= base_url('home') ?>"><i class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="<?= base_url('AttendanceAlert') ?>">Peringatan</a></li>
                            <li class="breadcrumb-item">Pengaturan</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="feather icon-check-circle mr-2"></i>
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Message Templates -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="feather icon-message-square mr-2"></i>Template Pesan Wali Kelas</h5>
                    </div>
                    <form method="post" action="<?= base_url('AttendanceAlert/saveSettings') ?>">
                        <div class="card-body">
                            <div class="alert alert-info">
                                <small>
                                    <strong>Variabel yang tersedia:</strong><br>
                                    <code>{nama_siswa}</code> - Nama siswa<br>
                                    <code>{nis}</code> - Nomor induk siswa<br>
                                    <code>{kelas}</code> - Nama kelas<br>
                                    <code>{alert_description}</code> - Deskripsi peringatan<br>
                                    <code>{tanggal}</code> - Tanggal hari ini
                                </small>
                            </div>
                            <div class="form-group">
                                <textarea name="alert_template_walikelas" class="form-control" rows="12"><?= esc($settings['alert_template_walikelas'] ?? '') ?></textarea>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="feather icon-save mr-1"></i>Simpan Template
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="feather icon-message-circle mr-2"></i>Template Pesan Guru BK</h5>
                    </div>
                    <form method="post" action="<?= base_url('AttendanceAlert/saveSettings') ?>">
                        <div class="card-body">
                            <div class="alert alert-warning">
                                <small>
                                    <strong>Variabel tambahan untuk Guru BK:</strong><br>
                                    <code>{wali_kelas}</code> - Nama wali kelas siswa
                                </small>
                            </div>
                            <div class="form-group">
                                <textarea name="alert_template_bk" class="form-control" rows="12"><?= esc($settings['alert_template_bk'] ?? '') ?></textarea>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="feather icon-save mr-1"></i>Simpan Template
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Guru BK Assignment -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="feather icon-users mr-2"></i>Penugasan Guru BK per Kelas</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-4">
                            <i class="feather icon-info mr-2"></i>
                            Tetapkan guru BK untuk setiap kelas. Notifikasi peringatan kehadiran akan dikirim ke guru BK yang ditugaskan.
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover" id="rombelTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Kelas</th>
                                        <th>Wali Kelas</th>
                                        <th>Guru BK</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rombels as $rombel): ?>
                                        <tr>
                                            <td><strong><?= esc($rombel['nm_rombel']) ?></strong></td>
                                            <td><?= esc($rombel['wali_kelas_nama'] ?? '-') ?></td>
                                            <td>
                                                <select class="form-control guru-bk-select" 
                                                        data-rombel="<?= $rombel['id_rombel'] ?>"
                                                        style="min-width: 200px;">
                                                    <option value="">-- Pilih Guru BK --</option>
                                                    <?php foreach ($teachers as $teacher): ?>
                                                        <option value="<?= $teacher['id_ptk'] ?>" 
                                                                <?= ($rombel['id_guru_bk'] ?? '') == $teacher['id_ptk'] ? 'selected' : '' ?>>
                                                            <?= esc($teacher['nama_ptk']) ?>
                                                            <?= $teacher['no_hp'] ? ' (' . $teacher['no_hp'] . ')' : '' ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-primary save-guru-bk" 
                                                        data-rombel="<?= $rombel['id_rombel'] ?>">
                                                    <i class="feather icon-save"></i>
                                                </button>
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

        <!-- Back Button -->
        <div class="row">
            <div class="col-12">
                <a href="<?= base_url('AttendanceAlert') ?>" class="btn btn-secondary">
                    <i class="feather icon-arrow-left mr-1"></i>Kembali ke Daftar Peringatan
                </a>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.save-guru-bk').click(function() {
        var btn = $(this);
        var idRombel = btn.data('rombel');
        var idGuruBK = btn.closest('tr').find('.guru-bk-select').val();
        
        btn.prop('disabled', true).html('<i class="feather icon-loader"></i>');
        
        $.post('<?= base_url("AttendanceAlert/saveGuruBK") ?>', {
            id_rombel: idRombel,
            id_guru_bk: idGuruBK
        }, function(response) {
            btn.prop('disabled', false).html('<i class="feather icon-save"></i>');
            
            if (response.success) {
                btn.removeClass('btn-primary').addClass('btn-success');
                setTimeout(function() {
                    btn.removeClass('btn-success').addClass('btn-primary');
                }, 2000);
            } else {
                alert('Gagal menyimpan: ' + response.message);
            }
        });
    });
});
</script>
