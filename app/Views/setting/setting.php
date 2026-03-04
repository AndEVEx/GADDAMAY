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
                                <div class="col-sm-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-10">
                                                <h5>Setting Aplikasi</h5>
                                                </div>
                                                
                                            </div>
                                            
                                       
                                        </div>
                                        <div class="card-body table-border-style">
                                        <form class="was-validated" method="post" action="<?= base_url('Setting/update'); ?>" enctype="multipart/form-data">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label>Nama Aplikasi</label>
                                                    <input type="text" class="form-control" value="<?=$getAplikasi;?>" name="nama" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Nama Sekolah</label>
                                                    <input type="text" class="form-control" value="<?=$getNamasekolah;?>" name="nm_sekolah" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Nama Kepela Sekolah</label>
                                                    <input type="text" class="form-control" value="<?=$getNamakepsek;?>" name="nm_kepsek" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Alamat</label>
                                                    <input type="text" class="form-control" value="<?=$getAlamat;?>" name="alamat" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Logo (Kosongi jika tidak ada perubahan)</label>
                                                    <div class="custom-file">
                                                        <input type="file" name="file" class="custom-file-input">
                                                        <label class="custom-file-label">Pilih Foto...</label>
                                                    </div>
                                                </div>
                                                <button type="submit" class="btn btn-danger">Save</button>
                                            </div>
                                            
                                        </form>
                                        </div>
                                    </div>
                                     
                                </div>
                                <!-- [ static-layout ] end -->
                                <div class="col-sm-8">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-10">
                                                <h5>Setting Jadwal Umum</h5>
                                                </div>
                                                
                                            </div>
                                            
                                        <?php if(session()->get('successhari')) : ?>
                                            <div class="alert alert-success alert-dismissible">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                            <center><h5><i class="icon fas fa-check"></i> Data sukses <strong><?= session()->getFlashdata('successhari'); ?></strong></h5></center> 
                                            </div>
                                        <?php endif; ?>

                                        <?php if(session()->get('errorhari')) : ?>
                                            <div class="alert alert-danger alert-dismissible">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                            <center><h5><i class="icon fas fa-check"></i> Data gagal <strong><?= session()->getFlashdata('errorhari'); ?></strong></h5></center> 
                                            </div>
                                        <?php endif; ?>
                                        </div>
                                        <div class="card-body table-border-style">
                                            <div class="table-responsive">
                                            <form method="post" action="<?= base_url('Setting/updatehari'); ?>">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Nama Hari</th>
                                                            <th>Masuk</th>
                                                            <th>Pulang</th>
                                                            <th>Status</th>
                                                            <th>Edit</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php 
                                                        $no=0;
                                                        foreach ($getHari as $data) {  
                                                            $id = $data['id_hari'];
                                                        $no++;
                                                        ?>
                                                        <tr>
                                                            <td><?=$no;?></td>
                                                            <td><?=$data['nm_hari'];?></td>
                                                            <td> <input type="time" class="form-control" value="<?=$data['jammasuk'];?>" name="jammasuk[]" ></td>
                                                            <td> <input type="time" class="form-control" value="<?=$data['jampulang'];?>" name="jampulang[]" ></td>
                                                            <td>
                                                                <?php if($data['sts_hari']==1){ ?>
                                                                    <span class="badge badge-pill badge-success"><?= sts_hari($data['sts_hari']) ?></span>
                                                                <?php }else{ ?>
                                                                    <span class="badge badge-pill badge-danger"><?= sts_hari($data['sts_hari']) ?></span>
                                                                <?php } ?>
                                                            </td>
                                                            <td>
                                                                <div class="custom-control custom-switch">
                                                                    <input type="checkbox" name="hari<?=$id;?>" value="<?=$id;?>" class="custom-control-input" id="customswitch<?=$id;?>"  <?php if($data['sts_hari']==1){ ?> checked <?php } ?>>
                                                                    <label class="custom-control-label" for="customswitch<?=$id;?>">Off/On</label>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <input type="hidden" class="form-control" name="id_hari[]" value="<?=$id;?>" required>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                                <button type="submit" class="btn btn-danger">Save</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!-- [ Main Content ] end -->
                           
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
