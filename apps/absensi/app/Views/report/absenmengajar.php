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
                                <div class="col-sm-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-10">
                                                <h5>Filter Bulan</h5>
                                                </div>
                                              
                                            </div>
                                      
                                        </div>
                                        <form method="post" action="<?= base_url('Absenmengajar/report'); ?>">
                                        <div class="card-body table-border-style">
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label>Pilih Bulan</label>
                                                            <select class="form-control" id="single-select-field" name="bln" required>
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
                                                <h5>Rekap Absensi Bulan <?=bulan($getBulan);?> </h5>
                                                </div>
                                              
                                            </div>
                                      
                                        </div>
                                       
                                        <div class="card-body table-border-style">
                                            <div class="container">
                                                <div class="row">
                                                <div class="card-body table-border-style">
                                                    <div class="table-responsive">
                                                        <table id="example" class="table table-striped table-hover">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Nip</th>
                                                                    <th>Nama Pegawai</th>
                                                                    <th>Kegiatan</th>
                                                                    <th>Catatan</th>
                                                                    <th>Tanggal</th>
                                                                    <th>Aksi</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php 
                                                                $no=0;
                                                                foreach ($getAbsenmengajar as $data) {  
                                                                $no++;
                                                                $id =$data['id_mengajar'];
                                                                ?>
                                                                <tr>
                                                                    <td><?= $no ?></td>
                                                                    <td><?= $data['nip'] ?></td>
                                                                    <td><?= $data['nama_ptk'] ?></td>
                                                                    <td><?= $data['nm_kegiatan'] ?></td>
                                                                    <td><?= $data['catatan'] ?></td>
                                                                    <td><?= formatTanggal($data['tgl_entri']) ?></td>
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
                                                                            <form method="post" action="<?= base_url('Absenmengajar/update'); ?>">
                                                                                <div class="modal-body">
                                                                                    <div class="row">
                                                                                    
                                                                                        <div class="col-sm-12">
                                                                                            <div class="form-group">
                                                                                                <label>Nama Kegiatan</label>
                                                                                                <input type="text" class="form-control" name="nama" value="<?=$data['nm_kegiatan']?>" required>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-sm-12">
                                                                                            <div class="form-group">
                                                                                                <label>Catatan</label>
                                                                                                <input type="text" class="form-control" name="catatan" value="<?=$data['catatan']?>" required>
                                                                                            </div>
                                                                                        </div>
                                                                                
                                                                                    </div>
                                                                                    <input type="hidden" name="bln" value="<?=$getBulan;?>">
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
                                                                            <form method="post" action="<?= base_url('Absenmengajar/hapus'); ?>">
                                                                                <div class="modal-body">
                                                                                    Apakah anda yakin ingin menghapus Data <?=$data['nm_kegiatan'];?>?
                                                                                    <input type="hidden" name="id" value="<?=$id;?>">
                                                                                    <input type="hidden" name="bln" value="<?=$getBulan;?>">
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
                                        </div>
                                        
                                    </div>
                                </div>
                           
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
