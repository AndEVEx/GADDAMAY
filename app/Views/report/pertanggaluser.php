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
                                                <li class="breadcrumb-item"><a href="<?= base_url($nav); ?>">Pertanggal User</a></li>
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
                                        <form method="post" action="<?= base_url('Absensi/pertanggaluser'); ?>">
                                        <div class="card-body table-border-style">
                                            <div class="row">
                                                <div class="col-2">
                                                    <input type="date" class="form-control" name="tgl1" required>
                                                </div>
                                                <div class="col-2">
                                                    <input type="date" class="form-control" name="tgl2" required>
                                                </div>
                                                <div class="col-3">
                                                    <select class="form-control" name="id_ptk" required>
                                                        <option>Pilih Guru/karyawan</option>
                                                       
                                                        <?php foreach ($getPegawai as $data) { ?>
                                                        <option value="<?=$data['id_ptk'] ?>"><?=$data['nama_ptk'] ?></option>
                                                        <?php } ?>
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
                                                <h5>Tabel Data Absensi Dari <?=formatTanggal($getTanggal1);?> s/d <?=formatTanggal($getTanggal2);?>, Nama : <?= $nama ?></h5>
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
                                                            <th>Tanggal</th>
                                                            <th>Jam Masuk</th>
                                                            <th>Status</th>
                                                            <th>Jam Pulang</th>
                                                            <th>Total Jam</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php 
                                                        if($idPtk>0){
                                                            $no=0;
                                                            foreach ($getAbsensi as $data) {  
                                                            $no++;
                                                            $id =$data['ID_PEGAWAI'];
                                                            $tgl = $data['TANGGAL'];
                                                            $jammasuk = jammasuk($idPtk,$tgl);
                                                            $jampulang = jampulang($idPtk,$tgl);
    
                                                            $diff    =strtotime($jampulang) - strtotime($jammasuk);
                                                            $jam    =floor($diff / (60 * 60));
                                                            $menit    =$diff - $jam * (60 * 60);
                                                        ?>
                                                        <tr>
                                                            <td><?= $no ?></td>
                                                            <td><?= $data['TANGGAL'] ?></td>
                                                            <td><?= $jammasuk ?></td>
                                                            <td><?= sts_absen($id,$tgl) ?></td>
                                                            <td><?= $jampulang ?></td>
                                                            <td>
                                                                <?php 
                                                                if(empty($jampulang)){
                                                                    echo "";
                                                                }elseif(empty($jammasuk)){
                                                                    echo "";
                                                                }else{
                                                                ?>
                                                                <?= $jam ?> jam, <?= floor( $menit / 60 )  ?> menit
                                                                <?php } ?>
                                                            </td>
                                                        </tr>    
                                                       <?php 
                                                        } 
                                                       }?>
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
