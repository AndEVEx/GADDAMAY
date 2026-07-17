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
                                                <h5>Filter Data</h5>
                                                </div>
                                                
                                            </div>
                                            
                                        </div>
                                        <form method="post" action="<?= base_url('Reportwal/bulanan'); ?>">
                                        <div class="card-body table-border-style">
                                            <div class="row">
                                                <div class="col-3">
                                                    <select class="form-control" name="bln" required>
                                                        <option disabled value="">Pilih Bulan</option>
                                                        <option value="1" <?= $getBulan == 1 ? 'selected' : '' ?>>Januari</option>
                                                        <option value="2" <?= $getBulan == 2 ? 'selected' : '' ?>>Februari</option>
                                                        <option value="3" <?= $getBulan == 3 ? 'selected' : '' ?>>Maret</option>
                                                        <option value="4" <?= $getBulan == 4 ? 'selected' : '' ?>>April</option>
                                                        <option value="5" <?= $getBulan == 5 ? 'selected' : '' ?>>Mei</option>
                                                        <option value="6" <?= $getBulan == 6 ? 'selected' : '' ?>>Juni</option>
                                                        <option value="7" <?= $getBulan == 7 ? 'selected' : '' ?>>Juli</option>
                                                        <option value="8" <?= $getBulan == 8 ? 'selected' : '' ?>>Agustus</option>
                                                        <option value="9" <?= $getBulan == 9 ? 'selected' : '' ?>>September</option>
                                                        <option value="10" <?= $getBulan == 10 ? 'selected' : '' ?>>Oktober</option>
                                                        <option value="11" <?= $getBulan == 11 ? 'selected' : '' ?>>November</option>
                                                        <option value="12" <?= $getBulan == 12 ? 'selected' : '' ?>>Desember</option>
                                                    </select>
                                                </div>
                                                <?php if (isset($getRombel) && count($getRombel) > 1) { ?>
                                                <div class="col-3">
                                                    <select class="form-control" name="id_rombel" required>
                                                        <?php foreach ($getRombel as $r) { ?>
                                                            <option value="<?= $r['id_rombel'] ?>" <?= $r['id_rombel'] == $idRombel ? 'selected' : '' ?>><?= $r['nm_rombel'] ?></option>
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
                                                <h5>Tabel Data Absensi Bulan <?=bulan($getBulan);?>, Kelas <?= $nmRombel ?></h5>
                                                </div>
                                                
                                                <div class="col-md-auto">
                                                    <a href="<?= base_url('Reportwal/cetakbulanan'); ?>/?bln=<?=$getBulan;?>" target="_blank">
                                                        <button class="btn btn-danger btn-sm" type="submit"><i class="feather icon-printer"></i> PDF</button>
                                                    </a>
                                                </div>
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
                                                            <td><?= jummasukbln($id,$getBulan) ?></td>
                                                            <td><?= jumterlambatbln($id,$getBulan) ?></td>
                                                            <td><?= jumsakitbln($id,$getBulan) ?></td>
                                                            <td><?= jumizinbln($id,$getBulan) ?></td>
                                                            <td><?= jumalphabln($id,$getBulan) ?></td>
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
