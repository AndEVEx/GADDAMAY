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
 
                                    <!-- [ Main Content ] start -->
                                    <div class="row">
                                        <div class="col-lg-7 col-md-12">
                                            <!-- support-section start -->
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="card support-bar overflow-hidden">
                                                        <div class="card-body pb-0">
                                                            <?php 
                                                            $db = \Config\Database::connect();
                                                            $builder_kar = $db->table('t_ptk');
                                                           
                                                            $builder_kar->where('status_ptk', 1);
                                                       
                                                            $all_kar =  $builder_kar->countAllResults();
                                                            ?>
                                                            <h2 class="m-0"><a href="<?= base_url('Pegawai'); ?>"><?=$all_kar;?></a></h2>
                                                            <span class="text-c-blue">Jumlah Guru</span>
                                                            <p class="mb-3 mt-3"></p>
                                                        </div>
                                                        <div id="support-chart"></div>
                                                        <div class="card-footer bg-primary text-white">
                                                            <div class="row text-center">
                                                                <div class="col">
                                                                    <?php 
                                                                    $builder_kar_lk = $db->table('t_ptk');
                                                                   
                                                                    $builder_kar_lk->where('status_ptk', 1);
                                                                    $builder_kar_lk->where('kd_jenis_kelamin', 1);
                                                                    $all_kar_lk =  $builder_kar_lk->countAllResults();
                                                                    ?>
                                                                    <h4 class="m-0 text-white"><?=$all_kar_lk;?></h4>
                                                                    <span>Laki-laki</span>
                                                                </div>
                                                                <div class="col">
                                                                    <?php 
                                                                    $builder_kar_pr = $db->table('t_ptk');
                                                                    
                                                                    $builder_kar_pr->where('status_ptk', 1);
                                                                    $builder_kar_pr->where('kd_jenis_kelamin', 2);
                                                                    $all_kar_pr =  $builder_kar_pr->countAllResults();
                                                                    ?>
                                                                    <h4 class="m-0 text-white"><?=$all_kar_pr;?></h4>
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
                                                                    $builder_hdrkar = $db->table('tweb_pegawai_hadir');
                                                                    $builder_hdrkar->where('TANGGAL', $tgl);
                                                                    $builder_hdrkar->where('STATUS', 0);
                                                                    $all_hdrkar =  $builder_hdrkar->countAllResults();
                                                                    ?>
                                                                    <h4 class="text-c-green"><a href="<?= base_url('Absensi/hadir'); ?>"><?=$all_hdrkar;?></a></h4>
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
                                                                    function persen($jml, $total){
                                                                        return ($total > 0) ? round(($jml / $total) * 100) : 0;
                                                                    }
                                                                    
                                                                    $pres_hadirkar = persen($all_hdrkar, $all_kar);                                                                    
                                                                    ?>
                                                                    <p class="text-white m-b-0"><?=round($pres_hadirkar);?>%</p>
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
                                                                    $jml_terlambatkar = jumterlambattgl(date('Y-m-d'));
                                                                    ?>
                                                                    <h4 class="text-c-yellow"><a href="<?= base_url('Absensi/terlambat'); ?>"><?=$jml_terlambatkar;?></a></h4>
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
                                                                    $pres_terkar = persen($jml_terlambatkar, $all_kar)
                                                                    ?>
                                                                    <p class="text-white m-b-0"><?=round($pres_terkar);?>%</p>
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
                                                                    $builder_alphakar = $db->table('t_ptk');
                                                                    $builder_alphakar->whereNotIn('nomor_absensi', static function (BaseBuilder $builder_alphakar) {
                                                                        $tgl = date('Y-m-d');
                                                                        $builder_alphakar->select('NO_INDUK')->from('tweb_pegawai_hadir')->where('TANGGAL', $tgl);
                                                                    });
                                                                    $all_alphakar =  $builder_alphakar->countAllResults();
                                                                    ?>
                                                                    <h4 class="text-c-red"><a href="<?= base_url('Absensi/alpha'); ?>"><?=$all_alphakar;?></a></h4>
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
                                                                    $pres_alphakar =  persen($all_alphakar, $all_kar);     
                                                                    ?>
                                                                    <p class="text-white m-b-0"><?=round($pres_alphakar);?>%</p>
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
                                                                    $builder_pulangkar = $db->table('tweb_pegawai_hadir');
                                                                    $builder_pulangkar->where('TANGGAL', $tgl);
                                                                    $builder_pulangkar->where('STATUS', 1);
                                                                    $all_pulangkar =  $builder_pulangkar->countAllResults();
                                                                    ?>
                                                                    <h4 class="text-c-blue"><a href="<?= base_url('Absensi/pulang'); ?>"><?=$all_pulangkar;?></a></h4>
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
                                                                    $pres_pulangkar =  persen($all_pulangkar, $all_kar);  
                                                                    ?>
                                                                    <p class="text-white m-b-0"><?=round($pres_pulangkar);?>%</p>
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
                                    <div class="row">
                                        <!-- [ static-layout ] start -->
                                        <div class="col-sm-6">
                                            <div class="card">
                                                <div class="card-header">
                                                    <div class="row">
                                                        <div class="col-10">
                                                        <h5>10 Karyawan Terbaik Bulan <?= bulan(date('m')) ?></h5>
                                                        </div>
                                                    
                                                    </div>

                                                </div>
                                                <div class="card-body table-border-style">
                                                    <div class="table-responsive">
                                                    <table class="table table-hover mb-0" id="example">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>#</th>
                                                                            <th>Nama Karyawan</th>
                                                                            <th>Jenis Kerja</th>
                                                                            <th>Jumlah Point</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody> 
                                                                    <?php 
                                                                    $no=0;
                                                                    foreach ($getPointkaryawan as $data) {  
                                                                    $no++;
                                                                    ?>
                                                                        <tr>
                                                                            <td><?= $no ?></td>
                                                                            <td><?= $data['nama_ptk'] ?></td>
                                                                            <td><?= $data['nama_jenis_ptk'] ?></td>
                                                                            <td><?= $data['jml_point'] ?></td>
                                                                        </tr>
                                                                    <?php } ?>  
                                                                    </tbody>
                                                                </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- [ static-layout ] end -->
                                        <div class="col-sm-6">
                                            <div class="card">
                                                <div class="card-header">
                                                    <div class="row">
                                                        <div class="col-10">
                                                        <h5>10 Siswa Terbaik Bulan <?= bulan(date('m')) ?></h5>
                                                        </div>
                                                    
                                                    </div>

                                                </div>
                                                <div class="card-body table-border-style">
                                                    <div class="table-responsive">
                                                    <table class="table table-hover mb-0" id="example">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>#</th>
                                                                            <th>Nama Siswa</th>
                                                                            <th>Kelas</th>
                                                                            <th>Jumlah Point</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    <?php 
                                                                    $no=0;
                                                                    foreach ($getPointsiswa as $data) {  
                                                                    $no++;
                                                                    ?>
                                                                        <tr>
                                                                            <td><?= $no ?></td>
                                                                            <td><?= $data['nm_siswa'] ?></td>
                                                                            <td><?= $data['nm_rombel'] ?></td>
                                                                            <td><?= $data['jml_point'] ?></td>
                                                                        </tr>
                                                                    <?php } ?>  
                                                                    </tbody>
                                                                </table>
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
    </div>