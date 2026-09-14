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
                                                <h5 class="m-b-10"><?=$title;?> </h5>
                                            </div>
                                            <ul class="breadcrumb">
                                                <li class="breadcrumb-item"><a href="<?= base_url('Home'); ?>"><i class="feather icon-home"></i></a></li>
                                                <li class="breadcrumb-item"><a href="<?= base_url('Siswarombel'); ?>"> Setting Kelas</a></li>
                                                <li class="breadcrumb-item"><a href="<?= base_url($nav); ?>"></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- [ breadcrumb ] end -->
                            
                           
                            <div class="row">
                                <!-- [ static-layout ] start -->
                                <div class="col-sm-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-10">
                                                <h5>Data Kelas <?= nmrombel($getIdrombel)?></h5>
                                                </div>
                                               
                                            </div>
                                            
                                            <?php if(session()->get('success1')) : ?>
                                            <div class="alert alert-success alert-dismissible">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                            <center><h5><i class="icon fas fa-check"></i> Data sukses <strong><?= session()->getFlashdata('success1'); ?></strong></h5></center> 
                                            </div>
                                        <?php endif; ?>

                                        <?php if(session()->get('error1')) : ?>
                                            <div class="alert alert-danger alert-dismissible">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                            <center><h5><i class="icon fas fa-check"></i> Data gagal <strong><?= session()->getFlashdata('error1'); ?></strong></h5></center> 
                                            </div>
                                        <?php endif; ?>
                                        </div>
                                        <div class="card-body table-border-style">
                                            <div class="table-responsive">
                                            <form method="post" action="<?= base_url('Siswarombel/updaterombel'); ?>">
                                                <table class="table table-striped table-hover" id="example">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>No Induk</th>
                                                            <th>Nama Siswa</th>
                                                            <th>kelas</th>
                                                            <th>JK</th>
                                                            <th>Pindah</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php 
                                                        $no1=0;
                                                        foreach ($getSiswarombel as $data) {  
                                                            $id = $data['id_siswa_rombel'];
                                                        $no1++;
                                                        ?>
                                                        <tr>
                                                            <td><?= $no1 ?></td>
                                                            <td><?= $data['no_induk'] ?></td>
                                                            <td><?= $data['nm_siswa'] ?></td>
                                                            <td><?= $data['nm_rombel'] ?></td>
                                                            <td><?= jk($data['jk']) ?></td>
                                                            <td>
                                                                <select class="form-control" name="id_rombel[]">
                                                                    <option>Pilih</option>
                                                                    <?php foreach ($getRombel as $datarombel) { ?>
                                                                    <option value="<?=$datarombel['id_rombel'];?>" <?php if($datarombel['id_rombel']==$data['id_rombel']){?>selected="" <?php } ?>><?=$datarombel['nm_rombel'];?></option>
                                                                    <?php } ?>
                                                                </select>
                                                                <input type="hidden" class="form-control" name="id[]" value="<?=$id;?>" required>
                                                            </td>
                                                        </tr>
                                                        
                                                       <?php } ?>
                                                    </tbody>
                                                </table>
                                                
                                                <button type="submit" class="btn btn-danger">Save</button>
                                                </form>
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
