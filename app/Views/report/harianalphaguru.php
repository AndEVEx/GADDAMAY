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
                                                <li class="breadcrumb-item"><a href="<?= base_url($nav); ?>">Harian Tidak Hadir</a></li>
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
                                                <h5>Tabel Data Tidak Hadir Tanggal <?=formatTanggal($getTanggal);?></h5>
                                                </div>
                                                
                                            </div>
                                            
                                        </div>
                                        <div class="card-body table-border-style">
                                            <div class="table-responsive">
                                                <table id="example" class="table table-striped table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Nomor Finger</th>
                                                            <th>Nama Pegawai</th>
                                                            <th>Jenis Kerja</th>
                                                            <th>Status</th>
                                                            
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php 
                                                        $no=0;
                                                       
                                                        //ambil data siswa alpha
                                                        $db = \Config\Database::connect();
                                                        $query = $db->query("SELECT t_ptk.id_ptk as id_ptk,nama_ptk,nomor_absensi,nama_jenis_ptk FROM t_ptk
                                                        JOIN r_jenis_ptk ON r_jenis_ptk.id_jenis_ptk = t_ptk.id_jenis_ptk
                                                        WHERE t_ptk.id_ptk NOT IN (SELECT ID_PEGAWAI FROM tweb_pegawai_hadir Where TANGGAL='$getTanggal') ");
                                                        foreach ($query->getResult() as $row) {
                                                        $no++;
                                                        $id_ptk =$row->id_ptk;
                                                        ?>
                                                        <tr>
                                                            <td><?= $no ?></td>
                                                            <td><?= $row->nomor_absensi ?></td>
                                                            <td><?= $row->nama_ptk ?></td>
                                                            <td><?= $row->nama_jenis_ptk ?></td>
                                                            <td><?= sts_absen($id_ptk,$getTanggal) ?></td>
                                                            
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
