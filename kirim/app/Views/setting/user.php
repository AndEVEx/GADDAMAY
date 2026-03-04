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
                                                <li class="breadcrumb-item"><a href="#!"> Setting</a></li>
                                                <li class="breadcrumb-item"><a href="<?= base_url('User'); ?>"><?=$nav;?></a></li>
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
                                                <h5>Tabel User</h5>
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
                                                <table class="table table-striped table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Nama</th>
                                                            <th>Username</th>
                                                            <th>Level</th>
                                                            <th>Setting</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php 
                                                        $no=0;
                                                        foreach ($getUser as $data) {  
                                                            $id = $data['id_user'];
                                                        $no++;
                                                        ?>
                                                        <tr>
                                                            <td><?= $no ?></td>
                                                            <td><?= $data['nama'] ?></td>
                                                            <td><?= $data['username'] ?></td>
                                                            <td><?= level($data['level']) ?></td>
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
                                                                    <h4 class="modal-title">Edit Data User</h4>
                                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                    </div>
                                                                                    
                                                                    <!-- Modal body -->
                                                                    <form method="post" action="<?= base_url('User/update'); ?>" enctype="multipart/form-data">
                                                                        <div class="modal-body">
                                                                            <div class="row">
                                                                                <div class="col-sm-12">
                                                                                    <div class="form-group">
                                                                                        <label>Username</label>
                                                                                        <input type="text" class="form-control" name="username" value="<?=$data['username']?>">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-12">
                                                                                    <div class="form-group">
                                                                                        <label>Nama</label>
                                                                                        <input type="text" class="form-control" name="nama" value="<?=$data['nama']?>" required>
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
                                                                    <form method="post" action="<?= base_url('User/updatepassword'); ?>">
                                                                        <div class="modal-body">
                                                                            <div class="row">
                                                                                
                                                                                <div class="col-sm-12">
                                                                                    <div class="form-group">
                                                                                        <label>Nama</label>
                                                                                        <input type="text" class="form-control" readonly value="<?=$data['nama']?>" required>
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
                                                                    <form method="post" action="<?= base_url('User/hapus'); ?>">
                                                                        <div class="modal-body">
                                                                            Apakah anda yakin ingin menghapus Data user <?=$data['nama'];?>?
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
<h4 class="modal-title">Tambah Data User</h4>
<button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
                
<!-- Modal body -->
<form class="was-validated" method="post" action="<?= base_url('User/add'); ?>" enctype="multipart/form-data">
    <div class="modal-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" class="form-control" name="nama" required>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" class="form-control" name="username" required>
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