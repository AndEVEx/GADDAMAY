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
                                                <h5>Filter Data Biweekly</h5>
                                                </div>
                                                
                                            </div>
                                            
                                        </div>
                                        <form method="post" action="<?= base_url('Absensisiswa/biweekly'); ?>">
                                        <div class="card-body table-border-style">
                                            <div class="row">
                                                <div class="col-3">
                                                    <select class="form-control" name="id_rombel" required>
                                                        <option value="">Pilih Rombel</option>
                                                        <?php foreach ($getRombel as $data) { ?>
                                                        <option value="<?=$data['id_rombel'] ?>" <?= ($idRombel==$data['id_rombel'])?'selected':'' ?>><?=$data['nm_rombel'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="col-3">
                                                    <select class="form-control" name="bln" required>
                                                        <option value="">Pilih Bulan</option>
                                                        <option value="1" <?= ($getBulan==1)?'selected':'' ?>>Januari</option>
                                                        <option value="2" <?= ($getBulan==2)?'selected':'' ?>>Februari</option>
                                                        <option value="3" <?= ($getBulan==3)?'selected':'' ?>>Maret</option>
                                                        <option value="4" <?= ($getBulan==4)?'selected':'' ?>>April</option>
                                                        <option value="5" <?= ($getBulan==5)?'selected':'' ?>>Mei</option>
                                                        <option value="6" <?= ($getBulan==6)?'selected':'' ?>>Juni</option>
                                                        <option value="7" <?= ($getBulan==7)?'selected':'' ?>>Juli</option>
                                                        <option value="8" <?= ($getBulan==8)?'selected':'' ?>>Agustus</option>
                                                        <option value="9" <?= ($getBulan==9)?'selected':'' ?>>September</option>
                                                        <option value="10" <?= ($getBulan==10)?'selected':'' ?>>Oktober</option>
                                                        <option value="11" <?= ($getBulan==11)?'selected':'' ?>>November</option>
                                                        <option value="12" <?= ($getBulan==12)?'selected':'' ?>>Desember</option>
                                                    </select>
                                                </div>
                                                <div class="col-3">
                                                    <select class="form-control" name="periode" required>
                                                        <option value="">Pilih Periode</option>
                                                        <option value="1" <?= ($getPeriode==1)?'selected':'' ?>>Tanggal 1 - 15</option>
                                                        <option value="2" <?= ($getPeriode==2)?'selected':'' ?>>Tanggal 16 - Akhir Bulan</option>
                                                    </select>
                                                </div>
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
                                                <h5>Laporan Biweekly <?= $nmRombel ?> | <?= $getTanggal1 ?> s/d <?= $getTanggal2 ?></h5>
                                                </div>
                                                
                                                <?php if(!empty($nmRombel)) { ?>
                                                <div class="col-md-auto">
                                                    <form method="post" action="<?= base_url('Export/biweeklyrombel'); ?>" style="display:inline;">
                                                        <input type="hidden" name="id_rombel" value="<?= $idRombel ?>">
                                                        <input type="hidden" name="tgl1" value="<?= $getTanggal1 ?>">
                                                        <input type="hidden" name="tgl2" value="<?= $getTanggal2 ?>">
                                                        <button class="btn btn-success btn-sm" type="submit"><i class="feather icon-download"></i> Excel</button>
                                                    </form>
                                                </div>
                                                <?php } ?>
                                            </div>
                                            
                                        </div>
                                        <div class="card-body table-border-style">
                                            <div class="table-responsive">
                                                <table id="example" class="table table-striped table-hover">
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
