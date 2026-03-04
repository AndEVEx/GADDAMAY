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
                                                <li class="breadcrumb-item"><a href="#!"> Referensi</a></li>
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
                                                <h5>Tabel Rombel</h5>
                                                </div>
                                                <div class="col-2">
                                                    <button class="btn btn-primary btn-sm" type="submit" data-toggle="modal" data-target="#add"><i class="feather icon-plus"></i> Tambah</button>
                                                </div>
                                                
                                            </div>
                                            
                                      
                                        </div>
                                        <div class="card-body table-border-style">
                                            <div class="table-responsive">
                                                <table class="table table-striped table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Kode</th>
                                                            <th>Tingkat Kelas</th>
                                                            <th>Nama Rombel</th>
                                                            <th>Wali Kelas</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php 
                                                        $no=0;
                                                        foreach ($getRombel as $data) {  
                                                            $id = $data['id_rombel'];
                                                        $no++;
                                                        ?>
                                                        <tr>
                                                            <td><?= $no ?></td>
                                                            <td><?= $data['id_rombel'] ?></td>
                                                            <td><?= $data['nm_tingkat_kelas'] ?></td>
                                                            <td><?= $data['nm_rombel'] ?></td>
                                                            <td><?= $data['nama_ptk'] ?></td>
                                                            <td>
                                                                <button class="btn btn-warning btn-sm" type="submit" data-toggle="modal" data-target="#edit<?=$id;?>"><i class="feather icon-edit-2"></i></button>
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
                                                                    <form method="post" action="<?= base_url('Rombel/update'); ?>">
                                                                        <div class="modal-body">
                                                                            <div class="row">
                                                                               
                                                                                <div class="col-sm-12">
                                                                                    <div class="form-group">
                                                                                        <label>Nama Rombel</label>
                                                                                        <input type="text" class="form-control" name="nama" value="<?=$data['nm_rombel']?>" required>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-12">
                                                                                    <div class="form-group">
                                                                                        <label>Wali Kelas</label>
                                                                                        <select class="form-control" name="id_ptk" required>
                                                                                            <option selected disabled value="">Pilih</option>
                                                                                            <?php foreach ($getGuru as $data_ptk) { ?>
                                                                                            <option value="<?=$data_ptk['id_ptk'] ?>" <?php if($data_ptk['id_ptk']==$data['id_walikelas']){ ?> selected="" <?php } ?>><?=$data_ptk['nama_ptk'] ?></option>
                                                                                            <?php } ?>
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
                                                                    <form method="post" action="<?= base_url('Rombel/hapus'); ?>">
                                                                        <div class="modal-body">
                                                                            Apakah anda yakin ingin menghapus Data <?=$data['nm_rombel'];?>?
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
<h4 class="modal-title">Tambah Data</h4>
<button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
                
<!-- Modal body -->
<form class="was-validated" method="post" action="<?= base_url('Rombel/add'); ?>">
    <div class="modal-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Nama Rombel</label>
                    <input type="text" class="form-control" name="nama" required>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Tingkat Kelas</label>
                    <select class="form-control" name="tingkat" required>
                        <option selected disabled value="">Pilih</option>
                        <?php foreach ($getTingkat as $data) { ?>
                        <option value="<?=$data['id_tingkat_kelas'] ?>"><?=$data['nm_tingkat_kelas'] ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Wali Kelas</label>
                    <select class="form-control" name="id_ptk" required>
                        <option selected disabled value="">Pilih</option>
                        <?php foreach ($getGuru as $data) { ?>
                        <option value="<?=$data['id_ptk'] ?>"><?=$data['nama_ptk'] ?></option>
                        <?php } ?>
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