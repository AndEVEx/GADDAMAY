<?php 
$id = $_GET['id'];
$bln = $_GET['bln'];
 //ambil nama pegawai
 $db = \Config\Database::connect();
 $query = $db->query("SELECT nama_ptk,alamat,no_hp,nama_jenis_ptk FROM t_ptk join r_jenis_ptk ON r_jenis_ptk.id_jenis_ptk = t_ptk.id_jenis_ptk where id_ptk='$id'");
 $row = $query->getRow();
 $nm = $row->nama_ptk;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Form Online</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' media='screen' href='main.css'>
    <script src='main.js'></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body onload="window.print();">

<div class="card-body table-border-style">
    <div class="container">
        <div class="row d-flex justify-content-center">
            <div class="col-10">
                <div class="form-group row d-flex justify-content-center">
                    <h3><u>SLIP GAJI KARYAWAN</u></h3>
                </div>                                                             
            </div>
            <br><br>
            <div class="col-10">
                    <h6>Universitas Sunan Giri Surabaya</h6>
                    Jl. Brigjen Katamso II, Bandilan, Kedungrejo, <br>
                    Kec. Waru, Kabupaten Sidoarjo, Jawa Timur 61256
                    <hr>                                             
            </div>
            <div class="col-10">
                <table width="100%">
                    <tr>
                        <td width="10%">Nama</td>
                        <td width="3%">:</td>
                        <td width="30%"><?=$getNama;?></td>
                        <td width="2%"></td>
                        <td width="10%">Alamat</td>
                        <td width="3%">:</td>
                        <td width="42%"><?=$row->alamat;?></td>
                    </tr>
                    <tr>
                        <td>Jabatan</td>
                        <td>:</td>
                        <td><?=$row->nama_jenis_ptk;?></td>
                        <td></td>
                        <td>Telp</td>
                        <td>:</td>
                        <td><?=$row->no_hp;?></td>
                    </tr>
                    <tr>
                        <td>Bulan</td>
                        <td>:</td>
                        <td><?=bulan($bln);?></td>
                        <td></td>
                        <td>Tgl Cetak</td>
                        <td>:</td>
                        <td><?=formatTanggal(date('Y-m-d'));?></td>
                    </tr>
                </table>  
                <hr>                                         
            </div>
            <div class="col-10">
            <table width="100%" class="table-primary">
                    <tr>
                        <td width="20%" align="center"><b>Keterangan</b></td>
                        <td width="80%" align="center"><b>Jumlah</b></td>
                    </tr>
                </table>                                                   
            </div>
            <br><br>
            <?php foreach ($getKatgaji as $data1) { 
            $id_kat = $data1['id_kat_gaji']; 
            $tot_gaji[] = gajidetail($getId,$getBulan,$getTahun,$id_kat);
            ?>
                <div class="col-10">
                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-4 col-form-label"><?= $data1['nm_kat_gaji'] ?></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" value="<?= rupiah(gajidetail($getId,$getBulan,$getTahun,$id_kat));?>">
                        </div>
                    </div>
                                                                            
                </div>
            <?php } 
            $total = array_sum($tot_gaji);
            ?>
                <div class="col-10">
                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-4 col-form-label"><b>TOTAL GAJI</b></label>
                        <div class="col-sm-8">
                           <button type="button" class="btn btn-info"><?=rupiah($total);?></button><br>
                           (<?=terbilang($total) ?>)
                        </div>
                        
                    </div>                                                      
                </div>
                <br>
                <br>
                <br>
                <br>
                <div class="col-10">
                    Mengetahui<br><br><br>
                    Bendahara<br>
                    Prayogi Academy
                                                
                </div>
        </div>
        
    </div>
</div>

</body>
</html>