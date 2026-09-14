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
                                <div class="col-sm-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-10">
                                                <h5>Form Kegiatan Mengajar</h5>
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
                                        <form name="form1" class="was-validated" method="post" action="<?= base_url('Absenmengajar/add'); ?>" enctype="multipart/form-data">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label>Materi Mengajar</span></label>
                                                    <input type="text" class="form-control" name="nama" required>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label>Catatan</label>
                                                    <input type="text" class="form-control" name="catatan" required>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label>Latitude & longitude</label>
                                                    <input type="text" class="form-control"  id="lat" name="latlong" required readonly>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="row">
                                                    <div class="col-sm">
                                                        <div class="webcam-capture"></div>
                                                    </div>
                                                    <div class="col-sm d-flex justify-content-center">
                                                        <button type="button" class="btn btn-info" onClick="take_snapshot()">Ambil Foto</button>
                                                    </div>
                                                    <div class="col-sm">
                                                        <div id="results"></div>
                                                    </div>
                                                </div>
                                                 
                                            </div>
                                            <br>
                                            <div class="col-sm-12">   
                                                <input type="hidden" name="file" class="image-tag">
                                                <button type="submit" class="btn btn-danger" onClick="captureimage(0)">Save</button>
                                            </div>
                                            </div>
                                        </form>
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

    