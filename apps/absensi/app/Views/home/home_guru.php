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
                                <div class="col-sm-6">
                                    <div class="card support-bar overflow-hidden">
                                        <div class="card-body pb-0" style="height: 200px;">
                                            
                                            <h1 class="m-0"><?= sts_absen(session()->get('id_user'),date('Y-m-d')) ?></h1>
                                            <span class="text-c-blue"><?= hari_ini() ?>, <?= date('d F Y') ?></span>
                                            
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
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-8">
                                                   
                                                    <h4 class="text-primary"><?=jmlpoint(session()->get('id_user'),date('m'));?></h4>
                                                    <h6 class="text-muted m-b-0">Total Point <?= bulan(date('m'))?></h6>
                                                </div>
                                                <div class="col-4 text-right">
                                                    <i class="feather icon-user-x f-28"></i>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div> 
                                </div>
                                <div class="col-sm-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Jumlah Data Absensi Bulan <?= bulan(date('m'))?> <?= date('Y')?></h5>
                                        </div>
                                        <div class="card-body">
                                            <div id="pie-chart-2" style="width:100%"></div>
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