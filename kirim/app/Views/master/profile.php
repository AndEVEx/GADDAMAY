<!-- [ Main Content ] start -->
<div class="pcoded-main-container">
        <div class="pcoded-wrapper container">
            <div class="pcoded-content">
                <div class="pcoded-inner-content">
                    <div class="main-body">
                        <div class="page-wrapper">
                            <div class="page-header">
                                <div class="page-block">
                                    <div class="row align-items-center">
                                        <div class="col-md-12">
                                            <div class="page-header-title">
                                                <h5 class="m-b-10"><?=$title;?></h5>
                                            </div>
                                            <ul class="breadcrumb">
                                                <li class="breadcrumb-item"><a href="<?= base_url('Home'); ?>"><i class="feather icon-home"></i></a></li>
                                                <li class="breadcrumb-item"><a href="<?= base_url($nav1); ?>"><?=$nav1;?></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- [ Main Content ] start -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Validasi Data</h5>
                                        </div>
                                        <div class="card-body">
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
                                        <?php endif;

                                        foreach ($getPegawai as $data) { 
                                        ?>
                                        <form action="<?= base_url('Profile/update'); ?>" method="POST">
                                            <div class="form-row">
                                                <div class="col-md-12 mb-3">
                                                    <label>Nomor Absensi</label>
                                                    <input type="text" class="form-control" value="<?= $data['nomor_absensi'] ?>" readonly required>
                                                    
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-md-12 mb-3">
                                                    <label>NIP</label>
                                                    <input type="text" class="form-control" name="nip"  value="<?= $data['nip'] ?>" required>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-md-12 mb-3">
                                                    <label>NIK</label>
                                                    <input type="text" class="form-control" name="nik"  value="<?= $data['nik'] ?>" required>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-md-12 mb-3">
                                                    <label>Nama Lengkap</label>
                                                    <input type="text" class="form-control" name="nama"  value="<?= $data['nama_ptk'] ?>" required>
                                                    
                                                </div>
                                            </div>
                                            
                                            <div class="form-row">
                                                <div class="col-md-12 mb-3">
                                                    <label>Jenis PTK</label>
                                                    <input type="text" class="form-control" value="<?= $data['nama_jenis_ptk'] ?>" readonly required>
                                                    
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-md-12 mb-3">
                                                    <label>Alamat</label>
                                                    <input type="text" class="form-control" name="alamat" value="<?= $data['alamat'] ?>" required>
                                                    
                                                </div>
                                                
                                            </div>
                                            <div class="form-row">
                                                <div class="col-md-12 mb-3">
                                                    <label>Tempat Lahir</label>
                                                    <input type="text" class="form-control" name="tempat_lahir" value="<?= $data['tempat_lahir'] ?>" required>
                                                    
                                                </div>
                                                
                                            </div>
                                            <div class="form-row">
                                                <div class="col-md-12 mb-3">
                                                    <label>Tanggal Lahir</label>
                                                    <input type="date" class="form-control" name="tgl_lahir" value="<?= $data['tgl_lahir'] ?>" required>
                                                    
                                                </div>
                                                
                                            </div>
                                            <div class="form-row">
                                                
                                                <div class="col-md-6 mb-3">
                                                    <label>HP (Active Wa)</label>
                                                    <input type="number" class="form-control" name="hp" value="<?= $data['no_hp'] ?>" required>
                                                    
                                                </div>
                                                
                                            </div>
                                            
                                            <input type="hidden" class="form-control" name="id"  value="<?= session()->get('id_user') ?>" required>
                                            <button class="btn  btn-primary" type="submit">Save</button>
                                        </form>
                                        <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Change Password</h5>
                                        </div>
                                        <div class="card-body">
                                        <?php if(session()->get('successpass')) : ?>
                                            <div class="alert alert-success alert-dismissible">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                            <center><h5><i class="icon fas fa-check"></i> Data sukses <strong><?= session()->getFlashdata('successpass'); ?></strong></h5></center> 
                                            </div>
                                        <?php endif; ?>

                                        <?php if(session()->get('errorpass')) : ?>
                                            <div class="alert alert-danger alert-dismissible">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                            <center><h5><i class="icon fas fa-check"></i> Data gagal <strong><?= session()->getFlashdata('errorpass'); ?></strong></h5></center> 
                                            </div>
                                        <?php endif; ?>
                                        <form class="needs-validation" novalidate action="<?= base_url('Profile/updatepassword'); ?>" method="POST">
                                            
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label">New Password</label>
                                                <div class="col-sm-9">
                                                    <input type="password" class="form-control"  placeholder="New Password" name="password1" required>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label">Repeat Password</label>
                                                <div class="col-sm-9">
                                                    <input type="password" class="form-control" placeholder="Repeat Password" name="password2" required>
                                                </div>
                                            </div>
                                            <input type="hidden" class="form-control" name="id"  value="<?= session()->get('id_user') ?>" required>
                                            <button class="btn  btn-primary" type="submit">Save</button>
                                        </form>
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
    <!-- [ Main Content ] end -->