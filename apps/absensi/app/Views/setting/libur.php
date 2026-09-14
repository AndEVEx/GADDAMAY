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
                                                <div class="col-12">
                                                <h5>Entri Libur</h5>
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
                                        <form method="post" action="<?= base_url('Libur/add'); ?>">
                                        <div class="card-body table-border-style">
                                            <div class="row"> 
                                                <div class="col-3">
                                                    <label>Pilih Guru</label>
                                                    <select class="form-control" name="id_ptk" id="single-select-field"  required>
                                                        <option>Pilih</option>
                                                        <option value="All">All</option>
                                                        <?php foreach ($getPegawai as $data) { ?>
                                                        <option value="<?=$data['id_ptk'] ?>"><?=$data['nama_ptk'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div> 
                                            <br><br>
                                            <div class="row">
                                                <div class="col-12">
                                                <h6>Checklist Hari libur</h6>
                                                </div>
                                                
                                            </div>
                                            <br>
                                            <div class="row">
                                                <div class="col-sm">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="customCheck1" name="senin" value="1">
                                                        <label class="custom-control-label" for="customCheck1">Senin</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="customCheck2" name="selasa" value="1">
                                                        <label class="custom-control-label" for="customCheck2">Selasa</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="customCheck3" name="rabu" value="1">
                                                        <label class="custom-control-label" for="customCheck3">Rabu</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="customCheck4" name="kamis" value="1">
                                                        <label class="custom-control-label" for="customCheck4">Kamis</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="customCheck5" name="jumat" value="1">
                                                        <label class="custom-control-label" for="customCheck5">Jumat</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="customCheck6" name="sabtu" value="1">
                                                        <label class="custom-control-label" for="customCheck6">Sabtu</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="customCheck7" name="minggu" value="1">
                                                        <label class="custom-control-label" for="customCheck7">Minggu</label>
                                                    </div>
                                                </div>
                                            </div>    
                                             <br>  
                                            <button type="submit" class="btn btn-danger">Save</button>
                                              
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
                                                <h5>Tabel Data Libur individu</h5>
                                                </div>
                                                
                                            </div>
                                            <?php if(session()->get('success1')) : ?>
                                                    <div class="alert alert-success alert-dismissible">
                                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                                    <center><h5><i class="icon fas fa-check"></i> Data sukses <strong><?= session()->getFlashdata('success1'); ?></strong></h5></center> 
                                                    </div>
                                                <?php endif; ?>

                                                <?php if(session()->get('error1')) : ?>
                                                    <div class="alert alert-danger alert-dismissible">
                                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                                    <center><h5><i class="icon fas fa-check"></i> Data gagal <strong><?= session()->getFlashdata('error1'); ?></strong></h5></center> 
                                                    </div>
                                                <?php endif; ?>
                                        </div>
                                        
                                        <div class="card-body table-border-style">
                                            <div class="table-responsive">
                                                <table id="example" class="table table-striped table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Nama Pegawai</th>
                                                            <th>Jenis Kerja</th>
                                                            <th>Senin</th>
                                                            <th>Selasa</th>
                                                            <th>Rabu</th>
                                                            <th>Kamis</th>
                                                            <th>Jumat</th>
                                                            <th>Sabtu</th>
                                                            <th>Minggu</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php 
                                                        $no=0;
                                                        foreach ($getLibur as $data) {  
                                                            $id = $data['id_libur'];
                                                        $no++;
                                                        ?>
                                                        <tr>
                                                            <td><?= $no ?></td>
                                                            <td><?= $data['nama_ptk'] ?></td>
                                                            <td><?= $data['nama_jenis_ptk'] ?></td>
                                                            <td>
                                                                <?php if($data['Senin']==1){ ?>
                                                                    <span class="badge badge-pill badge-danger">Libur</span>
                                                                <?php }else{ ?>
                                                                    <span class="badge badge-pill badge-success">Masuk</span>
                                                                <?php } ?>
                                                            </td>
                                                            <td>
                                                                <?php if($data['Selasa']==1){ ?>
                                                                    <span class="badge badge-pill badge-danger">Libur</span>
                                                                <?php }else{ ?>
                                                                    <span class="badge badge-pill badge-success">Masuk</span>
                                                                <?php } ?>
                                                            </td>
                                                            <td>
                                                                <?php if($data['Rabu']==1){ ?>
                                                                    <span class="badge badge-pill badge-danger">Libur</span>
                                                                <?php }else{ ?>
                                                                    <span class="badge badge-pill badge-success">Masuk</span>
                                                                <?php } ?>
                                                            </td>
                                                            <td>
                                                                <?php if($data['Kamis']==1){ ?>
                                                                    <span class="badge badge-pill badge-danger">Libur</span>
                                                                <?php }else{ ?>
                                                                    <span class="badge badge-pill badge-success">Masuk</span>
                                                                <?php } ?>
                                                            </td>
                                                            <td>
                                                                <?php if($data['Jumat']==1){ ?>
                                                                    <span class="badge badge-pill badge-danger">Libur</span>
                                                                <?php }else{ ?>
                                                                    <span class="badge badge-pill badge-success">Masuk</span>
                                                                <?php } ?>
                                                            </td>
                                                            <td>
                                                                <?php if($data['Sabtu']==1){ ?>
                                                                    <span class="badge badge-pill badge-danger">Libur</span>
                                                                <?php }else{ ?>
                                                                    <span class="badge badge-pill badge-success">Masuk</span>
                                                                <?php } ?>
                                                            </td>
                                                            <td>
                                                                <?php if($data['Minggu']==1){ ?>
                                                                    <span class="badge badge-pill badge-danger">Libur</span>
                                                                <?php }else{ ?>
                                                                    <span class="badge badge-pill badge-success">Masuk</span>
                                                                <?php } ?>
                                                            </td>
                                                            <td>
                                                                <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#edit<?=$id;?>"><i class="feather icon-edit-2"></i></button>
                                                                <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#delete<?=$id;?>"><i class="feather icon-trash"></i></button>
                                                            
                                                                <!-- Edit The Modal -->
                                                                <div class="modal fade" id="edit<?=$id;?>">
                                                                    <div class="modal-dialog modal-lg">
                                                                    <div class="modal-content">
                                                                                
                                                                    <!-- Modal Header -->
                                                                    <div class="modal-header">
                                                                    <h4 class="modal-title">Edit Data</h4>
                                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                    </div>
                                                                                    
                                                                    <!-- Modal body -->
                                                                    <form method="post" action="<?= base_url('Libur/update'); ?>">
                                                                        <div class="modal-body">
                                                                            <div class="row">
                                                                                <div class="col-sm-12">
                                                                                    <div class="form-group">
                                                                                        <label>Nama Pegawai</label>
                                                                                        <input type="text" class="form-control" name="nama" value="<?=$data['nama_ptk']?>" readonly>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col-sm">
                                                                                    <div class="custom-control custom-checkbox">
                                                                                        <input type="checkbox" class="custom-control-input" id="customCheck1<?=$id;?>" name="senin" value="1" <?php if($data['Senin']==1){ ?>checked <?php } ?>>
                                                                                        <label class="custom-control-label" for="customCheck1<?=$id;?>">Senin</label>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm">
                                                                                    <div class="custom-control custom-checkbox">
                                                                                        <input type="checkbox" class="custom-control-input" id="customCheck2<?=$id;?>" name="selasa" value="1" <?php if($data['Selasa']==1){ ?>checked <?php } ?>>
                                                                                        <label class="custom-control-label" for="customCheck2<?=$id;?>">Selasa</label>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm">
                                                                                    <div class="custom-control custom-checkbox">
                                                                                        <input type="checkbox" class="custom-control-input" id="customCheck3<?=$id;?>" name="rabu" value="1" <?php if($data['Rabu']==1){ ?>checked <?php } ?>>
                                                                                        <label class="custom-control-label" for="customCheck3<?=$id;?>">Rabu</label>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm">
                                                                                    <div class="custom-control custom-checkbox">
                                                                                        <input type="checkbox" class="custom-control-input" id="customCheck4<?=$id;?>" name="kamis" value="1" <?php if($data['Kamis']==1){ ?>checked <?php } ?>>
                                                                                        <label class="custom-control-label" for="customCheck4<?=$id;?>">Kamis</label>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm">
                                                                                    <div class="custom-control custom-checkbox">
                                                                                        <input type="checkbox" class="custom-control-input" id="customCheck5<?=$id;?>" name="jumat" value="1" <?php if($data['Jumat']==1){ ?>checked <?php } ?>>
                                                                                        <label class="custom-control-label" for="customCheck5<?=$id;?>">Jumat</label>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm">
                                                                                    <div class="custom-control custom-checkbox">
                                                                                        <input type="checkbox" class="custom-control-input" id="customCheck6<?=$id;?>" name="sabtu" value="1" <?php if($data['Sabtu']==1){ ?>checked <?php } ?>>
                                                                                        <label class="custom-control-label" for="customCheck6<?=$id;?>">Sabtu</label>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm">
                                                                                    <div class="custom-control custom-checkbox">
                                                                                        <input type="checkbox" class="custom-control-input" id="customCheck7<?=$id;?>" name="minggu" value="1" <?php if($data['Minggu']==1){ ?>checked <?php } ?>>
                                                                                        <label class="custom-control-label" for="customCheck7<?=$id;?>">Minggu</label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <br>
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
                                                                    <form method="post" action="<?= base_url('Libur/hapus'); ?>">
                                                                        <div class="modal-body">
                                                                            Apakah anda yakin ingin menghapus Data user <?=$data['nama_jenis_ptk'];?>?
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
