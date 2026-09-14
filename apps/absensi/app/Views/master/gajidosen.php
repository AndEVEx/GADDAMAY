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
                                                <li class="breadcrumb-item"><a href="#!"> Penggajian</a></li>
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
                                        <form method="post" action="<?= base_url('Gajidosen'); ?>">
                                        <div class="card-body table-border-style">
                                            <div class="row">
                                                
                                                <div class="col-4">
                                                    <select class="form-control" name="jenis" required>
                                                        <option>Pilih Jenis Ketenagaan</option>
                                                        <option value="All">All</option>
                                                        <?php foreach ($getJenis as $data) { ?>
                                                        <option value="<?=$data['id_jenis_ptk'] ?>"><?=$data['nama_jenis_ptk'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
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
                                                <div class="col-8">
                                                <h5>Tabel Data Gaji <?=jnskerja($Jenis);?>, <?=bulan($getBulan);?> <?=$getTahun;?></h5>
                                                </div>
                                                <div class="col-4">
                                               
                                                <button class="btn btn-success btn-sm" type="submit" data-toggle="modal" data-target="#approve"><i class="feather icon-user-check"></i> APPROVE</button>
                                                    <?php 
                                                     $db = \Config\Database::connect();
                                                     $bln = date('m');
                                                     $thn = date('Y');
                                                     $builder = $db->table('gaji');
                                                     $builder->where('bln', $bln);
                                                     $builder->where('thn', $thn);
                                                     $all =  $builder->countAllResults();
                                                     if($all>0){
                                                    ?>
                                                        <button class="btn btn-primary btn-sm" type="submit" disabled><i class="feather icon-plus"></i> Copy <?=bulan($getBulan-1);?></button>
                                                    <?php }else{ ?>
                                                        <button class="btn btn-primary btn-sm" type="submit" data-toggle="modal" data-target="#add"><i class="feather icon-plus"></i> Copy <?=bulan($getBulan-1);?></button>
                                                    <?php } ?>
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
                                                            <th>NIP</th>
                                                            <th>Nama Pegawai</th>
                                                            <th>Jenis Kerja</th>
                                                            <th>Total Gaji</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>
                                         
                                                    <tbody>
                                                        <?php 
                                                        $no=0;
                                                        foreach ($getPegawai as $data) {  
                                                        $id = $data['id_ptk'];
                                                        $no++;
                                                        $tot_gaji =gaji($id,$getBulan,$getTahun);
                                                        ?>
                                                        <tr>
                                                            <td><?= $no ?></td>
                                                            <td><?= $data['nip'] ?></td>
                                                            <td><?= $data['nama_ptk'] ?></td>
                                                            <td><?= $data['nama_jenis_ptk'] ?></td>
                                                            <td><?= rupiah($tot_gaji);?></td>
                                                            <td>
                                                                <?php 
                                                                //cek status approve gaji
                                                                $builder_approve = $db->table('gaji_approve');
                                                                $builder_approve->where('id_ptk', $id);
                                                                $builder_approve->where('bln', $getBulan);
                                                                $builder_approve->where('thn', $getTahun);
                                                                $all_approve =  $builder_approve->countAllResults();
                                                                if($all_approve>0){
                                                                ?>
                                                                    <button class="btn btn-success btn-sm" type="submit" disabled><i class="feather icon-plus"></i></button>
                                                                    <button class="btn btn-info btn-sm" type="submit" disabled><i class="feather icon-eye"></i></button>
                                                                    <button class="btn btn-warning btn-sm" type="submit" disabled><i class="feather icon-edit-2"></i></button>
                                                                    <button class="btn btn-danger btn-sm" type="submit" disabled><i class="feather icon-trash"></i></button>
                                                                <?php
                                                                }else{
                                                                    if($tot_gaji>0){?>
                                                                        <button class="btn btn-success btn-sm" type="submit" disabled><i class="feather icon-plus"></i></button>
                                                                        <button class="btn btn-info btn-sm" type="submit" data-toggle="modal" data-target="#detail<?=$id;?>"><i class="feather icon-eye"></i></button>
                                                                        <button class="btn btn-warning btn-sm" type="submit" data-toggle="modal" data-target="#edit<?=$id;?>"><i class="feather icon-edit-2"></i></button>
                                                                        <button class="btn btn-danger btn-sm" type="submit" data-toggle="modal" data-target="#delete<?=$id;?>"><i class="feather icon-trash"></i></button>
                                                                    <?php }else{?>
                                                                        <button class="btn btn-success btn-sm" type="submit" data-toggle="modal" data-target="#add<?=$id;?>"><i class="feather icon-plus"></i></button>
                                                                        <button class="btn btn-info btn-sm" type="submit" disabled><i class="feather icon-eye"></i></button>
                                                                        <button class="btn btn-warning btn-sm" type="submit" disabled><i class="feather icon-edit-2"></i></button>
                                                                        <button class="btn btn-danger btn-sm" type="submit" disabled><i class="feather icon-trash"></i></button>
                                                                    <?php 
                                                                    } 
                                                                }?>
                                                                

                                                                <!-- add The Modal -->
                                                                <div class="modal fade" id="add<?=$id;?>">
                                                                    <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                                
                                                                    <!-- Modal Header -->
                                                                    <div class="modal-header">
                                                                    <h4 class="modal-title">Tambah Data Penggajian</h4>
                                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                    </div>
                                                                                    
                                                                    <!-- Modal body -->
                                                                    <form method="post" action="<?= base_url('Gajidosen/add'); ?>">
                                                                        <div class="modal-body">
                                                                            <div class="row">
                                                                                <?php foreach ($getKatgaji as $data1) { ?>
                                                                                    <div class="col-12">
                                                                                        <div class="form-group row">
                                                                                            <label for="inputEmail3" class="col-sm-5 col-form-label"><?= $data1['nm_kat_gaji'] ?></label>
                                                                                            <div class="col-sm-7">
                                                                                                <input type="number" class="form-control" name="nominal[]">
                                                                                                <input type="hidden" class="form-control" name="id_kat[]" value="<?= $data1['id_kat_gaji'] ?>">   
                                                                                            </div>
                                                                                        </div>
                                                                            
                                                                                    </div>
                                                                                <?php } ?>
                                                                               
                                                                            </div>
                                                                            <input type="hidden" class="form-control" name="id" value="<?= $id ?>" required>   
                                                                            <input type="hidden" class="form-control" name="bln" value="<?= $getBulan ?>" required>
                                                                            <input type="hidden" class="form-control" name="thn" value="<?= $getTahun ?>" required>
                                                                            <input type="hidden" class="form-control" name="jenis" value="<?= $Jenis ?>" required>
                                                                            
                                                                            <button type="submit" class="btn btn-danger">Save</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                                </div>
                                                                </div>

                                                                <!-- edit The Modal -->
                                                                <div class="modal fade" id="edit<?=$id;?>">
                                                                    <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                                
                                                                    <!-- Modal Header -->
                                                                    <div class="modal-header">
                                                                    <h4 class="modal-title">Edit Data Penggajian</h4>
                                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                    </div>
                                                                                    
                                                                    <!-- Modal body -->
                                                                    <form method="post" action="<?= base_url('Gajidosen/update'); ?>">
                                                                        <div class="modal-body">
                                                                            <div class="row">
                                                                                <?php foreach ($getKatgaji as $data1) { 
                                                                                $id_kat = $data1['id_kat_gaji'];    
                                                                                ?>
                                                                                    <div class="col-12">
                                                                                        <div class="form-group row">
                                                                                            <label for="inputEmail3" class="col-sm-5 col-form-label"><?= $data1['nm_kat_gaji'] ?></label>
                                                                                            <div class="col-sm-7">
                                                                                                <input type="number" class="form-control" name="nominal[]" value="<?= gajidetail($id,$getBulan,$getTahun,$id_kat);?>">
                                                                                                <input type="hidden" class="form-control" name="id_kat[]" value="<?= $data1['id_kat_gaji'] ?>">   
                                                                                            </div>
                                                                                        </div>
                                                                            
                                                                                    </div>
                                                                                <?php } ?>
                                                                               
                                                                            </div>
                                                                            <input type="hidden" class="form-control" name="id" value="<?= $id ?>" required>   
                                                                            <input type="hidden" class="form-control" name="bln" value="<?= $getBulan ?>" required>
                                                                            <input type="hidden" class="form-control" name="thn" value="<?= $getTahun ?>" required>
                                                                            <input type="hidden" class="form-control" name="jenis" value="<?= $Jenis ?>" required>
                                                                            
                                                                            <button type="submit" class="btn btn-danger">Save</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                                </div>
                                                                </div>

                                                                <!-- detail The Modal -->
                                                                <div class="modal fade" id="detail<?=$id;?>">
                                                                    <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                                
                                                                    <!-- Modal Header -->
                                                                    <div class="modal-header">
                                                                    <h4 class="modal-title">Detail Data Penggajian</h4>
                                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                    </div>
                                                                                    
                                                                    <!-- Modal body -->
                                                                   
                                                                        <div class="modal-body">
                                                                            <div class="row">
                                                                                <?php foreach ($getKatgaji as $data2) { 
                                                                                $id_kat = $data2['id_kat_gaji'];
                                                                                ?>
                                                                                    <div class="col-12">
                                                                                        <div class="form-group row">
                                                                                            <label for="inputEmail3" class="col-sm-5 col-form-label"><?= $data2['nm_kat_gaji'] ?></label>
                                                                                            <div class="col-sm-7">
                                                                                                <input type="text" class="form-control" value="<?= rupiah(gajidetail($id,$getBulan,$getTahun,$id_kat));?>">
                                                                                            </div>
                                                                                        </div>
                                                                            
                                                                                    </div>
                                                                                <?php } ?>
                                                                               
                                                                            </div>
                                                                           
                                                                        </div>
                                                                   
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
                                                                    <form method="post" action="<?= base_url('Gajidosen/hapus'); ?>">
                                                                        <div class="modal-body">
                                                                            Apakah anda yakin ingin menghapus Data Gaji <?= $data['nama_ptk'] ?>?
                                                                            <input type="hidden" name="id" value="<?=$id;?>">
                                                                            <input type="hidden" name="bln" value="<?=$getBulan;?>">
                                                                            <input type="hidden" name="thn" value="<?=$getTahun;?>">
                                                                            <input type="hidden" name="jenis" value="<?= $Jenis ?>" required>
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

<!-- copy The Modal -->
<div class="modal fade" id="add">
<div class="modal-dialog">
<div class="modal-content">
            
<!-- Modal Header -->
<div class="modal-header">
<h4 class="modal-title">Copy Data Gaji</h4>
<button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
                
<!-- Modal body -->
<form class="was-validated" method="post" action="<?= base_url('Gajidosen/copy'); ?>">
    <div class="modal-body">
        Apakah anda yakin ingin mengcopy Data Gaji <?=bulan($getBulan-1);?>?<br>
        (data akan diambil dari data keseluruhan jenis ketenagaan)
        <br>
        <br>
        <input type="hidden" name="bln" value="<?=$getBulan;?>">
        <input type="hidden" name="thn" value="<?=$getTahun;?>">
        <input type="hidden" name="jenis" value="<?= $Jenis ?>" required>
        <button type="submit" class="btn btn-danger">Proses</button>
    </div>
</form>

</div>
</div>
</div>

<!-- approve The Modal -->
<div class="modal fade" id="approve">
<div class="modal-dialog">
<div class="modal-content">
            
<!-- Modal Header -->
<div class="modal-header">
<h4 class="modal-title">Approve Data Gaji</h4>
<button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
                
<!-- Modal body -->
<form method="post" action="<?= base_url('Gajidosen/approve'); ?>">
    <div class="modal-body">
        Apakah anda yakin ingin melakukan APPROVE gaji bulan <?=bulan($getBulan) ?>?<br>
        (data yang tampil akan di approve semua. dan tombol aksi akan terkunci)
        <br>
        <br>
        <?php 
        foreach ($getPegawai as $data) {  
        $id = $data['id_ptk'];
        ?>
            <input type="hidden" name="approve[]" value="<?=$id;?>">
        <?php
        }
        ?>
        <input type="hidden" name="bln" value="<?=$getBulan;?>">
        <input type="hidden" name="thn" value="<?=$getTahun;?>">
        <input type="hidden" name="jenis" value="<?= $Jenis ?>" required>
        <button type="submit" class="btn btn-danger">Proses</button>
    </div>
</form>

</div>
</div>
</div>