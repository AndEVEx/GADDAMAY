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
                                                            $db = \Config\Database::connect();
                                                            $builder_dsn = $db->table('t_ptk');
                                                            $builder_dsn->where('id_jenis_ptk', 1);
                                                            $builder_dsn->where('status_ptk', 1);
                                                            $all_dsn =  $builder_dsn->countAllResults();
                                                            ?>
                                                            <h2 class="m-0"><?=$all_dsn;?></h2>
                                                            <span class="text-c-blue">Jumlah Dosen</span>
                                                            <p class="mb-3 mt-3"></p>
                                                        </div>
                                                        <div id="support-chart"></div>
                                                        <div class="card-footer bg-primary text-white">
                                                            <div class="row text-center">
                                                                <div class="col">
                                                                    <?php 
                                                                    $builder_dsn_lk = $db->table('t_ptk');
                                                                    $builder_dsn_lk->where('id_jenis_ptk', 1);
                                                                    $builder_dsn_lk->where('status_ptk', 1);
                                                                    $builder_dsn_lk->where('kd_jenis_kelamin', 1);
                                                                    $all_dsn_lk =  $builder_dsn_lk->countAllResults();
                                                                    ?>
                                                                    <h4 class="m-0 text-white"><?=$all_dsn_lk;?></h4>
                                                                    <span>Laki-laki</span>
                                                                </div>
                                                                <div class="col">
                                                                    <?php 
                                                                    $builder_dsn_pr = $db->table('t_ptk');
                                                                    $builder_dsn_pr->where('id_jenis_ptk', 1);
                                                                    $builder_dsn_pr->where('status_ptk', 1);
                                                                    $builder_dsn_pr->where('kd_jenis_kelamin', 2);
                                                                    $all_dsn_pr =  $builder_dsn_pr->countAllResults();
                                                                    ?>
                                                                    <h4 class="m-0 text-white"><?=$all_dsn_pr;?></h4>
                                                                    <span>Perempuan</span>
                                                                </div>
                                                               
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-sm-6">
                                                    <div class="card support-bar overflow-hidden">
                                                        <div class="card-body pb-0">
                                                            <?php 
                                                            $builder_kry = $db->table('t_ptk');
                                                            $builder_kry->where('id_jenis_ptk', 2);
                                                            $builder_kry->where('status_ptk', 1);
                                                            $all_kry =  $builder_kry->countAllResults();
                                                            ?>
                                                            <h2 class="m-0"><?=$all_kry;?></h2>
                                                            <span class="text-c-yellow">Jumlah Karyawan</span>
                                                            <p class="mb-3 mt-3"></p>
                                                        </div>
                                                        <div id="support-chart"></div>
                                                        <div class="card-footer bg-warning text-white">
                                                            <div class="row text-center">
                                                                <div class="col">
                                                                    <?php 
                                                                    $builder_kry_lk = $db->table('t_ptk');
                                                                    $builder_kry_lk->where('id_jenis_ptk', 2);
                                                                    $builder_kry_lk->where('status_ptk', 1);
                                                                    $builder_kry_lk->where('kd_jenis_kelamin', 1);
                                                                    $all_kry_lk =  $builder_kry_lk->countAllResults();
                                                                    ?>
                                                                    <h4 class="m-0 text-white"><?=$all_kry_lk;?></h4>
                                                                    <span>Laki-laki</span>
                                                                </div>
                                                                <div class="col">
                                                                    <?php 
                                                                    $builder_kry_pr = $db->table('t_ptk');
                                                                    $builder_kry_pr->where('id_jenis_ptk', 2);
                                                                    $builder_kry_pr->where('status_ptk', 1);
                                                                    $builder_kry_pr->where('kd_jenis_kelamin', 2);
                                                                    $all_kry_pr =  $builder_kry_pr->countAllResults();
                                                                    ?>
                                                                    <h4 class="m-0 text-white"><?=$all_kry_pr;?></h4>
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
                                                                    $builder_hdr = $db->table('tweb_pegawai_hadir');
                                                                    $builder_hdr->where('TANGGAL', $tgl);
                                                                    $builder_hdr->where('STATUS', 0);
                                                                    $all_hdr =  $builder_hdr->countAllResults();
                                                                    ?>
                                                                    <h4 class="text-c-green"><?=$all_hdr;?></h4>
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
                                                                    $pres_hadir = ($all_hdr/($all_dsn+$all_kry))*100;
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
                                                                    //ambil data absensi berdasarkan tgl sekarang
                                                                    $query_abs = $db->query("SELECT tweb_pegawai_hadir.NO_INDUK as NO_INDUK, tweb_pegawai_hadir.JAM as JAM, t_anggota_shift.id_shift as id_shift, jam_masuk FROM tweb_pegawai_hadir
                                                                    JOIN t_ptk ON t_ptk.nomor_absensi = tweb_pegawai_hadir.NO_INDUK
                                                                    JOIN t_anggota_shift ON t_ptk.id_ptk = t_anggota_shift.id_ptk
                                                                    JOIN r_shift ON t_anggota_shift.id_shift = r_shift.id_shift
                                                                    where TANGGAL='$tgl' AND STATUS=0");
                                                                    $row_jml  = $query_abs->getResult();
                                                                    $jumlah_ter = count($row_jml);
                                                                    if($jumlah_ter>0){
                                                                        foreach ($query_abs->getResult() as $row_abs) {
                                                                            $induk = $row_abs->NO_INDUK;
                                                                            $jam_abs = $row_abs->JAM;
                                                                            $jam_masuk = $row_abs->jam_masuk;
                                                                            if( $jam_abs>$jam_masuk){
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
                                                                    <h4 class="text-c-yellow"><?=$jml_terlambat;?></h4>
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
                                                                    $pres_ter = ($jml_terlambat/($all_dsn+$all_kry))*100;
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
                                                                    $builder_alpha = $db->table('t_ptk');
                                                                    $builder_alpha->whereNotIn('nomor_absensi', static function (BaseBuilder $builder_alpha) {
                                                                        $tgl = date('Y-m-d');
                                                                        $builder_alpha->select('NO_INDUK')->from('tweb_pegawai_hadir')->where('TANGGAL', $tgl);
                                                                    });
                                                                    $all_alpha =  $builder_alpha->countAllResults();
                                                                    ?>
                                                                    <h4 class="text-c-red"><?=$all_alpha;?></h4>
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
                                                                    $pres_alpha = ($all_alpha/($all_dsn+$all_kry))*100;
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
                                                                    $builder_pulang = $db->table('tweb_pegawai_hadir');
                                                                    $builder_pulang->where('TANGGAL', $tgl);
                                                                    $builder_pulang->where('STATUS', 1);
                                                                    $all_pulang =  $builder_pulang->countAllResults();
                                                                    ?>
                                                                    <h4 class="text-c-blue"><?=$all_pulang;?></h4>
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
                                                                    $pres_pulang = ($all_pulang/($all_dsn+$all_kry))*100;
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
                                        <!-- prject ,team member start -->
                                        <div class="col-xl-12 col-md-12">
                                            <div class="card table-card">
                                                <div class="card-header">
                                                    <h5>Grafik Absensi Tahun <?=date('Y');?></h5>
                                                </div>
                                                <div class="card-body">
                                                    <div id="bar-chart-1"></div> 
                                                </div>
                                            </div>
                                        </div>
                                        <!-- prject ,team member start -->
                                        

                                        <div class="col-xl-12 col-md-12">
                                            <div class="card table-card">
                                                <div class="card-header">
                                                    <h5>5 Terbaik Karyawan Bulan <?=bulan(date('m'));?></h5>
                                                    <div class="card-header-right">
                                                        <div class="btn-group card-option">
                                                            <button type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                <i class="feather icon-more-horizontal"></i>
                                                            </button>
                                                            <ul class="list-unstyled card-option dropdown-menu dropdown-menu-right">
                                                                <li class="dropdown-item full-card"><a href="#!"><span><i class="feather icon-maximize"></i> maximize</span><span style="display:none"><i class="feather icon-minimize"></i> Restore</span></a></li>
                                                                <li class="dropdown-item minimize-card"><a href="#!"><span><i class="feather icon-minus"></i> collapse</span><span style="display:none"><i class="feather icon-plus"></i> expand</span></a></li>
                                                                <li class="dropdown-item reload-card"><a href="#!"><i class="feather icon-refresh-cw"></i> reload</a></li>
                                                                <li class="dropdown-item close-card"><a href="#!"><i class="feather icon-trash"></i> remove</a></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div> 
                                                <div class="card-body p-0">
                                                    <div class="table-responsive">
                                                        <table class="table table-hover mb-0">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Nama Karyawan</th>
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
                                                                    <td><?= $data['point'] ?></td>
                                                                </tr>
                                                            <?php } ?>  
                                                            </tbody>
                                                        </table>
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