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
                                                <li class="breadcrumb-item"><a href="<?= base_url($nav); ?>"><?=$nav;?></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- [ breadcrumb ] end -->
                            <!-- [ Main Content ] start -->
                            <div class="row">
                                <!-- [ static-layout ] start -->
                                <div class="col-sm-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-6">
                                                <h5>Filter Data</h5>
                                                </div>
                                                
                                            </div>
                                            
                                        </div>
                                        <form method="post" action="<?= base_url('Reportwal'); ?>">
                                        <div class="card-body table-border-style">
                                            <div class="row">
                                                <div class="col-3">
                                                    <input type="date" class="form-control" name="tgl" required>
                                                </div>
                                               
                                                <div class="col-2">
                                                <button type="submit" class="btn btn-danger">Cari Data</button>
                                                </div>
                                            </div>
                                        </div>
                                        </form>
                                    </div>
                                </div>
                                <!-- [ static-layout ] end -->
                            </div>
                            <div class="row">
                                <!-- [ static-layout ] start -->
                                <div class="col-sm-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-6">
                                                <h5>Tabel Data Absensi Tanggal <?=formatTanggal($getTanggal);?>, Kelas <?= $nmRombel ?></h5>
                                                </div>
                                                <div class="col-6 text-right">
                                                    <a href="<?= base_url('Reportwal/cetakharian/?tgl='.$getTanggal) ?>" class="btn btn-warning btn-sm" target="_blank"><i class="feather icon-printer"></i> Print PDF</a>
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
                                                            <th>Jam Masuk</th>
                                                            <th>Status</th>
                                                            <th>Jam Pulang</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php 
                                                        $no=0;
                                                        $tgl = date('Y-m-d');
                                                        foreach ($getSiswa as $data) {  
                                                        $no++;
                                                        $id_siswa =$data['id_siswa'];
                                                        ?>
                                                        <tr>
                                                            <td><?= $no ?></td>
                                                            <td><?= $data['no_induk'] ?></td>
                                                            <td><?= $data['nm_siswa'] ?></td>
                                                            <td><?= jammasuk($id_siswa,$getTanggal) ?></td>
                                                            <td><?= sts_absen($id_siswa,$getTanggal) ?></td>
                                                            <td><?= jampulang($id_siswa,$getTanggal) ?></td>
                                                            <td>
                                                                <a href="<?= base_url('Absensisiswa/koreksiwali/?tgl='.$getTanggal) ?>" class="btn btn-primary btn-sm"><i class="feather icon-edit"></i> Koreksi</a>
                                                            </td>
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
