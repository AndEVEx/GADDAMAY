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
                                                <li class="breadcrumb-item"><a href="#!"> Info Absensi</a></li>
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
                                <div class="col-sm-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-6">
                                                <h5>Filter Data Berdasarkan Tanggal Awal dan Akhir</h5>
                                                </div>
                                                
                                            </div>
                                            
                                        </div>
                                        <form method="post" action="<?= base_url($nav); ?>">
                                        <div class="card-body table-border-style">
                                            <div class="row">
                                                <div class="col-2">
                                                    <input type="date" class="form-control" name="tgl1" value="<?= $getTanggal1 ?>" required>
                                                </div>
                                                <div class="col-2">
                                                    <input type="date" class="form-control" name="tgl2" value="<?= $getTanggal2 ?>" required>
                                                </div>
                                                <?php if (isset($getRombel) && count($getRombel) > 1) { ?>
                                                <div class="col-3">
                                                    <select class="form-control" name="id_rombel" required>
                                                        <option disabled value="">Pilih Kelas</option>
                                                        <?php foreach ($getRombel as $data) { ?>
                                                        <option value="<?=$data['id_rombel'] ?>" <?= $data['id_rombel'] == $idRombel ? 'selected' : '' ?>><?=$data['nm_rombel'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <?php } else { ?>
                                                    <input type="hidden" name="id_rombel" value="<?= $idRombel ?>">
                                                <?php } ?>
                                               
                                                <div class="col-2">
                                                <button type="submit" class="btn btn-danger">Cari Data</button>
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
                                <div class="col-sm-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col">
                                                <h5>Tabel Data Absensi Dari <?=formatTanggal($getTanggal1);?> s/d <?=formatTanggal($getTanggal2);?>, Kelas <?= $nmRombel ?></h5>
                                                </div>
                                                <div class="col-md-auto">
                                                    
                                                </div>
                                            </div>
                                            
                                        </div>
                                        <div class="card-body table-border-style">
                                            <div class="table-responsive">
                                                <table id="example2" class="table table-striped table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>No. Induk</th>
                                                            <th>Nama Siswa</th>
                                                            <th>Masuk</th>
                                                            <th>Terlambat</th>
                                                            <th>Sakit</th>
                                                            <th>Izin</th>
                                                            <th>Alpha</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php 
                                                        $no=0;
                                                        foreach ($getSiswa as $data) {  
                                                        $no++;
                                                        $id =$data['id_siswa'];
                                                        ?>
                                                        <tr>
                                                            <td><?= $no ?></td>
                                                            <td><?= $data['no_induk'] ?></td>
                                                            <td><?= $data['nm_siswa'] ?></td>
                                                            <td><?= jummasukpertanggal($id,$getTanggal1,$getTanggal2) ?></td>
                                                            <td><?= jumterlambatpertanggal($id,$getTanggal1,$getTanggal2) ?></td>
                                                            <td><?= jumsakitpertanggal($id,$getTanggal1,$getTanggal2) ?></td>
                                                            <td><?= jumizinpertanggal($id,$getTanggal1,$getTanggal2) ?></td>
                                                            <td><?= jumalphapertanggal($id,$getTanggal1,$getTanggal2) ?></td>
                                                        </tr>     
                                                       <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- [ static-layout ] end -->
                            </div>
                            <!-- [ Main Content ] end -->
                           
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
