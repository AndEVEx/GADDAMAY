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
                                        <form method="post" action="<?= base_url('Lembur'); ?>">
                                        <div class="card-body table-border-style">
                                            <div class="row">
                                                <div class="col-3">
                                                    <input type="date" class="form-control" name="tgl" required>
                                                </div>
                                                <div class="col-3">
                                                    <select class="form-control" name="jenis" required>
                                                        <option>Pilih Jenis Ketenagaan</option>
                                                        <option value="All">All</option>
                                                        <?php foreach ($getJenis as $data) { ?>
                                                        <option value="<?=$data['id_jenis_ptk'] ?>"><?=$data['nama_jenis_ptk'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="col-3">
                                                    <select class="form-control" name="shift" required>
                                                        <option>Pilih Shift</option>
                                                        <option value="All">All</option>
                                                        <?php foreach ($getShift as $data) { ?>
                                                        <option value="<?=$data['id_shift'] ?>"><?=$data['nm_shift'] ?></option>
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
                                                <div class="col-6">
                                                <h5>Tabel Data Terdeteksi Lembur Tanggal <?=formatTanggal($getTanggal);?></h5>
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
                                        <form method="post" action="<?= base_url('Lembur/add'); ?>">
                                        <div class="card-body table-border-style">
                                            <div class="table-responsive">
                                                <table id="example" class="table table-striped table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Nama Pegawai</th>
                                                            <th>Jenis Kerja</th>
                                                            <th>Jam Masuk</th>
                                                            <th>Status</th>
                                                            <th>Jam Pulang</th>
                                                            <th>Durasi Lembur</th>
                                                            <th>
                                                                <input type="checkbox" onchange="checkAll(this)" name="chk[]" >
                                                                Approve
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php 
                                                        $db = \Config\Database::connect();
                                                        $no=0;
                                                        $tgl = date('Y-m-d');
                                                        foreach ($getAbsensi as $data) {  
                                                        $no++;
                                                        $induk =$data['nomor_absensi'];
                                                        $id =$data['id_ptk'];
                                                        $jam_pulang = jampulang($id,$getTanggal);
                                                        $date = date_create($data['jam_pulang']);
                                                        date_add($date, date_interval_create_from_date_string('+'.$getBatasjam.' hours'));
                                                        $jumlah_time= date_format($date, 'H:i:s');
                                                        
                                                        $diff    =strtotime($jam_pulang) - strtotime($jumlah_time);
                                                        $jam    =floor($diff / (60 * 60));
                                                        $menit    =$diff - $jam * (60 * 60);
                                                        $lembur = $jam.':'.floor( $menit / 60 ).':00';

                                                        //cek apakah sudah ada data lembur
                                                        $builder = $db->table('t_lembur');
                                                        $builder->where('id_ptk', $id);
                                                        $builder->where('tgl_lembur', $getTanggal);
                                                        $all =  $builder->countAllResults();
                                                        if($lembur>0 AND $all==0){
                                                        ?>
                                                        <tr>
                                                            <td><?= $no ?></td>
                                                            <td><?= $data['nama_ptk'] ?></td>
                                                            <td><?= $data['nama_jenis_ptk'] ?></td>
                                                            <td><?= jammasuk($id,$getTanggal) ?></td>
                                                            <td><?= sts_absen($id,$getTanggal) ?></td>
                                                            <td><?= $jam_pulang ?></td>
                                                            <td><?= $jam ?> jam, <?= floor( $menit / 60 )  ?> menit</td>
                                                            <td align="center">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" name="approve[]" value="<?=$id;?>" type="checkbox">
                                                                </div>
                                                            </td>
                                                        </tr>    
                                                       <?php 
                                                        }
                                                        } ?>

                                                    </tbody>
                                                    <footer>
                                                        <tr>
                                                            <input name="tgl" value="<?=$getTanggal;?>" type="hidden">
                                                            <td colspan="8" align="center"><button type="submit" class="btn btn-info">Approve</button></td>
                                                        </tr>
                                                    </footer>
                                                </table>
                                            </div>
                                        </div>
                                        </form>
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
