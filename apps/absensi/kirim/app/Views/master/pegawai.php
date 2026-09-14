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
                                                <li class="breadcrumb-item"><a href="#!"> Master</a></li>
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
                                                <h5>Tabel Data Pegawai</h5>
                                                </div>
                                                <div class="col-2">
                                                    <button class="btn btn-primary btn-sm" type="submit" data-toggle="modal" data-target="#add"><i class="feather icon-plus"></i> Tambah</button>
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
                                                            <th>Nama Panggilan</th>
                                                            <th>Jenis Kerja</th>
                                                            <th>Nomor HP</th>
                                                            <th>Status Pegawai</th>
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
                                                            <td><?= $data['nama_panggilan'] ?></td>
                                                            <td><?= $data['nama_jenis_ptk'] ?></td>
                                                            <td><?= $data['no_hp'] ?></td>
                                                            <td>
                                                                <?php if($data['status_ptk']==1){ ?>
                                                                    <span class="badge badge-pill badge-success"><?= stsptk($data['status_ptk']) ?></span>
                                                                <?php }else{ ?>
                                                                    <span class="badge badge-pill badge-danger"><?= stsptk($data['status_ptk']) ?></span>
                                                                <?php } ?>
                                                            </td>
                                                            <td>
                                                                <button class="btn btn-warning btn-sm" type="submit" data-toggle="modal" data-target="#edit<?=$id;?>"><i class="feather icon-edit-2"></i></button>
                                                                <button class="btn btn-info btn-sm" type="submit" data-toggle="modal" data-target="#editpass<?=$id;?>"><i class="feather icon-lock"></i></button>
                                                                <button class="btn btn-danger btn-sm" type="submit" data-toggle="modal" data-target="#delete<?=$id;?>"><i class="feather icon-trash"></i></button>
                                                            
                                                                <!-- Edit The Modal -->
                                                                <div class="modal fade" id="edit<?=$id;?>">
                                                                    <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                                
                                                                    <!-- Modal Header -->
                                                                    <div class="modal-header">
                                                                    <h4 class="modal-title">Edit Data</h4>
                                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                    </div>
                                                                                    
                                                                    <!-- Modal body -->
                                                                    <form method="post" action="<?= base_url('Pegawai/update'); ?>">
                                                                        <div class="modal-body">
                                                                            <div class="row">
                                                                               
                                                                            <div class="col-sm-6">
                                                                                <div class="form-group">
                                                                                    <label>Nomor Finger</label>
                                                                                    <input type="number" class="form-control" value="<?= $data['nomor_absensi'] ?>" name="no_finger" required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-6">
                                                                                <div class="form-group">
                                                                                    <label>NIP</label>
                                                                                    <input type="number" class="form-control" value="<?= $data['nip'] ?>" name="nip" required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-12">
                                                                                <div class="form-group">
                                                                                    <label>Nama Pegawai</label>
                                                                                    <input type="text" class="form-control" value="<?= $data['nama_ptk'] ?>" name="nama" required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-6">
                                                                                <div class="form-group">
                                                                                    <label>NIK</label>
                                                                                    <input type="number" class="form-control" value="<?= $data['nik'] ?>" name="nik" required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-6">
                                                                                <div class="form-group">
                                                                                    <label>Nama Panggilan</label>
                                                                                    <input type="text" class="form-control" value="<?= $data['nama_panggilan'] ?>" name="nm_panggilan" required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-12">
                                                                                <div class="form-group">
                                                                                    <label>Alamat</label>
                                                                                    <input type="text" class="form-control" value="<?= $data['alamat'] ?>" name="alamat" required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-6">
                                                                                <div class="form-group">
                                                                                    <label>Tempat Lahir</label>
                                                                                    <input type="text" class="form-control" value="<?= $data['tempat_lahir'] ?>" name="tempat_lahir" required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-6">
                                                                                <div class="form-group">
                                                                                    <label>Tanggal Lahir</label>
                                                                                    <input type="date" class="form-control" value="<?= $data['tgl_lahir'] ?>" name="tgl_lahir" required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-6">
                                                                                <div class="form-group">
                                                                                    <label>Jenis Kerja</label>
                                                                                    <select class="form-control" name="jenis" required>
                                                                                        <?php foreach ($getJenis as $data_jenis) { ?>
                                                                                        <option value="<?=$data_jenis['id_jenis_ptk'] ?>" <?php if($data_jenis['id_jenis_ptk']==$data['id_jenis_ptk']) { ?> selected="" <?php } ?>><?=$data_jenis['nama_jenis_ptk'] ?></option>
                                                                                        <?php } ?>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-6">
                                                                                <div class="form-group">
                                                                                    <label>Jenis Kelamin</label>
                                                                                    <select class="form-control" name="jk" required>
                                                                                        <option>Pilih</option>
                                                                                        <option value="1" <?php if($data['kd_jenis_kelamin']==1) { ?> selected="" <?php } ?>>Laki-laki</option>
                                                                                        <option value="2" <?php if($data['kd_jenis_kelamin']==2) { ?> selected="" <?php } ?>>Perempuan</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-6">
                                                                                <div class="form-group">
                                                                                    <label>Nomor HP</label>
                                                                                    <input type="number" value="<?= $data['no_hp'] ?>" class="form-control" name="hp" required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-6">
                                                                                <div class="form-group">
                                                                                    <label>Status Pegawai</label>
                                                                                    <select class="form-control" name="sts" required>
                                                                                        <option>Pilih</option>
                                                                                        <option value="1" <?php if($data['status_ptk']==1) { ?> selected="" <?php } ?>>Aktif</option>
                                                                                        <option value="2" <?php if($data['status_ptk']==2) { ?> selected="" <?php } ?>>Tidak Aktif</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            </div>
                                                                            <input type="hidden" class="form-control" name="id" value="<?=$id;?>" required>   
                                                                            <button type="submit" class="btn btn-danger">Save</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                                </div>
                                                                </div>

                                                              <!-- Edit The Modal -->
                                                              <div class="modal fade" id="editpass<?=$id;?>">
                                                                    <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                                
                                                                    <!-- Modal Header -->
                                                                    <div class="modal-header">
                                                                    <h4 class="modal-title">Edit Data Password</h4>
                                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                    </div>
                                                                                    
                                                                    <!-- Modal body -->
                                                                    <form method="post" action="<?= base_url('Pegawai/updatepassword'); ?>">
                                                                        <div class="modal-body">
                                                                            <div class="row">
                                                                                
                                                                                <div class="col-sm-12">
                                                                                    <div class="form-group">
                                                                                        <label>Nama</label>
                                                                                        <input type="text" class="form-control" readonly value="<?=$data['nama_ptk']?>" required>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-12">
                                                                                    <div class="form-group">
                                                                                        <label>Password</label>
                                                                                        <input type="text" class="form-control" name="password1" required>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-12">
                                                                                    <div class="form-group">
                                                                                        <label>Ulangi Password</label>
                                                                                        <input type="text" class="form-control" name="password2" required>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <input type="hidden" class="form-control" name="id" value="<?=$id;?>" required>   
                                                                            <button type="submit" class="btn btn-danger">Save</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                                </div>
                                                                </div>

                                                                <!-- delete The Modal -->
                                                                <div class="modal fade" id="delete<?=$id?>">
                                                                    <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                                
                                                                    <!-- Modal Header -->
                                                                    <div class="modal-header">
                                                                    <h4 class="modal-title">Hapus Data</h4>
                                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                    </div>
                                                                                    
                                                                    <!-- Modal body -->
                                                                    <form method="post" action="<?= base_url('Pegawai/hapus'); ?>">
                                                                        <div class="modal-body">
                                                                            Apakah anda yakin ingin menghapus Data user <?=$data['nama_ptk'];?>?
                                                                            <input type="hidden" name="id" value="<?=$id;?>">
                                                                            <br>
                                                                            <br>
                                                                            <button type="submit" class="btn btn-danger">Delete</button>
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

<!-- Edit The Modal -->
<div class="modal fade" id="add">
<div class="modal-dialog">
<div class="modal-content">
            
<!-- Modal Header -->
<div class="modal-header">
<h4 class="modal-title">Tambah Data Pegawai</h4>
<button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
                
<!-- Modal body -->
<form class="was-validated" method="post" action="<?= base_url('Pegawai/add'); ?>">
    <div class="modal-body">
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Nomor Finger</label>
                    <input type="number" class="form-control" name="no_finger" required>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>NIP</label>
                    <input type="number" class="form-control" name="nip" required>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Nama Pegawai</label>
                    <input type="text" class="form-control" name="nama" required>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>NIK</label>
                    <input type="number" class="form-control" name="nik" required>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Nama Panggilan</label>
                    <input type="text" class="form-control" name="nm_panggilan" required>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Alamat</label>
                    <input type="text" class="form-control" name="alamat" required>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Tempat Lahir</label>
                    <input type="text" class="form-control" name="tempat_lahir" required>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Tanggal Lahir</label>
                    <input type="date" class="form-control" name="tgl_lahir" required>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Jenis Kerja</label>
                    <select class="form-control" name="jenis" required>
                        <?php foreach ($getJenis as $data) { ?>
                        <option value="<?=$data['id_jenis_ptk'] ?>"><?=$data['nama_jenis_ptk'] ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Jenis Kelamin</label>
                    <select class="form-control" name="jk" required>
                        <option>Pilih</option>
                        <option value="1">Laki-laki</option>
                        <option value="2">Perempuan</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Nomor HP</label>
                    <input type="number" class="form-control" name="hp" required>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" class="form-control" name="password" required>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Status Pegawai</label>
                    <select class="form-control" name="sts" required>
                        <option>Pilih</option>
                        <option value="1">Aktif</option>
                        <option value="2">Tidak Aktif</option>
                    </select>
                </div>
            </div>
        </div>
         
        <button type="submit" class="btn btn-danger">Save</button>
    </div>
</form>

</div>
</div>
</div>