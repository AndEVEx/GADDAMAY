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
                                                <li class="breadcrumb-item"><a href="#!"> Master</a></li>
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
                                                <div class="col-10">
                                                    <h5>Tabel data Setting Kelas Siswa</h5>
                                                </div>
                                                <div class="col-2 text-right">
                                                    <button class="btn btn-warning btn-sm" type="button" data-toggle="modal" data-target="#promoteModal"><i class="feather icon-arrow-up"></i> Naik Kelas</button>
                                                </div>
                                            </div>
                                            
                                       
                                        </div>
                                        <div class="card-body table-border-style">
                                            <div class="table-responsive">
                                                <table class="table table-striped table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Kelas</th>
                                                            <th>Jumlah Siswa</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php 
                                                        $no=0;
                                                        foreach ($getRombel as $data) {  
                                                            $id = $data['id_rombel'];
                                                        $no++;
                                                        ?>
                                                        <tr>
                                                            <td><?= $no ?></td>
                                                            <td><?= $data['nm_rombel'] ?></td>
                                                            <td>
                                                                <?php 
                                                                $db = \Config\Database::connect();
                                                                $builder = $db->table('t_siswa_rombel');
                                                                $builder->where('id_rombel', $id);
                                                                $all =  $builder->countAllResults();
                                                                ?>
                                                                <form method="post" action="<?= base_url('Siswarombel/detail'); ?>">
                                                                <input type="hidden" class="form-control" name="id" value="<?=$id;?>" required>
                                                                
                                                                <button type="submit" class="btn  btn-icon btn-info"><?=$all;?></button>
                                                                </form>
                                                            </td>
                                                            <td>
                                                              
                                                                <button class="btn btn-danger btn-sm" type="submit" data-toggle="modal" data-target="#delete<?=$id;?>"><i class="feather icon-trash"></i></button>
                                                            
                                                               
                                                                <!-- delete The Modal -->
                                                                <div class="modal fade" id="delete<?=$id?>">
                                                                    <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                                
                                                                    <!-- Modal Header -->
                                                                    <div class="modal-header">
                                                                    <h4 class="modal-title">Hapus Data Siswa</h4>
                                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                    </div>
                                                                                    
                                                                    <!-- Modal body -->
                                                                    <form method="post" action="<?= base_url('Siswarombel/hapus'); ?>">
                                                                        <div class="modal-body">
                                                                            Apakah anda yakin ingin menghapus Data siswa di <?=$data['nm_rombel'];?>? <br>
                                                                           
                                                                            <input type="hidden" name="id" value="<?=$id;?>">
                                                                            <br>
                                                                            <br>
                                                                            <button type="submit" class="btn btn-danger">Delete</button>
                                                                        </div>
                                                                    </form>
                                                                    </div>
                                                                </div>
                                                                </div>

                                                            </td>
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
                           
                            <div class="row">
                                <!-- [ static-layout ] start -->
                                <div class="col-sm-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-10">
                                                <h5>Data Siswa Yang Belum Masuk Rombel</h5>
                                                </div>
                                               
                                            </div>
                                            
                                       
                                        </div>
                                        <div class="card-body table-border-style">
                                            <div class="table-responsive">
                                            <form method="post" action="<?= base_url('Siswarombel/addrombel'); ?>">
                                                <table class="table table-striped table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>No Induk</th>
                                                            <th>Nama Siswa</th>
                                                            <th>Jenis Kelamin</th>
                                                            <th>Pilih Rombel</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php 
                                                        $no1=0;
                                                        $db = \Config\Database::connect();
                                                        $subQuery = $db->table('t_siswa_rombel')->select('id_siswa')->where('id_tapel', $getTapel);
                                                        $builder = $db->table('t_siswa');
                                                        $builder->whereNotIn('id_siswa', $subQuery);
                                                        $query = $builder->get();
                                                        foreach ($query->getResult() as $row) {
                                                            $id = $row->id_siswa;
                                                            $no1++;
                                                        ?>
                                                        <tr>
                                                            <td><?= $no1 ?></td>
                                                            <td><?= $row->no_induk ?></td>
                                                            <td><?= $row->nm_siswa ?></td>
                                                            <td><?= jk($row->jk) ?></td>
                                                            <td>
                                                                <select class="form-control" name="id_rombel[]">
                                                                    <option>Pilih</option>
                                                                    <?php foreach ($getRombel as $data) { ?>
                                                                    <option value="<?=$data['id_rombel'];?>"><?=$data['nm_rombel'];?></option>
                                                                    <?php } ?>
                                                                </select>
                                                                <input type="hidden" class="form-control" name="id_siswa[]" value="<?=$id;?>" required>
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

<!-- Promote Class Modal -->
<div class="modal fade" id="promoteModal">
    <div class="modal-dialog">
        <div class="modal-content">
                    
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Kenaikan Kelas</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
                        
            <!-- Modal body -->
            <form class="was-validated" method="post" action="<?= base_url('Siswarombel/promote'); ?>">
                <div class="modal-body">
                    <div class="alert alert-info">
                        Fitur ini menyalin semua siswa aktif dari Kelas Asal (Tahun Pelajaran Sebelumnya) ke Kelas Tujuan (Tahun Pelajaran Saat Ini).
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Tahun Pelajaran Asal</label>
                                <select class="form-control" name="source_tapel" id="source_tapel" required>
                                    <option selected disabled value="">Pilih</option>
                                    <?php 
                                    $db = \Config\Database::connect();
                                    $tapels = $db->table('r_tapel')->orderBy('nm_tapel', 'DESC')->get()->getResultArray();
                                    foreach ($tapels as $t) { ?>
                                    <option value="<?=$t['id_tapel'] ?>"><?=$t['nm_tapel'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Rombel Asal</label>
                                <select class="form-control" name="source_rombel" id="source_rombel" required>
                                    <option selected disabled value="">Pilih Tahun Pelajaran Terlebih Dahulu</option>
                                </select>
                            </div>
                        </div>
                        </div>
                    </div>
                     
                    <button type="submit" class="btn btn-warning btn-block">Proses Kenaikan Kelas</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script>
    $(document).ready(function() {
        $('#source_tapel').on('change', function() {
            var id_tapel = $(this).val();
            if (id_tapel) {
                $.ajax({
                    url: '<?= base_url('Siswarombel/getRombelsByTapel'); ?>/' + id_tapel,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        var $select = $('#source_rombel');
                        $select.empty();
                        $select.append('<option selected disabled value="">Pilih Rombel Asal</option>');
                        $select.append('<option value="all">Semua Kelas</option>');
                        $.each(data, function(key, val) {
                            $select.append('<option value="' + val.id_rombel + '">' + val.nm_rombel + '</option>');
                        });
                    }
                });
            }
        });
    });
</script>
