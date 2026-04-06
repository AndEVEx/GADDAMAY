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
                                                <div class="col-8">
                                                <h5>Tabel Data Siswa</h5>
                                                </div>
                                                <div class="col-2">
                                                <a href="<?= base_url('Siswa/printkartuall'); ?>" target="_blank">
                                                    <button class="btn btn-primary btn-sm" type="submit"><i class="feather icon-printer"></i> Print All</button>
                                                </a>
                                                </div>
                                                <div class="col-2">
                                                    <button class="btn btn-primary btn-sm" type="submit" data-toggle="modal" data-target="#add"><i class="feather icon-plus"></i> Tambah</button>
                                                </div>
                                                
                                            </div>
                                            
                                      
                                        </div>
                                        <div class="card-body table-border-style">
                                            <form id="formBulkDelete" method="post" action="<?= base_url('Siswa/bulkDelete') ?>">
                                            <div class="mb-3">
                                                <button type="submit" class="btn btn-danger btn-sm" id="btnBulkDelete" style="display:none" onclick="return confirm('Yakin ingin menghapus siswa terpilih?')">
                                                    <i class="feather icon-trash-2"></i> Hapus Terpilih (<span id="selectedCount">0</span>)
                                                </button>
                                            </div>
                                            <div class="table-responsive">
                                                <table id="example" class="table table-striped table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th><input type="checkbox" id="selectAll"></th>
                                                            <th>#</th>
                                                            <th>Aksi</th>
                                                            <th>Foto</th>
                                                            <th>Cetak Kartu</th>
                                                            <th>Nisn</th>
                                                            <th>Nomor induk</th>
                                                            <th>RFID</th>
                                                            <th>Nama Siswa</th>
                                                            <th>Alamat</th>
                                                            <th>TTL</th>
                                                            <th>JK</th>
                                                            <th>Nomor HP</th>
                                                            <th>Status Siswa</th>
                                                           
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php 
                                                        $no=0;
                                                        foreach ($getSiswa as $data) {  
                                                            $id = $data['id_siswa'];
                                                        $no++;
                                                        ?>
                                                        <tr>
                                                            <td><input type="checkbox" name="ids[]" value="<?=$id;?>" class="row-select"></td>
                                                            <td><?= $no ?></td>
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
                                                                    <form method="post" action="<?= base_url('Siswa/update'); ?>" enctype="multipart/form-data">
                                                                        <div class="modal-body">
                                                                            <div class="row">
                                                                            <div class="col-sm-6">
                                                                                <div class="form-group">
                                                                                    <label>Nisn</label>
                                                                                    <input type="number" class="form-control" value="<?= $data['nisn'] ?>" name="nisn" required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-6">
                                                                                <div class="form-group">
                                                                                    <label>No Induk</label>
                                                                                    <input type="number" class="form-control" value="<?= $data['no_induk'] ?>" name="no_induk" required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-6">
                                                                                <div class="form-group">
                                                                                    <label>RFID</label>
                                                                                    <input type="number" class="form-control" value="<?= $data['rfid'] ?>" name="rfid" required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-12">
                                                                                <div class="form-group">
                                                                                    <label>Nama Siswa</label>
                                                                                    <input type="text" class="form-control" value="<?= $data['nm_siswa'] ?>" name="nama" required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-6">
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
                                                                                    <label>Jenis Kelamin</label>
                                                                                    <select class="form-control" name="jk" required>
                                                                                        <option>Pilih</option>
                                                                                        <option value="1" <?php if($data['jk']==1) { ?> selected="" <?php } ?>>Laki-laki</option>
                                                                                        <option value="2" <?php if($data['jk']==2) { ?> selected="" <?php } ?>>Perempuan</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-6">
                                                                                <div class="form-group">
                                                                                    <label>Nomor HP</label>
                                                                                    <input type="number" value="<?= $data['hp'] ?>" class="form-control" name="hp" required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-6">
                                                                                <div class="form-group">
                                                                                    <label>Status Siswa</label>
                                                                                    <select class="form-control" name="sts" required>
                                                                                        <option>Pilih</option>
                                                                                        <option value="1" <?php if($data['sts_siswa']==1) { ?> selected="" <?php } ?>>Aktif</option>
                                                                                        <option value="2" <?php if($data['sts_siswa']==2) { ?> selected="" <?php } ?>>Tidak Aktif</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-12">
                                                                                    <div class="form-group">
                                                                                        <div class="custom-file">
                                                                                            <input type="file" name="file" class="custom-file-input">
                                                                                            <label class="custom-file-label">Pilih Foto (kosongi jika tidak ada perubahan)...</label>
                                                                                        </div>
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
                                                                    <form method="post" action="<?= base_url('Siswa/updatepassword'); ?>">
                                                                        <div class="modal-body">
                                                                            <div class="row">
                                                                                
                                                                                <div class="col-sm-12">
                                                                                    <div class="form-group">
                                                                                        <label>Nama</label>
                                                                                        <input type="text" class="form-control" readonly value="<?=$data['nm_siswa']?>" required>
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
                                                                    <form method="post" action="<?= base_url('Siswa/hapus'); ?>">
                                                                        <div class="modal-body">
                                                                            Apakah anda yakin ingin menghapus Data user <?=$data['nm_siswa'];?>? <br>
                                                                            Proses ini akan sekaligus menghapus data rombel bersangkutan
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
                                                            <td>
                                                                <?php if(empty($data['file'])){?>
                                                                    <img class="img-thumbnail" src="<?=base_url()?>/image/siswa/noimage.png" width="50">
                                                                <?php }else{ ?>
                                                                    <img class="img-thumbnail" src="<?=base_url()?>/image/siswa/<?=$data['file'];?>" width="50">
                                                                <?php } ?>
                                                            </td> 
                                                            <td>
                                                                <a href="<?= base_url('Siswa/printkartu/'.$id); ?>" target="_blank">
                                                                    <button class="btn btn-info btn-sm" type="submit"><i class="feather icon-printer"></i></button>
                                                                </a>
                                                            </td>
                                                            <td><?= $data['nisn'] ?></td>
                                                            <td><?= $data['no_induk'] ?></td>
                                                            <td><?= $data['rfid'] ?></td>
                                                            <td><?= $data['nm_siswa'] ?></td>
                                                            <td><?= $data['alamat'] ?></td>
                                                            <td><?= $data['tempat_lahir'] ?>, <?= formatTanggal($data['tgl_lahir']) ?></td>
                                                            <td><?= jk($data['jk']) ?></td>
                                                            <td><?= $data['hp'] ?></td>
                                                            <td>
                                                                <?php if($data['sts_siswa']==1){ ?>
                                                                    <span class="badge badge-pill badge-success"><?= stsptk($data['sts_siswa']) ?></span>
                                                                <?php }else{ ?>
                                                                    <span class="badge badge-pill badge-danger"><?= stsptk($data['sts_siswa']) ?></span>
                                                                <?php } ?>
                                                            </td>
                                                            
                                                        </tr>

                                                               

                                                                
                                                       <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- [ static-layout ] end -->
                            </div>

                            <script>
                            // Select All checkbox
                            document.getElementById('selectAll').addEventListener('change', function() {
                                var checkboxes = document.querySelectorAll('.row-select');
                                checkboxes.forEach(function(cb) { cb.checked = this.checked; }.bind(this));
                                updateBulkButton();
                            });
                            // Individual checkboxes
                            document.addEventListener('change', function(e) {
                                if (e.target.classList.contains('row-select')) updateBulkButton();
                            });
                            function updateBulkButton() {
                                var checked = document.querySelectorAll('.row-select:checked').length;
                                document.getElementById('selectedCount').textContent = checked;
                                document.getElementById('btnBulkDelete').style.display = checked > 0 ? 'inline-block' : 'none';
                            }
                            </script>
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
<h4 class="modal-title">Tambah Data Siswa</h4>
<button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
                
<!-- Modal body -->
<form class="was-validated" method="post" action="<?= base_url('Siswa/add'); ?>" enctype="multipart/form-data">
    <div class="modal-body">
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Nisn</label>
                    <input type="number" class="form-control" name="nisn" required>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>No Induk</label>
                    <input type="number" class="form-control" name="no_induk" required>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>RFID</label>
                    <input type="number" class="form-control" name="rfid" required>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Nama Siswa</label>
                    <input type="text" class="form-control" name="nama" required>
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
                    <input type="number" class="form-control" name="hp">
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
                    <label>Status Siswa</label>
                    <select class="form-control" name="sts" required>
                        <option>Pilih</option>
                        <option value="1">Aktif</option>
                        <option value="2">Tidak Aktif</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <div class="custom-file">
                        <input type="file" name="file" class="custom-file-input" required>
                        <label class="custom-file-label">Pilih Foto...</label>
                    </div>
                </div>
            </div>
        </div>
         
        <button type="submit" class="btn btn-danger">Save</button>
    </div>
</form>

</div>
</div>
</div>