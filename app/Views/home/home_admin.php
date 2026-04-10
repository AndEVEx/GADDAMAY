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
                                        <div class="col-md-12 d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="page-header-title">
                                                    <h5 class="m-b-10"><?=$title;?></h5>
                                                </div>
                                                <ul class="breadcrumb">
                                                    <li class="breadcrumb-item"><a href="<?= base_url('Home'); ?>"><i class="feather icon-home"></i></a></li>
                                                </ul>
                                            </div>
                                            <div>
                                                <a href="<?= base_url('Absensisiswa/koreksi') ?>" class="btn btn-warning"><i class="feather icon-check-square"></i> Koreksi Masal Absensi</a>
                                                <button class="btn btn-danger" data-toggle="modal" data-target="#modalGlobalKoreksi"><i class="feather icon-alert-triangle"></i> Koreksi Masal Sesekolah (Force Majeure)</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- [ breadcrumb ] end -->
                            
                            <!-- Modal Global Koreksi -->
                            <div class="modal fade" id="modalGlobalKoreksi">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title text-danger"><i class="feather icon-alert-triangle"></i> Koreksi Masal Sesekolah</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <form action="<?= base_url('Absensisiswa/bulkUpdateGlobal') ?>" method="post">
                                            <div class="modal-body">
                                                <div class="alert alert-danger">
                                                    <strong>Peringatan!</strong> Aksi ini akan segera mengubah status absensi <strong>SELURUH SISWA AKTIF</strong> secara masal untuk tanggal yang ditentukan. Gunakan hanya pada kondisi <i>Force Majeure</i> (misal: Bencana, Mati Lampu Total, atau Libur Mendadak).
                                                </div>
                                                <div class="form-group">
                                                    <label>Tanggal</label>
                                                    <input type="date" name="tgl" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Ubah Status Menjadi:</label>
                                                    <select name="status" class="form-control" required>
                                                        <option value="Sakit">Sakit</option>
                                                        <option value="Izin">Izin</option>
                                                        <option value="Alpha">Alpha</option>
                                                        <option value="Masuk">Masuk</option>
                                                        <option value="Terlambat">Terlambat</option>
                                                        <option value="Pulang">Pulang</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Jam Rekam (Opsional, Default sekarang)</label>
                                                    <input type="time" name="jam" class="form-control" value="<?= date('H:i') ?>">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('APAKAH ANDA YAKIN? Aksi ini akan menimpa seluruh data absen sekolah pada tanggal yang dipilih!')">Proses Semua Siswa</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                                    <!-- [ Main Content ] start -->
                                    <div class="row">
                                        <div class="col-lg-7 col-md-12">
                                            <!-- support-section start -->
                                            <div class="row">
                                                <div class="col-sm-6">
                                                   
                                                    <div class="card support-bar overflow-hidden">
                                                        <div class="card-body pb-0">
                                                            <?php 
                                                            $idTapel = session()->get('id_tapel');
                                                            $db = \Config\Database::connect();
                                                            $builder_rbl = $db->table('t_rombel');
                                                            $builder_rbl->where('id_tapel', $idTapel);
                                                            $jml_rbl =  $builder_rbl->countAllResults();
                                                            ?>
                                                            
                                                            <h2 class="m-0"><a href="<?= base_url('Rombel'); ?>"><?= $jml_rbl ?></a></h2>
                                                            <span class="text-c-blue">Jumlah Rombel</span>
                                                            <p class="mb-3 mt-3"></p>
                                                        </div>
                                                        <div id="support-chart"></div>
                                                        <div class="card-footer bg-primary text-white">
                                                            <div class="row text-center">
                                                                <?php foreach ($getTingkat as $data) {   ?>
                                                                <div class="col">
                                                                    <?php 
                                                                     $builder_rbl7 = $db->table('t_rombel');
                                                                     $builder_rbl7->where('id_tapel', $idTapel);
                                                                     $builder_rbl7->where('id_tingkat_kelas', $data['id_tingkat_kelas']);
                                                                     $jml_rbl7 =  $builder_rbl7->countAllResults();
                                                                    ?>
                                                                    <h4 class="m-0 text-white"><?= $jml_rbl7 ?></h4>
                                                                    <span><?= $data['nm_tingkat_kelas'] ?></span>
                                                                </div>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                   
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="card support-bar overflow-hidden">
                                                        <div class="card-body pb-0">
                                                            <?php 
                                                            $builder_siswa = $db->table('t_siswa');
                                                            $builder_siswa->where('sts_siswa', 1);
                                                            $jml_siswa =  $builder_siswa->countAllResults();
                                                            ?>
                                                            <h2 class="m-0"><a href="<?= base_url('Siswa'); ?>"><?=$jml_siswa;?></a></h2>
                                                            <span class="text-c-blue">Jumlah Siswa</span>
                                                            <p class="mb-3 mt-3"></p>
                                                        </div>
                                                        <div id="support-chart"></div>
                                                        <div class="card-footer bg-primary text-white">
                                                            <div class="row text-center">
                                                                <div class="col">
                                                                    <?php 
                                                                    $builder_siswa1 = $db->table('t_siswa');
                                                                    $builder_siswa1->where('sts_siswa', 1);
                                                                    $builder_siswa1->where('jk', 1);
                                                                    $jml_siswa1 =  $builder_siswa1->countAllResults();
                                                                    ?>
                                                                    <h4 class="m-0 text-white"><?=$jml_siswa1;?></h4>
                                                                    <span>Laki-laki</span>
                                                                </div>
                                                                <div class="col">
                                                                    <?php 
                                                                   $builder_siswa2 = $db->table('t_siswa');
                                                                   $builder_siswa2->where('sts_siswa', 1);
                                                                   $builder_siswa2->where('jk', 2);
                                                                   $jml_siswa2 =  $builder_siswa2->countAllResults();
                                                                    ?>
                                                                    <h4 class="m-0 text-white"><?=$jml_siswa2;?></h4>
                                                                    <span>Perempuan</span>
                                                                </div>
                                                               
                                                            </div>
                                                        </div>
                                                    </div>

                                                   
                                                </div>

                                                
                                                
                                            </div>
                                            <!-- support-section end -->
                                        </div>
                                        <div class="col-lg-5 col-md-12">
                                            <!-- page statustic card start -->
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <div class="row align-items-center">
                                                                <div class="col-8">
                                                                    <?php 
                                                                    $tgl = date('Y-m-d');
                                                                    $builder_hdr = $db->table('t_siswa_hadir');
                                                                    $builder_hdr->where('tgl_hadir', $tgl);
                                                                    $builder_hdr->where('sts_hadir', 0);
                                                                    $all_hdr =  $builder_hdr->countAllResults();
                                                                    ?>
                                                                    <h4 class="text-c-green"><a href="<?= base_url('Absensisiswa/hadir'); ?>"><?=$all_hdr;?></a></h4>
                                                                    <h6 class="text-muted m-b-0">Hadir</h6>
                                                                </div>
                                                                <div class="col-4 text-right">
                                                                    <i class="feather icon-user-check f-28"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card-footer bg-c-green">
                                                            <div class="row align-items-center">
                                                                <div class="col-9">
                                                                    <?php 
                                                                    if($all_hdr==0 || $jml_siswa==0){
                                                                        $pres_hadir = 0;
                                                                    }else{
                                                                        $pres_hadir = ($all_hdr/($jml_siswa))*100;
                                                                    }
                                                                    
                                                                    ?>
                                                                    <p class="text-white m-b-0"><?=round($pres_hadir);?>%</p>
                                                                </div>
                                                                <div class="col-3 text-right">
                                                                    <i class="feather icon-trending-up text-white f-16"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <div class="row align-items-center">
                                                                <div class="col-8">
                                                                <?php 
                                                                    //cek jam masuk pada hari ini
                                                                    $nmhari = hari_ini();
                                                                    $query_jam = $db->query("SELECT sts_hari,jammasuk FROM r_hari where nm_hari='$nmhari'");
                                                                    $row_jam = $query_jam->getRow();
                                                                    $jammasuk = $row_jam->jammasuk;

                                                                    //ambil data absensi berdasarkan tgl sekarang
                                                                    $query_abs = $db->query("SELECT t_siswa_hadir.id_siswa as id_siswa, jam FROM t_siswa_hadir
                                                                    JOIN t_siswa ON t_siswa.id_siswa = t_siswa_hadir.id_siswa
                                                                    where tgl_hadir='$tgl' AND sts_hadir=0");
                                                                    $row_jml  = $query_abs->getResult();
                                                                    $jumlah_ter = count($row_jml);

                                                                    if($jumlah_ter>0){
                                                                        foreach ($query_abs->getResult() as $row_abs) {
                                                                            $id_siswa = $row_abs->id_siswa;
                                                                            $jam_abs = $row_abs->jam;
                                                                            if($jam_abs>$jammasuk){
                                                                                $terlambat[] = 1;
                                                                            }else{
                                                                                $terlambat[] = 0;
                                                                            }
                                                                        }
                                                                        $jml_terlambat = array_sum($terlambat);
                                                                    }else{
                                                                        $jml_terlambat = 0;
                                                                    }
                                                                    ?>
                                                                    <h4 class="text-c-yellow"><a href="<?= base_url('Absensisiswa/terlambat'); ?>"><?=$jml_terlambat;?></a></h4>
                                                                    <h6 class="text-muted m-b-0">Terlambat</h6>
                                                                </div>
                                                                <div class="col-4 text-right">
                                                                    <i class="feather icon-user-minus f-28"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card-footer bg-c-yellow">
                                                            <div class="row align-items-center">
                                                                <div class="col-9">
                                                                    <?php 
                                                                    if($jml_terlambat==0 || $jml_siswa==0){
                                                                        $pres_ter = 0;
                                                                    }else{
                                                                        $pres_ter = ($jml_terlambat/($jml_siswa))*100;
                                                                    }
                                                                    ?>
                                                                    <p class="text-white m-b-0"><?=round($pres_ter);?>%</p>
                                                                </div>
                                                                <div class="col-3 text-right">
                                                                    <i class="feather icon-trending-up text-white f-16"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <div class="row align-items-center">
                                                                <div class="col-8">
                                                                    <?php 
                                                                    use CodeIgniter\Database\BaseBuilder;
                                                                    $builder_alpha = $db->table('t_siswa');
                                                                    $builder_alpha->join('t_siswa_rombel','t_siswa_rombel.id_siswa = t_siswa.id_siswa');
                                                                    $builder_alpha->whereNotIn('t_siswa.id_siswa', static function (BaseBuilder $builder_alpha) {
                                                                        $tgl = date('Y-m-d');
                                                                        $builder_alpha->select('id_siswa')->from('t_siswa_hadir')->where('tgl_hadir', $tgl);
                                                                    });
                                                                    $all_alpha =  $builder_alpha->countAllResults();
                                                                    ?>
                                                                    <h4 class="text-c-red"><a href="<?= base_url('Absensisiswa/alpha'); ?>"><?=$all_alpha;?></a></h4>
                                                                    <h6 class="text-muted m-b-0">Tidak Hadir</h6>
                                                                </div>
                                                                <div class="col-4 text-right">
                                                                    <i class="feather icon-user-x f-28"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card-footer bg-c-red">
                                                            <div class="row align-items-center">
                                                                <div class="col-9">
                                                                    <?php 
                                                                    if($all_alpha==0 || $jml_siswa==0){
                                                                        $pres_alpha = 0;
                                                                    }else{
                                                                        $pres_alpha = ($all_alpha/($jml_siswa))*100;
                                                                    }
                                                                    ?>
                                                                    <p class="text-white m-b-0"><?=round($pres_alpha);?>%</p>
                                                                </div>
                                                                <div class="col-3 text-right">
                                                                    <i class="feather icon-trending-down text-white f-16"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <div class="row align-items-center">
                                                                <div class="col-8">
                                                                    <?php 
                                                                    $builder_pulang = $db->table('t_siswa_hadir');
                                                                    $builder_pulang->where('tgl_hadir', $tgl);
                                                                    $builder_pulang->where('sts_hadir', 1);
                                                                    $all_pulang =  $builder_pulang->countAllResults();
                                                                    ?>
                                                                    <h4 class="text-c-blue"><a href="<?= base_url('Absensisiswa/pulang'); ?>"><?=$all_pulang;?></a></h4>
                                                                    <h6 class="text-muted m-b-0">Pulang</h6>
                                                                </div>
                                                                <div class="col-4 text-right">
                                                                    <i class="feather icon-user f-28"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card-footer bg-c-blue">
                                                            <div class="row align-items-center">
                                                                <div class="col-9">
                                                                    <?php 
                                                                    if($all_alpha==0 || $jml_siswa==0){
                                                                        $pres_pulang = 0;
                                                                    }else{
                                                                        $pres_pulang = ($all_pulang/($jml_siswa))*100;
                                                                    }
                                                                    ?>
                                                                    <p class="text-white m-b-0"><?=round($pres_pulang);?>%</p>
                                                                </div>
                                                                <div class="col-3 text-right">
                                                                    <i class="feather icon-trending-down text-white f-16"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                            <!-- page statustic card end -->
                                        </div>
                                        
                                    </div>
                                    <!-- [ Main Content ] end -->
                                    
                                    <!-- [ Cards Per Kelas ] start -->
                                    <div class="row mt-4 mb-2">
                                        <div class="col-sm-12">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h5><i class="feather icon-grid"></i> Rekap Absen Kehadiran Per Kelas Hari Ini</h5>
                                            </div>
                                            <hr>
                                        </div>
                                        <?php 
                                            // Call the helper function to get all rombel
                                            $rombelList = getAllRombelForChart(); 
                                            foreach($rombelList as $rombel):
                                                $id_rombel = $rombel->id_rombel;
                                                $rekap = getRekapKelasHariIni($id_rombel);
                                        ?>
                                        <div class="col-md-4 col-xl-3 mb-4">
                                            <div class="card shadow-sm h-100" style="cursor:pointer; border-radius: 12px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'" onclick="showDetailKelasAjax(<?=$id_rombel?>, '<?=$rombel->nm_rombel?>')">
                                                <div class="card-body p-3">
                                                    <div class="text-center mb-3">
                                                        <h5 class="mb-1 font-weight-bold text-primary"><?=$rombel->nm_rombel?></h5>
                                                        <span class="badge badge-light border" style="font-size: 12px;">Total: <?= $rekap['total'] ?> Siswa</span>
                                                    </div>
                                                    <div class="row text-center mt-3">
                                                        <div class="col-6 border-right">
                                                            <h6 class="text-success mb-1" style="font-size: 11px; text-transform: uppercase; font-weight: bold;">Hadir</h6>
                                                            <h4 class="mb-0 font-weight-bold"><?=$rekap['hadir']?></h4>
                                                        </div>
                                                        <div class="col-6">
                                                            <h6 class="text-danger mb-1" style="font-size: 11px; text-transform: uppercase; font-weight: bold;">S/I/A</h6>
                                                            <h4 class="mb-0 font-weight-bold"><?= $rekap['sakit'] + $rekap['izin'] + $rekap['alfa'] ?></h4>
                                                            <small class="text-muted" style="font-size: 9px;">S:<?=$rekap['sakit']?> I:<?=$rekap['izin']?> A:<?=$rekap['alfa']?></small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <!-- [ Cards Per Kelas ] end -->

                                    <!-- [ Charts Per Jurusan ] start -->
                                    <div class="row mt-4 mb-2">
                                        <div class="col-sm-12">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h5><i class="feather icon-bar-chart-2"></i> Grafik Kehadiran Per Jurusan</h5>
                                            </div>
                                            <hr>
                                        </div>
                                        <div class="col-sm-12" id="chart-jurusan-container">
                                            <div class="row">
                                            <?php 
                                            // Extract jurusans for div containers
                                            $jurusanList = [];
                                            foreach($rombelList as $rombel) {
                                                $parts = explode(" ", trim($rombel->nm_rombel));
                                                // Pattern normally "X TKJ 1" -> parts[1] is TKJ
                                                $jur = isset($parts[1]) ? $parts[1] : 'Lainnya';
                                                if(count($parts) > 3) $jur = $parts[1]; // fallback
                                                if(!in_array($jur, $jurusanList)) $jurusanList[] = $jur;
                                            }
                                            foreach($jurusanList as $jur) {
                                                echo '<div class="col-md-6 mb-4">';
                                                echo '<div class="card shadow-sm"><div class="card-header bg-light pb-2 pt-3"><h5 class="mb-0 text-primary">Jurusan '.htmlspecialchars($jur).'</h5></div>';
                                                echo '<div class="card-body"><div id="chart-jurusan-'.htmlspecialchars($jur).'"></div></div></div></div>';
                                            }
                                            ?>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- [ Charts Per Jurusan ] end -->

                                    <!-- Modal Detail Kelas -->
                                    <div class="modal fade" id="modalDetailKelas" tabindex="-1">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content" style="border-radius: 15px;">
                                                <div class="modal-header bg-primary text-white" style="border-radius: 15px 15px 0 0;">
                                                    <h5 class="modal-title"><i class="feather icon-list mr-2"></i> Detail Absen Kelas: <span id="mdlKelasNama"></span></h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                                </div>
                                                <div class="modal-body p-0">
                                                    <div class="text-center p-4" id="mdlKelasLoading">
                                                        <div class="spinner-border text-primary" role="status">
                                                            <span class="sr-only">Loading...</span>
                                                        </div>
                                                        <p class="mt-2 text-muted">Memuat data absensi...</p>
                                                    </div>
                                                    <div class="table-responsive" id="mdlKelasTableContainer" style="display: none; max-height: 400px;">
                                                        <table class="table table-hover table-striped mb-0">
                                                            <thead class="thead-light">
                                                                <tr>
                                                                    <th class="sticky-top bg-light">No</th>
                                                                    <th class="sticky-top bg-light">Nama Siswa</th>
                                                                    <th class="sticky-top bg-light">Masuk</th>
                                                                    <th class="sticky-top bg-light">Pulang</th>
                                                                    <th class="sticky-top bg-light">Keterangan</th>
                                                                    <th class="sticky-top bg-light text-center">Aksi</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="mdlKelasBody">
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <script>
                                    function showDetailKelasAjax(idRombel, nmRombel) {
                                        $('#mdlKelasNama').text(nmRombel);
                                        $('#mdlKelasLoading').show();
                                        $('#mdlKelasTableContainer').hide();
                                        $('#modalDetailKelas').modal('show');

                                        // Fetch data
                                        $.ajax({
                                            url: '<?= base_url('Dashboard/getDetailKelasAjax') ?>/' + idRombel,
                                            method: 'GET',
                                            dataType: 'json',
                                            success: function(res) {
                                                $('#mdlKelasLoading').hide();
                                                let html = '';
                                                if(res.length === 0) {
                                                    html = '<tr><td colspan="6" class="text-center text-muted">Tidak ada data siswa untuk kelas ini.</td></tr>';
                                                } else {
                                                    res.forEach((item, index) => {
                                                        let badgeClass = 'secondary';
                                                        let sts = item.status;
                                                        if(sts === 'Hadir' || sts === 'Pulang') badgeClass = 'success';
                                                        else if(sts === 'Terlambat') badgeClass = 'warning';
                                                        else if(sts === 'Sakit' || sts === 'Izin') badgeClass = 'info';
                                                        else if(sts === 'Alpha') badgeClass = 'danger';

                                                        html += `<tr>
                                                            <td>${index + 1}</td>
                                                            <td><span class="font-weight-bold">${item.nm_siswa}</span></td>
                                                            <td><span class="badge ${item.jam_masuk ? 'badge-light' : ''} border">${item.jam_masuk || '-'}</span></td>
                                                            <td><span class="badge ${item.jam_pulang ? 'badge-light' : ''} border">${item.jam_pulang || '-'}</span></td>
                                                            <td><span class="badge badge-${badgeClass}">${sts}</span></td>
                                                            <td class="text-center">
                                                                <a href="<?= base_url('Absensisiswa/detail') ?>/${item.id_siswa}" target="_blank" class="btn btn-sm btn-outline-primary" style="margin:0; padding:2px 8px; border-radius:15px;" title="Lihat Riwayat Absen">
                                                                    <i class="feather icon-calendar m-0"></i> Riwayat
                                                                </a>
                                                            </td>
                                                        </tr>`;
                                                    });
                                                }
                                                $('#mdlKelasBody').html(html);
                                                $('#mdlKelasTableContainer').show();
                                            },
                                            error: function() {
                                                $('#mdlKelasLoading').html('<p class="text-danger mt-2">Gagal memuat data. Silahkan coba lagi.</p>');
                                            }
                                        });
                                    }
                                    </script>

                            <!-- [ Murid on Watch ] start -->
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col">
                                                    <h5><i class="feather icon-alert-triangle"></i> Murid on Watch - Monitoring Aktif</h5>
                                                </div>
                                                <div class="col-md-auto">
                                                    <span class="badge badge-warning" style="font-size:14px;"><?= isset($monitorList) ? count($monitorList) : 0 ?> siswa</span>
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
                                                            <th>Alasan</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if(!isset($monitorList) || empty($monitorList)) { ?>
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
                                                                          style="font-size:13px; margin:1px; min-width:26px; display:inline-block;">
                                                                        <?= $p['step'] ?>
                                                                    </span>
                                                                <?php } ?>
                                                            </td>
                                                            <td><small><?= substr($m['alasan'], 0, 60) ?><?= strlen($m['alasan']) > 60 ? '...' : '' ?></small></td>
                                                        </tr>
                                                        <?php } } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- [ Murid on Watch ] end -->

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>