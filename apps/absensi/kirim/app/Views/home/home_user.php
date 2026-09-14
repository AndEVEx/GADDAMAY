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
                                <?php if((session()->get('id_jenis'))==2){?>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Point</h5>
                                        </div>
                                        <div class="card-body">
                                            <div id="line-chart-1"></div>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Jumlah Data Absensi Bulan <?= bulan(date('m'))?> <?= date('Y')?></h5>
                                        </div>
                                        <div class="card-body">
                                            <div id="pie-chart-2" style="width:100%"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="card support-bar overflow-hidden">
                                        <div class="card-body pb-0" style="height: 200px;">
                                            
                                            <h1 class="m-0"><?= sts_absen(session()->get('id_user'),date('Y-m-d')) ?></h1>
                                            <span class="text-c-blue">Senin, 22 September 2023</span>
                                            
                                        </div>
                                        
                                        <div class="card-footer bg-primary text-white">
                                            <div class="row text-center">
                                                
                                                <div class="col">
                                                    <h4 class="m-0 text-white"><?= jammasuk(session()->get('id_user'),date('Y-m-d')) ?></h4>
                                                    <span>Masuk</span>
                                                </div>
                                                <div class="col">
                                                    <h4 class="m-0 text-white"><?= jampulang(session()->get('id_user'),date('Y-m-d')) ?></h4>
                                                    <span>Pulang</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
 
                                <div class="col-xl-6 col-md-12">
                                    <div class="card text-white bg-info ">
                                        <div class="card-header">TOTAL GAJI SEPTEMBER 2023</div>
                                            <div class="card-body">
                                            <?php if(approvegaji(session()->get('id_user'),date('m'),date('Y'))>0) {?>
                                                <h5 class="card-title text-white"><?= rupiah(gaji(session()->get('id_user'),date('m'),date('Y'))) ?></h5>
                                                <p class="card-text">Jika terdapat ketidak sesuaian, segera konfermasi ke Bendahara.</p>
                                                <a href="<?= base_url('Saldo'); ?>">
                                                    <button class="btn  btn-warning" type="submit"><i class="feather mr-2 icon-check-circle"></i> Wa Ibu ....</button>
                                                </a>
                                            <?php }else{ ?>
                                                <h5 class="card-title text-white">Masih dalam proses verifikasi</h5>
                                            <?php } ?>
                                            </div>
                                            
                                        </div>
                                </div>

                            </div>
                            <!-- [ Main Content ] end -->

                            </div>
                            <!-- [ Main Content ] end -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->