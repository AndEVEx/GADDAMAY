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
                                      
                                        </div>
                                       
                                        <div class="card-body table-border-style">
                                            <div class="table-responsive">
                                                <table class="table table-striped table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Nama Guru/Karyawan</th>
                                                            <th>Tanggal Pengajuan</th>
                                                            <th>Tanggal Izin</th>
                                                            <th>Status Absen</th>
                                                            <th>Keterangan</th>
                                                            <th>File</th>
                                                          
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php 
                                                        $no=0;
                                                        foreach ($getAbsen as $data) {  
                                                        $no++;
                                                        $id = $data['ID_PEGAWAI'];
                                                        ?>
                                                        <tr>
                                                            <td><?= $no ?></td>
                                                            <td><?= $data['nama_ptk'] ?></td>
                                                            <td><?=date('d-m-Y H:i:s', strtotime($data['TANGGAL_AKSES']));?></td>
                                                            <td><?=formatTanggal($data['TANGGAL_ABSEN']);?></td>
                                                            <td><?= stsizin($data['STATUS']) ?></td>
                                                            <td><?= $data['KETERANGAN'] ?></td>
                                                          
                                                            <td>
                                                                <form method="post" action="<?= base_url('Approveijin/printijin'); ?>" target="_blank">
                                                                    <input type="hidden" name="id" value="<?=$id;?>">
                                                                    <input type="hidden" name="tgl" value="<?=$data['TANGGAL_ABSEN'];?>">
                                                                    <button class="btn btn-info btn-sm" type="submit"><i class="feather icon-file"></i></button>
                                                                </form>

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
