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
                                        <form method="post" action="<?= base_url('Absensi/bulanan'); ?>">
                                        <div class="card-body table-border-style">
                                            <div class="row">
                                                <div class="col-3">
                                                    <select class="form-control" name="bln" required>
                                                        <option>Pilih Bulan</option>
                                                        <option value="1">Januari</option>
                                                        <option value="2">Februari</option>
                                                        <option value="3">Maret</option>
                                                        <option value="4">April</option>
                                                        <option value="5">Mei</option>
                                                        <option value="6">Juni</option>
                                                        <option value="7">Juli</option>
                                                        <option value="8">Agustus</option>
                                                        <option value="9">September</option>
                                                        <option value="10">Oktober</option>
                                                        <option value="11">November</option>
                                                        <option value="12">Desember</option>
                                                    </select>
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
                                                <div class="col-10">
                                                <h5>Tabel Data Absensi Bulan <?=bulan($getBulan);?></h5>
                                                </div>
                                                <div class="col-2">
                                                    <form class="was-validated" method="post" action="<?= base_url('Export/bulanan'); ?>">
                                                        <input type="hidden" name="bln" value="<?=$getBulan;?>">
                                                        <button class="btn btn-success btn-sm" type="submit"><i class="feather icon-external-link"></i> Export</button>
                                                    </form>
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
                                                            <th>Masuk</th>
                                                            <th>Terlambat</th>
                                                            <th>Sakit</th>
                                                            <th>Izin</th>
                                                            <th>Alpha</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php 
                                                        $no=0;
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
                                                            <td><?= jummasukbln($id,$getBulan) ?></td>
                                                            <td><?= jumterlambatbln($id,$getBulan) ?></td>
                                                            <td><?= jumsakitbln($id,$getBulan) ?></td>
                                                            <td><?= jumizinbln($id,$getBulan) ?></td>
                                                            <td><?= jumalphabln($id,$getBulan) ?></td>
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
