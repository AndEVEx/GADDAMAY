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
                                            <h5>Entri Libur</h5>
                                        </div>

                                        <form method="post" action="<?= base_url('Jadwalkhusus/add'); ?>">
                                        <div class="card-body table-border-style">

                                            <div class="row">
                                                <div class="col-6">
                                                    <label>Pilih Guru <span class="text-danger">*</span></label>
                                                    <select class="form-control" name="id_ptk" id="single-select-field" required>
                                                        <option value="" disabled selected>-- Pilih Guru --</option>
                                                        <option value="All">All</option>
                                                        <?php foreach ($getPegawai as $data) { ?>
                                                        <option value="<?= $data['id_ptk'] ?>"><?= $data['nama_ptk'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <br>
                                            <div class="row">
                                                <div class="col-12">
                                                    <h6>Pilih Shift Setiap Hari <span class="text-danger">*</span></h6>
                                                </div>
                                            </div>

                                            <div class="table-responsive mt-3">
                                                <table class="table table-striped table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Senin</th><th>Selasa</th><th>Rabu</th><th>Kamis</th><th>Jumat</th><th>Sabtu</th><th>Minggu</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                        <?php for ($i = 1; $i <= 7; $i++) { ?>
                                                            <td>
                                                                <select class="form-control" name="id_shift<?= $i ?>" required>
                                                                    <option value="" disabled selected>-- Pilih --</option>
                                                                    <option value="99">Libur</option>
                                                                    <?php foreach ($getShift as $data) { ?>
                                                                    <option value="<?= $data['id_shift'] ?>"><?= $data['nm_shift'] ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </td>
                                                        <?php } ?>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <br>
                                            <button type="submit" class="btn btn-danger">Save</button>

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
                                                <h5>Tabel Data Jadwla Khusus Guru</h5>
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
                                                        foreach ($getJadwalkhusus as $data) {  
                                                            $id = $data['id_jadwal'];
                                                        $no++;
                                                        ?>
                                                        <tr>
                                                            <td><?= $no ?></td>
                                                            <td><?= $data['nama_ptk'] ?></td>
                                                            <td><?= $data['nama_jenis_ptk'] ?></td>
                                                            <td>
                                                                <?php if($data['Senin']==99 || $data['Senin']==0){ ?>
                                                                    <span class="badge badge-pill badge-danger">Libur</span>
                                                                <?php }else{ ?>
                                                                    <span class="badge badge-pill badge-success"><?= nmshift($data['Senin']) ?></span>
                                                                <?php } ?>
                                                            </td>
                                                            <td>
                                                                <?php if($data['Selasa']==99 || $data['Selasa']==0){ ?>
                                                                    <span class="badge badge-pill badge-danger">Libur</span>
                                                                <?php }else{ ?>
                                                                    <span class="badge badge-pill badge-success"><?= nmshift($data['Selasa']) ?></span>
                                                                <?php } ?>
                                                            </td>
                                                            <td>
                                                                <?php if($data['Rabu']==99 || $data['Rabu']==0){ ?>
                                                                    <span class="badge badge-pill badge-danger">Libur</span>
                                                                <?php }else{ ?>
                                                                    <span class="badge badge-pill badge-success"><?= nmshift($data['Rabu']) ?></span>
                                                                <?php } ?>
                                                            </td>
                                                            <td>
                                                                <?php if($data['Kamis']==99 || $data['Kamis']==0){ ?>
                                                                    <span class="badge badge-pill badge-danger">Libur</span>
                                                                <?php }else{ ?>
                                                                    <span class="badge badge-pill badge-success"><?= nmshift($data['Kamis']) ?></span>
                                                                <?php } ?>
                                                            </td>
                                                            <td>
                                                                <?php if($data['Jumat']==99 || $data['Jumat']==0){ ?>
                                                                    <span class="badge badge-pill badge-danger">Libur</span>
                                                                <?php }else{ ?>
                                                                    <span class="badge badge-pill badge-success"><?= nmshift($data['Jumat']) ?></span>
                                                                <?php } ?>
                                                            </td>
                                                            <td>
                                                                <?php if($data['Sabtu']==99 || $data['Sabtu']==0){ ?>
                                                                    <span class="badge badge-pill badge-danger">Libur</span>
                                                                <?php }else{ ?>
                                                                    <span class="badge badge-pill badge-success"><?= nmshift($data['Sabtu']) ?></span>
                                                                <?php } ?>
                                                            </td>
                                                            <td>
                                                                <?php if($data['Minggu']==99 || $data['Minggu']==0){ ?>
                                                                    <span class="badge badge-pill badge-danger">Libur</span>
                                                                <?php }else{ ?>
                                                                    <span class="badge badge-pill badge-success"><?= nmshift($data['Minggu']) ?></span>
                                                                <?php } ?>
                                                            </td>
                                                            <td>
                                                                <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#edit<?=$id;?>"><i class="feather icon-edit-2"></i></button>
                                                                <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#delete<?=$id;?>"><i class="feather icon-trash"></i></button>
                                                            
                                                                <!-- Edit The Modal -->
                                                                <div class="modal fade" id="edit<?=$id;?>">
                                                                    <div class="modal-dialog modal-xl">
                                                                    <div class="modal-content">
                                                                                
                                                                    <!-- Modal Header -->
                                                                    <div class="modal-header">
                                                                    <h4 class="modal-title">Edit Data</h4>
                                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                    </div>
                                                                                    
                                                                    <!-- Modal body -->
                                                                    <form method="post" action="<?= base_url('Jadwalkhusus/update'); ?>">
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
                                                                                
                                                                                <div class="col-12">
                                                                                    <div class="table-responsive">
                                                                                        <table class="table table-striped table-hover">
                                                                                            <thead>
                                                                                                <tr>
                                                                                                    <th>Senin</th>
                                                                                                    <th>Selasa</th>
                                                                                                    <th>Rabu</th>
                                                                                                    <th>Kamis</th>
                                                                                                    <th>Jumat</th>
                                                                                                    <th>Sabtu</th>
                                                                                                    <th>Minggu</th>
                                                                                                </tr>
                                                                                            </thead>
                                                                                            <tbody>
                                                                                                <tr>
                                                                                                    <td>
                                                                                                        <select class="form-control" name="id_shift1">
                                                                                                            <option>Pilih</option>
                                                                                                            <option <?php if($data['Senin']==99 || $data['Senin']==0){ ?> selected="" <?php } ?> value="99">Libur</option>
                                                                                                            <?php foreach ($getShift as $datashift) { ?>
                                                                                                            <option <?php if($datashift['id_shift']==$data['Senin']){ ?> selected="" <?php } ?> value="<?=$datashift['id_shift'];?>"><?=$datashift['nm_shift'];?></option>
                                                                                                            <?php } ?>
                                                                                                        </select>
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        <select class="form-control" name="id_shift2">
                                                                                                            <option>Pilih</option>
                                                                                                            <option <?php if($data['Selasa']==99 || $data['Selasa']==0){ ?> selected="" <?php } ?> value="99">Libur</option>
                                                                                                            <?php foreach ($getShift as $datashift) { ?>
                                                                                                            <option <?php if($datashift['id_shift']==$data['Selasa']){ ?> selected="" <?php } ?> value="<?=$datashift['id_shift'];?>"><?=$datashift['nm_shift'];?></option>
                                                                                                            <?php } ?>
                                                                                                        </select>
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        <select class="form-control" name="id_shift3">
                                                                                                            <option>Pilih</option>
                                                                                                            <option <?php if($data['Rabu']==99 || $data['Rabu']==0){ ?> selected="" <?php } ?> value="99">Libur</option>
                                                                                                            <?php foreach ($getShift as $datashift) { ?>
                                                                                                            <option <?php if($datashift['id_shift']==$data['Rabu']){ ?> selected="" <?php } ?> value="<?=$datashift['id_shift'];?>"><?=$datashift['nm_shift'];?></option>
                                                                                                            <?php } ?>
                                                                                                        </select>
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        <select class="form-control" name="id_shift4">
                                                                                                            <option>Pilih</option>
                                                                                                            <option <?php if($data['Kamis']==99 || $data['Kamis']==0){ ?> selected="" <?php } ?> value="99">Libur</option>
                                                                                                            <?php foreach ($getShift as $datashift) { ?>
                                                                                                            <option <?php if($datashift['id_shift']==$data['Kamis']){ ?> selected="" <?php } ?> value="<?=$datashift['id_shift'];?>"><?=$datashift['nm_shift'];?></option>
                                                                                                            <?php } ?>
                                                                                                        </select>
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        <select class="form-control" name="id_shift5">
                                                                                                            <option>Pilih</option>
                                                                                                            <option <?php if($data['Jumat']==99 || $data['Kamis']==0){ ?> selected="" <?php } ?> value="99">Libur</option>
                                                                                                            <?php foreach ($getShift as $datashift) { ?>
                                                                                                            <option <?php if($datashift['id_shift']==$data['Jumat']){ ?> selected="" <?php } ?> value="<?=$datashift['id_shift'];?>"><?=$datashift['nm_shift'];?></option>
                                                                                                            <?php } ?>
                                                                                                        </select>
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        <select class="form-control" name="id_shift6">
                                                                                                            <option>Pilih</option>
                                                                                                            <option <?php if($data['Sabtu']==99 || $data['Sabtu']==0){ ?> selected="" <?php } ?> value="99">Libur</option>
                                                                                                            <?php foreach ($getShift as $datashift) { ?>
                                                                                                            <option <?php if($datashift['id_shift']==$data['Sabtu']){ ?> selected="" <?php } ?> value="<?=$datashift['id_shift'];?>"><?=$datashift['nm_shift'];?></option>
                                                                                                            <?php } ?>
                                                                                                        </select>
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        <select class="form-control" name="id_shift7">
                                                                                                            <option>Pilih</option>
                                                                                                            <option <?php if($data['Minggu']==99 || $data['Minggu']==0){ ?> selected="" <?php } ?> value="99">Libur</option>
                                                                                                            <?php foreach ($getShift as $datashift) { ?>
                                                                                                            <option <?php if($datashift['id_shift']==$data['Minggu']){ ?> selected="" <?php } ?> value="<?=$datashift['id_shift'];?>"><?=$datashift['nm_shift'];?></option>
                                                                                                            <?php } ?>
                                                                                                        </select>
                                                                                                    </td>
                                                                                                </tr>    
                                                                                        
                                                                                            </tbody>
                                                                                        </table>
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
                                                                    <form method="post" action="<?= base_url('Jadwalkhusus/hapus'); ?>">
                                                                        <div class="modal-body">
                                                                            Apakah anda yakin ingin menghapus Jadwal Khusus <?=$data['nama_ptk'];?>?
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
