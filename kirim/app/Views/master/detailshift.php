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
                                                <li class="breadcrumb-item"><a href="<?= base_url('Shift'); ?>"> Shift</a></li>
                                                <li class="breadcrumb-item"><a href="<?= base_url($nav); ?>">Detail</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- [ breadcrumb ] end -->
                            
                           
                            <div class="row">
                                <!-- [ static-layout ] start -->
                                <div class="col-sm-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-10">
                                                <h5>Data Anggota <?= $getNama ?></h5>
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
                                            <form method="post" action="<?= base_url('Shift/updateanggota'); ?>">
                                                <table class="table table-striped table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Nip</th>
                                                            <th>Nomor Absensi</th>
                                                            <th>Nama Pegawai</th>
                                                            <th>Jenis Ketenagaan</th>
                                                            <th>Pindah</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php 
                                                        $no1=0;
                                                        foreach ($getAnggota as $data) {  
                                                            $id = $data['id_anggota_shift'];
                                                        $no1++;
                                                        ?>
                                                        <tr>
                                                            <td><?= $no1 ?></td>
                                                            <td><?= $data['nik'] ?></td>
                                                            <td><?= $data['nomor_absensi'] ?></td>
                                                            <td><?= $data['nama_ptk'] ?></td>
                                                            <td><?= $data['nama_jenis_ptk'] ?></td>
                                                            <td>
                                                                <select class="form-control" name="id_shift[]">
                                                                    <option>Pilih</option>
                                                                    <?php foreach ($getShift as $datashift) { ?>
                                                                    <option value="<?=$datashift['id_shift'];?>" <?php if($datashift['id_shift']==$data['id_shift']){?>selected="" <?php } ?>><?=$datashift['nm_shift'];?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <input type="hidden" class="form-control" name="id[]" value="<?=$id;?>" required>
                                                       <?php } ?>
                                                    </tbody>
                                                </table>
                                                
                                                <button type="submit" class="btn btn-danger">Save</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- [ static-layout ] end -->
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
