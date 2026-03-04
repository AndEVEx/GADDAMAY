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
                                        <form method="post" action="<?= base_url('Absensi/pegawai'); ?>">
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
                                <div class="col-sm-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-10">
                                                <h5>Rekap Absensi Bulan <?=bulan($getBulan);?> </h5>
                                                </div>
                                              
                                            </div>
                                      
                                        </div>
                                        <form method="post" action="<?= base_url('Absensi/koreksi'); ?>">
                                        <div class="card-body table-border-style">
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <label for="inputEmail3" class="col-sm-5 col-form-label">Masuk</label>
                                                            <div class="col-sm-7">
                                                                <input type="email" class="form-control" value="<?= jummasukbln($getId,$getBulan) ?>">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label for="inputEmail3" class="col-sm-5 col-form-label">Terlambat</label>
                                                            <div class="col-sm-7">
                                                                <input type="email" class="form-control" value="<?= jumterlambatbln($getId,$getBulan) ?>">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label for="inputEmail3" class="col-sm-5 col-form-label">Sakit</label>
                                                            <div class="col-sm-7">
                                                                <input type="email" class="form-control" value="<?= jumsakitbln($getId,$getBulan) ?>">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label for="inputEmail3" class="col-sm-5 col-form-label">Izin</label>
                                                            <div class="col-sm-7">
                                                                <input type="email" class="form-control" value="<?= jumizinbln($getId,$getBulan) ?>">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label for="inputEmail3" class="col-sm-5 col-form-label">Belum Absen Pulang</label>
                                                            <div class="col-sm-7">
                                                                <input type="email" class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label for="inputEmail3" class="col-sm-5 col-form-label">Alpha</label>
                                                            <div class="col-sm-7">
                                                                <?php if($getBulan==date('m')){?>
                                                                    <input type="email" class="form-control" value="<?= jumalphatgl($getId) ?>">
                                                                <?php }else{?>
                                                                    <input type="email" class="form-control" value="<?= jumalphabln($getId,$getBulan) ?>">
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                        <?php if(session()->get('id_jenis')==2){?>
                                                        <div class="form-group row">
                                                            <label for="inputEmail3" class="col-sm-5 col-form-label">Point</label>
                                                            <div class="col-sm-7">
                                                                <input type="email" class="form-control" value="<?= jmlpoint($getId,$getBulan) ?>">
                                                            </div>
                                                        </div>
                                                        <?php } ?>
                                                    </div>
                                                   
                                                </div>
                                            </div>
                                        </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-10">
                                                <h5><?= session()->get('nama') ?></h5>
                                                </div>
                                              
                                            </div>
                                      
                                        </div>
                                        
                                        <div class="card-body table-border-style">
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <label for="inputEmail3" class="col-sm-5 col-form-label">Tanggal</label>
                                                    <div class="col-sm-7">
                                                        <input type="email" class="form-control" value="<?= hari_ini() ?>, <?= date('d F Y') ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="inputEmail3" class="col-sm-5 col-form-label">Jenis Ketenagaan</label>
                                                    <div class="col-sm-7">
                                                        <input type="email" class="form-control" value="<?= jnskerja(session()->get('id_jenis')) ?>">
                                                    </div>
                                                </div>   
                                                <div class="form-group row">
                                                    <label for="inputEmail3" class="col-sm-5 col-form-label">Absen Masuk</label>
                                                    <div class="col-sm-7">
                                                        <input type="email" class="form-control" value="<?= jammasuk($getId,date('Y-m-d')) ?>">
                                                    </div>
                                                </div>     
                                                <div class="form-group row">
                                                    <label for="inputEmail3" class="col-sm-5 col-form-label">Absen Pulang</label>
                                                    <div class="col-sm-7">
                                                        <input type="email" class="form-control" value="<?= jampulang($getId,date('Y-m-d')) ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="inputEmail3" class="col-sm-5 col-form-label">Status Absen</label>
                                                    <div class="col-sm-7">
                                                        <input type="email" class="form-control" value="<?= sts_absen($getId,date('Y-m-d')) ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="inputEmail3" class="col-sm-5 col-form-label">Keterangan</label>
                                                    <div class="col-sm-7">
                                                        <input type="email" class="form-control" value="<?= ketabsen($getId,date('Y-m-d')) ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </form>
                                    </div>
                                </div>
                                <!-- [ static-layout ] end -->
                            </div>
                            <!-- [ Main Content ] end -->
                            <div class="row">
                                <!-- [ static-layout ] start -->
                                <div class="col-sm-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-10">
                                                <h5>Tabel Data Absensi Bulan <?=bulan($getBulan);?></h5>
                                                </div>
                                              
                                            </div>
                                      
                                        </div>
                                        <div class="card-body table-border-style">
                                        <div class="container">
                                            <div class="row">
                                                <?php 
                                                $jumHari = cal_days_in_month(CAL_GREGORIAN, $getBulan, date('Y'));
                                                for($i=1;$i<=$jumHari;$i++){
                                                    $tanggal = date('Y').'-'.$getBulan.'-'.$i;
                                                    $namahari = date('l', strtotime($tanggal));
                                                ?>
                                                <div class="col-2">
                                                    <a href="" data-toggle="modal" data-target="#detail<?=$i;?>">
                                                    <div class="card text-white <?php if(harilibur($namahari)==2){?> bg-danger <?php }else{?>bg-primary <?php } ?> mb-3" style="max-width: 10rem;">
                                                        <div class="card-header"><?=hari($namahari);?>, tgl <?=$i;?></div>
                                                        <div class="card-body">
                                                            <?php if(harilibur($namahari)==2){?>
                                                            <h5 class="card-title">Libur</h5>
                                                            <?php }else{ ?>
                                                                <h5 class="card-title text-white"><?=sts_absen($getId,$tanggal);?></h5>
                                                            <?php } ?>
                                                            <p class="card-text">
                                                                JM : <?=jammasuk($getId,$tanggal);?><br>
                                                                JP : <?=jampulang($getId,$tanggal);?><br>
                                                                Point : <?=jmlpointtgl($getId,$tanggal);?><br>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    </a>
                                                </div>

                                                                <!-- Edit The Modal -->
                                                                <div class="modal fade" id="detail<?=$i;?>">
                                                                    <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                                
                                                                    <!-- Modal Header -->
                                                                    <div class="modal-header">
                                                                    <h4 class="modal-title">Detail Data Absensi <?= session()->get('nama') ?></h4>
                                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                    </div>
                                                                                    
                                                                    <!-- Modal body -->
                                                                        <div class="modal-body">
                                                                        <div class="col-12">
                                                                            <div class="form-group row">
                                                                                <label for="inputEmail3" class="col-sm-5 col-form-label">Tanggal</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="email" class="form-control" value="<?=hari($namahari);?>, <?=formatTanggal($tanggal);?>">
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group row">
                                                                                <label for="inputEmail3" class="col-sm-5 col-form-label">Jenis Ketenagaan</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="email" class="form-control" value="<?= jnskerja(session()->get('id_jenis')) ?>">
                                                                                </div>
                                                                            </div>   
                                                                            <div class="form-group row">
                                                                                <label for="inputEmail3" class="col-sm-5 col-form-label">Absen Masuk</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="email" class="form-control" value="<?=jammasuk($getId,$tanggal);?>">
                                                                                </div>
                                                                            </div>     
                                                                            <div class="form-group row">
                                                                                <label for="inputEmail3" class="col-sm-5 col-form-label">Absen Pulang</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="email" class="form-control" value="<?=jampulang($getId,$tanggal);?>">
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group row">
                                                                                <label for="inputEmail3" class="col-sm-5 col-form-label">Status Absen</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="email" class="form-control" value="<?=sts_absen($getId,$tanggal);?>">
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group row">
                                                                                <label for="inputEmail3" class="col-sm-5 col-form-label">Keterangan</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="email" class="form-control" value="<?= ketabsen($getId,$tanggal) ?>">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        </div>
                                                                </div>
                                                                </div>
                                                                </div>
                                                <?php } ?>
                                                
                                            </div>
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
