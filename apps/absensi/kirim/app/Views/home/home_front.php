
<!-- [ Main Content ] start -->
    <div class="pcoded-main-container">
        <div class="pcoded-wrapper container">
            <div class="pcoded-content">
                <div class="pcoded-inner-content">
                    <div class="main-body">
                        <div class="page-wrapper">
                            <div class="page-header">
                                                
                            </div>
                            <div class="row d-flex justify-content-center">
                                <div class="col-sm-6">
                                        <div class="card-body">
                                            <div id="jam"></div>
                                        </div>  
                                </div>
                            </div>
                            <br><br>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>ABSENSI TERKIRIM TANGGAL <?=formatTanggal(date('Y-m-d'));?></h5>
                                        </div>
                                        <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Nama Karyawan/Dosen</th>
                                                        <th>Jam Absen</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    $no=0;
                                                    foreach ($getAbsensi as $data) { 
                                                    $no++;
                                                    $id = $data['id_ptk'];
                                                    ?>
                                                    <tr>
                                                        <td><?= $no ?></td>
                                                        <td><?= $data['nama_ptk'] ?></td>
                                                        <td><?= $data['JAM'] ?></td>
                                                        <td><?= sts_absen($id,date('Y-m-d')) ?></td>        
                                                    </tr>
                                                    <?php } ?>   
                                                </tbody>
                                            </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- [ horizontal-layout ] end -->
                          
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>POINT 5 KARYAWAN TERBAIK BULAN <?=strtoupper(bulan(date('m')));?></h5>
                                        </div>
                                        <div class="card-body">
                                        <div class="table-responsive">
                                                        <table class="table table-hover mb-0">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Nama Karyawan</th>
                                                                    <th>Jumlah Point</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                            <?php 
                                                            $no=0;
                                                            foreach ($getPointkaryawan as $data) {  
                                                            $no++;
                                                            ?>
                                                                <tr>
                                                                    <td><?= $no ?></td>
                                                                    <td><?= $data['nama_ptk'] ?></td>
                                                                    <td><?= $data['point'] ?></td>
                                                                </tr>
                                                            <?php } ?> 
                                                            </tbody>
                                                        </table>
                                                    </div>
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
    <script type="text/javascript">
        setTimeout(function(){
            location.reload();
        },5000);
    </script>