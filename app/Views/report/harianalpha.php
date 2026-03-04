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
                                                <li class="breadcrumb-item"><a href="#!"> Info Absensi</a></li>
                                                <li class="breadcrumb-item"><a href="<?= base_url($nav); ?>">Harian Alpha</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- [ breadcrumb ] end -->
                           
                            <div class="row">
                                <!-- [ static-layout ] start -->
                                <div class="col-sm-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-6">
                                                <h5>Tabel Data Alpha Tanggal <?=formatTanggal($getTanggal);?></h5>
                                                </div>
                                                
                                            </div>
                                            
                                        </div>
                                        <div class="card-body table-border-style">
                                            <div class="table-responsive">
                                                <table id="example" class="table table-striped table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>No. Induk</th>
                                                            <th>Nama Siswa</th>
                                                            <th>Kelas</th>
                                                            <th>Status</th>
                                                            
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php 
                                                        $no=0;
                                                        $tgl = date('Y-m-d');
                                                        //ambil data siswa alpha
                                                        $db = \Config\Database::connect();
                                                        $query = $db->query("SELECT t_siswa.id_siswa as id_siswa,no_induk,nm_siswa,nm_rombel FROM t_siswa 
                                                        JOIN t_siswa_rombel ON t_siswa_rombel.id_siswa = t_siswa.id_siswa
                                                        JOIN t_rombel ON t_rombel.id_rombel = t_siswa_rombel.id_rombel
                                                        WHERE t_siswa.id_siswa NOT IN (SELECT id_siswa FROM t_siswa_hadir Where tgl_hadir='$tgl') ");
                                                        foreach ($query->getResult() as $row) {
                                                        $no++;
                                                        $id_siswa =$row->id_siswa;
                                                        ?>
                                                        <tr>
                                                            <td><?= $no ?></td>
                                                            <td><?= $row->no_induk ?></td>
                                                            <td><?= $row->nm_siswa ?></td>
                                                            <td><?= $row->nm_rombel ?></td>
                                                            <td><?= sts_absen($id_siswa,$getTanggal) ?></td>
                                                            
                                                        </tr>    
                                                       <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- [ static-layout ] end -->
                            </div>
                            <!-- [ Main Content ] end -->
                           
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
