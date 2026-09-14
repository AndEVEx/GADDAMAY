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
                                
                                <div class="col-sm-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-10">
                                                <h5>Data Pengajuan Izin</h5>
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
                                                            <th>Nama Siswa</th>
                                                            <th>Tanggal Pengajuan</th>
                                                            <th>Tanggal Izin</th>
                                                            <th>Status Absen</th>
                                                            <th>Keterangan</th>
                                                            <th>Status Pengajuan</th>
                                                            <th>File</th>
                                                            <th>Approve</th>
                                                            <th>Reject</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php 
                                                        $no=0;
                                                        foreach ($getAbsen as $data) {  
                                                        $no++;
                                                        $id = $data['id_siswa_absen'];
                                                        ?>
                                                        <tr>
                                                            <td><?= $no ?></td>
                                                            <td><?= $data['nm_siswa'] ?></td>
                                                            <td><?=date('d-m-Y H:i:s', strtotime($data['tgl_entri']));?></td>
                                                            <td>
                                                                <?php 
                                                                if(is_NULL($data['tgl_absen'])){
                                                                    echo "";
                                                                }else{
                                                                    echo formatTanggal($data['tgl_absen']);
                                                                }
                                                                ?>
                                                               
                                                            </td>
                                                            <td><?= stsizin($data['sts_absen']) ?></td>
                                                            <td><?= $data['ket_absen'] ?></td>
                                                            <td>
                                                                <?php if($data['sts_approve']==0){ ?>
                                                                    <span class="badge badge-pill badge-warning"><?= stsapprove($data['sts_approve']) ?></span>
                                                                <?php }elseif($data['STS']==1){ ?>
                                                                    <span class="badge badge-pill badge-success"><?= stsapprove($data['sts_approve']) ?></span>
                                                                <?php }else{ ?>
                                                                    <span class="badge badge-pill badge-danger"><?= stsapprove($data['sts_approve']) ?></span>
                                                                <?php } ?>
                                                               
                                                            </td>
                                                            <td>
                                                                <form method="post" action="<?= base_url('Approveijinsiswa/printijin'); ?>" target="_blank">
                                                                    <input type="hidden" name="id" value="<?=$id;?>">
                                                                    <button class="btn btn-info btn-sm" type="submit"><i class="feather icon-file"></i></button>
                                                                </form>

                                                            </td>
                                                            <td>
                                                                <button class="btn btn-success btn-sm" type="submit" data-toggle="modal" data-target="#approve<?=$no;?>"><i class="feather icon-check-square"></i></button>
                                                                
                                                                 <!-- delete The Modal -->
                                                                 <div class="modal fade" id="approve<?=$no?>">
                                                                    <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                                
                                                                    <!-- Modal Header -->
                                                                    <div class="modal-header">
                                                                    <h4 class="modal-title">Approve Izin</h4>
                                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                    </div>
                                                                                    
                                                                    <!-- Modal body -->
                                                                    <form method="post" action="<?= base_url('Approveijinsiswa/approve'); ?>">
                                                                        <div class="modal-body">
                                                                            Apakah anda yakin ingin menyetujui pengajuan <?=$data['nm_siswa'];?>?
                                                                            <input type="hidden" name="id" value="<?=$id;?>">
                                                                            <input type="hidden" name="tgl" value="<?=$data['tgl_absen'];?>">
                                                                            <input type="hidden" name="status" value="1">
                                                                            <br>
                                                                            <br>
                                                                            <button type="submit" class="btn btn-success">Approve</button>
                                                                        </div>
                                                                    </form>
                                                                    </div>
                                                                </div>
                                                                </div>
                                                            
                                                            </td>
                                                            <td>
                                                                <button class="btn btn-danger btn-sm" type="submit" data-toggle="modal" data-target="#reject<?=$no;?>"><i class="feather icon-x-circle"></i></button>
                                                                
                                                                <!-- delete The Modal -->
                                                                <div class="modal fade" id="reject<?=$no?>">
                                                                    <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                                
                                                                    <!-- Modal Header -->
                                                                    <div class="modal-header">
                                                                    <h4 class="modal-title">Reject Ijin</h4>
                                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                    </div>
                                                                                    
                                                                    <!-- Modal body -->
                                                                    <form method="post" action="<?= base_url('Approveijin/approve'); ?>">
                                                                        <div class="modal-body">
                                                                            Apakah anda yakin ingin reject pengajuan <?=$data['nm_siswa'];?>?
                                                                            <input type="hidden" name="id" value="<?=$id;?>">
                                                                            <input type="hidden" name="tgl" value="<?=$data['tgl_absen'];?>">
                                                                            <input type="hidden" name="status" value="2">
                                                                            <br>
                                                                            <br>
                                                                            <button type="submit" class="btn btn-danger">Reject</button>
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
