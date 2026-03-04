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
                                                <li class="breadcrumb-item"><a href="<?= base_url($nav); ?>">Ajukan Izin</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- [ breadcrumb ] end -->
                            <!-- [ Main Content ] start -->
                            <div class="row">
                                <!-- [ static-layout ] start -->
                                <div class="col-sm-5">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-8">
                                                    <h5>Isi Data Pengajuan Izin</h5>
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
                                        <form class="was-validated" method="post" action="<?= base_url('Ajukanizinsiswa/add'); ?>" enctype="multipart/form-data">
                                        <div class="card-body table-border-style">
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-12">
                                                        
                                                        <div class="form-group">
                                                            <label>Upload Foto</label>
                                                            <div class="custom-file">
                                                                <input type="file" name="file" class="custom-file-input" required>
                                                                <label class="custom-file-label">Pilih File...</label>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Keterangan</label>
                                                            <input type="text" class="form-control" name="keterangan" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Tanggal Izin</label>
                                                            <input type="date" class="form-control" name="tgl" required> 
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Status</label>
                                                            <select class="form-control" name="status" required>
                                                                <option>Pilih</option>
                                                                <option value="3">Ijin</option>
                                                                <option value="2">Sakit</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                   
                                                    <div class="col-sm">
                                                        <button type="submit" class="btn btn-danger">Ajukan</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="col-sm-7">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-10">
                                                <h5>Data Pengajuan</h5>
                                                </div>
                                              
                                            </div>
                                       
                                        </div>
                                       
                                        <div class="card-body table-border-style">
                                            <div class="table-responsive">
                                                <table class="table table-striped table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Tanggal Pengajuan</th>
                                                            <th>Status Absen</th>
                                                            <th>Keterangan</th>
                                                            <th>Status Pengajuan</th>
                                                            <th>Tgl Approve/Reject</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php 
                                                        $no=0;
                                                        foreach ($getAbsen as $data) {  
                                                        $no++;
                                                        ?>
                                                        <tr>
                                                            <td><?= $no ?></td>
                                                            <td><?=formatTanggal($data['tgl_absen']);?></td>
                                                            <td><?= stsizin($data['sts_absen']) ?></td>
                                                            <td><?= $data['ket_absen'] ?></td>
                                                            <td>
                                                                <?php if($data['sts_approve']==0){ ?>
                                                                    <span class="badge badge-pill badge-warning"><?= stsapprove($data['sts_approve']) ?></span>
                                                                <?php }elseif($data['sts_approve']==1){ ?>
                                                                    <span class="badge badge-pill badge-success"><?= stsapprove($data['sts_approve']) ?></span>
                                                                <?php }else{ ?>
                                                                    <span class="badge badge-pill badge-danger"><?= stsapprove($data['sts_approve']) ?></span>
                                                                <?php } ?>
                                                               
                                                            </td>
                                                            <td><?=date('d-m-Y H:i:s', strtotime($data['tgl_approve']));?></td>
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
