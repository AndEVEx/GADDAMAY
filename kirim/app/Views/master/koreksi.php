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
                                                <div class="col-10">
                                                <h5>Filter Data</h5>
                                                </div>
                                               
                                            </div>
                                     
                                        </div>
                                        <form method="post" action="<?= base_url('Absensi/koreksi'); ?>">
                                        <div class="card-body table-border-style">
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-sm">
                                                        <div class="form-group">
                                                            <label>Jenis Pegawai</label>
                                                            <select class="form-control" name="jenis" required>
                                                                <option>Pilih</option>    
                                                                <?php foreach ($getJenis as $data) { ?>
                                                                <option value="<?=$data['id_jenis_ptk'] ?>"><?=$data['nama_jenis_ptk'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm">
                                                        <div class="form-group">
                                                            <label>Tanggal</label>
                                                            <input type="date" class="form-control" name="tgl" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm">
                                                        <button type="submit" class="btn btn-danger">Lihat Data</button>
                                                    </div>
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
                                                <h5>Tabel Data Koreksi Absensi</h5>
                                                </div>
                                               
                                            </div>
                                            
                                        <?php if(session()->get('success')) : ?>
                                            <div class="alert alert-success alert-dismissible">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                            <center><h5><i class="icon fas fa-check"></i> Data sukses <strong><?= session()->getFlashdata('success'); ?></strong></h5></center> 
                                            </div>
                                        <?php endif; ?>

                                        <?php if(session()->get('error')) : ?>
                                            <div class="alert alert-danger alert-dismissible">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                            <center><h5><i class="icon fas fa-check"></i> Data gagal <strong><?= session()->getFlashdata('error'); ?></strong></h5></center> 
                                            </div>
                                        <?php endif; ?>
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
                                                            <th>Status Absen</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php 
                                                        $no=0;
                                                        foreach ($getPegawai as $data) {  
                                                            $id = $data['id_ptk'];
                                                        $no++;
                                                        ?>
                                                        <tr>
                                                            <td><?= $no ?></td>
                                                            <td><?= $data['nomor_absensi'] ?></td>
                                                            <td><?= $data['nip'] ?></td>
                                                            <td><?= $data['nama_ptk'] ?></td>
                                                            <td>
                                                                <?php 
                                                                if(sts_absen($id,$getTanggal)=='Masuk'){
                                                                ?>
                                                                  <span class="badge badge-pill badge-success"><?= sts_absen($id,$getTanggal) ?></span>
                                                                <?php  
                                                                }elseif(sts_absen($id,$getTanggal)=='Sakit' || sts_absen($id,$getTanggal)=='Izin'){
                                                                ?>
                                                                    <span class="badge badge-pill badge-info"><?= sts_absen($id,$getTanggal) ?></span>
                                                                <?php 
                                                                }elseif(sts_absen($id,$getTanggal)=='Terlambat'){
                                                                ?>
                                                                        <span class="badge badge-pill badge-warning"><?= sts_absen($id,$getTanggal) ?></span>
                                                                <?php 
                                                                }else{
                                                                ?>   
                                                                    <span class="badge badge-pill badge-danger"><?= sts_absen($id,$getTanggal) ?></span>
                                                                <?php
                                                                }
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <button class="btn btn-success btn-sm" type="submit" data-toggle="modal" data-target="#editmasuk<?=$id ?>"><i class="feather icon-edit-2"></i></button>
                                                                <?php if(sts_absen($id,$getTanggal)=='Alpha' || sts_absen($id,$getTanggal)=='Sakit' || sts_absen($id,$getTanggal)=='Izin'){?>
                                                                <button class="btn btn-warning btn-sm" type="submit" data-toggle="modal" data-target="#edittidak<?=$id ?>"><i class="feather icon-edit-2"></i></button>
                                                                <?php }else{ ?>
                                                                    <button class="btn btn-warning btn-sm" type="submit" disabled><i class="feather icon-edit-2"></i></button>
                                                                <?php } ?>
                                                              <!-- Edit The Modal -->
                                                              <div class="modal fade" id="editmasuk<?=$id ?>">
                                                                <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                                
                                                                    <!-- Modal Header -->
                                                                    <div class="modal-header">
                                                                    <h4 class="modal-title">Edit Absen Masuk</h4>
                                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                    </div>
                                                                                    
                                                                    <!-- Modal body -->
                                                                    <form method="post" action="<?= base_url('Absensi/updatemasuk'); ?>">
                                                                        <div class="modal-body">
                                                                            <div class="row">
                                                                                
                                                                                <div class="col-sm-12">
                                                                                    <div class="form-group">
                                                                                        <label>Nama</label>
                                                                                        <input type="text" class="form-control" readonly value="<?= $data['nama_ptk'] ?>" required>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-12">
                                                                                    <div class="form-group">
                                                                                        <label>Ubah Status Absen</label>
                                                                                        <select class="form-control" name="sts" required readonly>
                                                                                            <option value="0">Masuk</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-12">
                                                                                    <div class="form-group">
                                                                                        <label>Jam Masuk</label>
                                                                                        <input type="time" class="form-control" name="jam" required>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <input type="hidden" class="form-control" name="id" value="<?= $id ?>" required> 
                                                                            <input type="hidden" class="form-control" name="tgl" value="<?= $getTanggal ?>" required> 
                                                                            <input type="hidden" class="form-control" name="sts" value="<?= sts_absen($id,$getTanggal) ?>" required> 
                                                                            <input type="hidden" class="form-control" name="nomor_absensi" value="<?= $data['nomor_absensi'] ?>" required>  
                                                                            <button type="submit" class="btn btn-danger">Save</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                                </div>
                                                                </div>

                                                                <!-- Edit The Modal -->
                                                              <div class="modal fade" id="edittidak<?=$id ?>">
                                                                <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                                
                                                                    <!-- Modal Header -->
                                                                    <div class="modal-header">
                                                                    <h4 class="modal-title">Edit Absen Tidak Masuk</h4>
                                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                    </div>
                                                                                    
                                                                    <!-- Modal body -->
                                                                    <form method="post" action="<?= base_url('Absensi/updatetidakmasuk'); ?>">
                                                                        <div class="modal-body">
                                                                            <div class="row">
                                                                                
                                                                                <div class="col-sm-12">
                                                                                    <div class="form-group">
                                                                                        <label>Nama</label>
                                                                                        <input type="text" class="form-control" readonly value="<?= $data['nama_ptk'] ?>" required>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-12">
                                                                                    <div class="form-group">
                                                                                        <label>Ubah Status Absen</label>
                                                                                        <select class="form-control" name="sts" required>
                                                                                            <option>Pilih</option>
                                                                                            <option value="2">Sakit</option>
                                                                                            <option value="3">Izin</option>
                                                                                            <option value="4">Alpha</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-12">
                                                                                    <div class="form-group">
                                                                                        <label>Keterangan</label>
                                                                                        <input type="text" class="form-control" name="keterangan" required>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <input type="hidden" class="form-control" name="id" value="<?= $id ?>" required> 
                                                                            <input type="hidden" class="form-control" name="tgl" value="<?= $getTanggal ?>" required> 
                                                                            <input type="hidden" class="form-control" name="status" value="<?= sts_absen($id,$getTanggal) ?>" required>
                                                                            <input type="hidden" class="form-control" name="nomor_absensi" value="<?= $data['nomor_absensi'] ?>" required>   
                                                                            <button type="submit" class="btn btn-danger">Save</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                                </div>
                                                                </div>

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
