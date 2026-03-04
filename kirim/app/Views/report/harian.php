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
                                        <form method="post" action="<?= base_url('Absensi'); ?>">
                                        <div class="card-body table-border-style">
                                            <div class="row">
                                                <div class="col-3">
                                                    <input type="date" class="form-control" name="tgl" required>
                                                </div>
                                                <div class="col-3">
                                                    <select class="form-control" name="jenis" required>
                                                        <option>Pilih Jenis Ketenagaan</option>
                                                        <option value="All">All</option>
                                                        <?php foreach ($getJenis as $data) { ?>
                                                        <option value="<?=$data['id_jenis_ptk'] ?>"><?=$data['nama_jenis_ptk'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="col-3">
                                                    <select class="form-control" name="shift" required>
                                                        <option>Pilih Shift</option>
                                                        <option value="All">All</option>
                                                        <?php foreach ($getShift as $data) { ?>
                                                        <option value="<?=$data['id_shift'] ?>"><?=$data['nm_shift'] ?></option>
                                                        <?php } ?>
                                                    </select>
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
                                                <h5>Tabel Data Absensi Tanggal <?=formatTanggal($getTanggal);?></h5>
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
                                                            <th>NIP</th>
                                                            <th>Nama Pegawai</th>
                                                            <th>Jenis Kerja</th>
                                                            <th>Jam Masuk</th>
                                                            <th>Status</th>
                                                            <th>Jam Pulang</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php 
                                                        $no=0;
                                                        $tgl = date('Y-m-d');
                                                        foreach ($getAbsensi as $data) {  
                                                        $no++;
                                                        $induk =$data['nomor_absensi'];
                                                        $id =$data['id_ptk'];
                                                        ?>
                                                        <tr>
                                                            <td><?= $no ?></td>
                                                            <td><?= $data['nomor_absensi'] ?></td>
                                                            <td><?= $data['nip'] ?></td>
                                                            <td><?= $data['nama_ptk'] ?></td>
                                                            <td><?= $data['nama_jenis_ptk'] ?></td>
                                                            <td><?= jammasuk($id,$getTanggal) ?></td>
                                                            <td><?= sts_absen($id,$getTanggal) ?></td>
                                                            <td><?= jampulang($id,$getTanggal) ?></td>
                                                            
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
