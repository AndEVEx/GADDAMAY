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
                                                <li class="breadcrumb-item"><a href="#!"> Info Gaji</a></li>
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
                                                <h5>Cari Data Pegawai</h5>
                                                </div>
                                              
                                            </div>
                                      
                                        </div>
                                        <form method="post" action="<?= base_url('Gajidosen/report'); ?>">
                                        <div class="card-body table-border-style">
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label>Nama Pegawai</label>
                                                            <select class="form-control" id="single-select-field" name="id_ptk" required>
                                                                <option>Pilih</option>    
                                                                <?php foreach ($getPegawai as $data) { ?>
                                                                <option value="<?=$data['id_ptk'] ?>"><?=$data['nama_ptk'] ?> - <?=$data['nomor_absensi'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Bulan</label>
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
                            
                            <?php if($getId>0){?>

                                <div class="col-sm-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-9">
                                                <h5>Rekap Gaji <?=$getNama;?> Bulan <?=bulan($getBulan);?> <?=$getTahun;?> </h5>
                                                </div>
                                                <div class="col-3">
                                                <?php if(approvegaji($getId,$getBulan,$getTahun)>0) {?>
                                                    <a href="<?= base_url('Gajidosen/cetakpegawai'); ?>/?id=<?=$getId;?>&bln=<?=$getBulan;?>&thn=<?=$getTahun;?>" target="_blank">
                                                        <button class="btn btn-primary btn-sm" type="submit"><i class="feather icon-printer"></i> Print</button>
                                                    </a>
                                                <?php } ?>
                                                </div>
                                            </div>
                                      
                                        </div>
                                       
                                        <div class="card-body table-border-style">
                                            <div class="container">
                                                <div class="row">
                                                    <?php foreach ($getKatgaji as $data1) { 
                                                    $id_kat = $data1['id_kat_gaji']; 
                                                    $tot_gaji[] = gajidetail($getId,$getBulan,$getTahun,$id_kat);
                                                    ?>
                                                        <div class="col-12">
                                                            <div class="form-group row">
                                                                <label for="inputEmail3" class="col-sm-5 col-form-label"><?= $data1['nm_kat_gaji'] ?></label>
                                                                <div class="col-sm-7">
                                                                    <input type="text" class="form-control" value="<?= rupiah(gajidetail($getId,$getBulan,$getTahun,$id_kat));?>">
                                                                </div>
                                                            </div>
                                                                            
                                                        </div>
                                                    <?php } 
                                                    $total = array_sum($tot_gaji);
                                                    ?>
                                                        <div class="col-12">
                                                            <div class="form-group row">
                                                                <label for="inputEmail3" class="col-sm-5 col-form-label"><b>TOTAL GAJI</b></label>
                                                                <div class="col-sm-7">
                                                                    <?php if(approvegaji($getId,$getBulan,$getTahun)>0) {?>
                                                                        <button type="button" class="btn btn-info"><?=rupiah($total);?></button>
                                                                    <?php }else{ ?>
                                                                        <button type="button" class="btn btn-danger"><?=rupiah($total);?> <br>(BELUM APPROVE)</button>  
                                                                    <?php } ?>
                                                                </div>
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
        </div>
    </div>
